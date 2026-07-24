<?php

namespace Modules\SupportTicket\Http\Controllers;

use App\PurchaseLine;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\SupportTicket\Entities\SupportTicket;
use Modules\SupportTicket\Entities\SupportTicketClosureReason;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class SupportTicketController extends Controller
{
    protected $productUtil;

    protected $transactionUtil;

    protected $businessUtil;

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * "My Support Tickets" - every store account, scoped to the user's
     * permitted locations, same mechanism as the Damage/Loss Report.
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('support_ticket.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            SupportTicket::flagOverdueAsDelayed($business_id);

            $query = SupportTicket::where('business_id', $business_id)
                ->with(['location', 'closure_reason', 'purchase_line.product']);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }

            return $this->ticketDatatable($query);
        }

        return view('supportticket::support_ticket.index');
    }

    /**
     * Consolidated dashboard across every account - admin / ticket-manager only.
     */
    public function dashboard(Request $request)
    {
        if (! auth()->user()->can('support_ticket.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            SupportTicket::flagOverdueAsDelayed($business_id);

            $query = SupportTicket::where('business_id', $business_id)
                ->with(['location', 'closure_reason', 'purchase_line.product', 'transaction', 'purchase_order'])
                // Delayed tickets need immediate attention - always surface them
                // first, regardless of whatever column the user last sorted by.
                ->orderByRaw("FIELD(status, 'delayed', 'open', 'closed')")
                ->orderBy('tat_due_at', 'asc');

            return $this->ticketDatatable($query, true);
        }

        return view('supportticket::support_ticket.dashboard');
    }

    private function ticketDatatable($query, $is_dashboard = false)
    {
        $datatable = DataTables::of($query)
            ->editColumn('ticket_type', function ($row) {
                return SupportTicket::ticketTypeLabels()[$row->ticket_type] ?? $row->ticket_type;
            })
            ->editColumn('status', function ($row) {
                $badges = ['open' => 'bg-yellow', 'delayed' => 'bg-red', 'closed' => 'bg-green'];
                $badge = $badges[$row->status] ?? 'bg-gray';
                $label = SupportTicket::statusLabels()[$row->status] ?? ucfirst($row->status);

                return '<span class="label '.$badge.'">'.$label.'</span>';
            })
            ->addColumn('location_name', function ($row) {
                return $row->location->name ?? '';
            })
            ->addColumn('product_name', function ($row) {
                return $row->purchase_line->product->name ?? '';
            })
            ->addColumn('action', function ($row) {
                $html = '<a href="#" data-href="'.action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'show'], [$row->id])
                    .'" data-container=".view_modal" class="btn-modal btn btn-xs btn-info"><i class="fa fa-eye"></i> '.__('messages.view').'</a>';

                if ($row->status != 'closed' && auth()->user()->can('support_ticket.add_log')) {
                    $html .= ' <a href="#" data-href="'.action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'addLogForm'], [$row->id])
                        .'" data-container=".view_modal" class="btn-modal btn btn-xs btn-primary"><i class="fa fa-comment"></i> '.__('lang_v1.add_progress_log').'</a>';
                }

                return $html;
            })
            // DataTables' reserved DT_RowClass key - applied to the <tr> automatically,
            // no client-side JS needed to paint delayed tickets red.
            ->addColumn('DT_RowClass', function ($row) {
                return $row->status == 'delayed' ? 'support-ticket-delayed-row' : '';
            });

        $raw_columns = ['status', 'action'];

        if ($is_dashboard) {
            $datatable->addColumn('grn_no', function ($row) {
                if (empty($row->transaction)) {
                    return '--';
                }

                return '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                    .'" data-container=".view_modal" class="btn-modal">'.$row->transaction->ref_no.'</a>';
            });

            $datatable->addColumn('po_no', function ($row) {
                if (empty($row->purchase_order)) {
                    return '--';
                }

                return '<a href="#" data-href="'.action([\App\Http\Controllers\PurchaseOrderController::class, 'show'], [$row->purchase_order_id])
                    .'" data-container=".view_modal" class="btn-modal">'.$row->purchase_order->ref_no.'</a>';
            });

            $raw_columns[] = 'grn_no';
            $raw_columns[] = 'po_no';
        }

        return $datatable->rawColumns($raw_columns)->make(true);
    }

    /**
     * Modal form: raise a ticket against a Damage/Loss Report row.
     */
    public function create($purchase_line_id)
    {
        if (! auth()->user()->can('support_ticket.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $purchase_line = PurchaseLine::whereHas('transaction', function ($q) use ($business_id) {
            $q->where('business_id', $business_id)->where('type', 'purchase');
        })
            ->with(['product', 'variations', 'transaction', 'purchase_order_line.transaction'])
            ->findOrFail($purchase_line_id);

        if (SupportTicket::where('purchase_line_id', $purchase_line_id)->exists()) {
            abort(422, __('lang_v1.ticket_already_raised_for_this_entry'));
        }

        return view('supportticket::support_ticket.create', compact('purchase_line'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('support_ticket.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = $request->session()->get('user.business_id');
            $purchase_line = PurchaseLine::whereHas('transaction', function ($q) use ($business_id) {
                $q->where('business_id', $business_id)->where('type', 'purchase');
            })->findOrFail($request->input('purchase_line_id'));

            if (SupportTicket::where('purchase_line_id', $purchase_line->id)->exists()) {
                throw new \Exception(__('lang_v1.ticket_already_raised_for_this_entry'));
            }

            $transaction = $purchase_line->transaction;
            $po_line = $purchase_line->purchase_order_line;

            $ticket_type = 'mixed';
            if ($purchase_line->quantity_damaged > 0 && $purchase_line->quantity_lost <= 0) {
                $ticket_type = 'in_transit_damage';
            } elseif ($purchase_line->quantity_lost > 0 && $purchase_line->quantity_damaged <= 0) {
                $ticket_type = 'loss_short';
            }

            $ticket = new SupportTicket();
            $ticket->business_id = $business_id;
            $ticket->ticket_number = SupportTicket::generateTicketNumber($business_id);
            $ticket->location_id = $transaction->location_id;
            $ticket->purchase_line_id = $purchase_line->id;
            $ticket->transaction_id = $transaction->id;
            $ticket->purchase_order_line_id = $po_line->id ?? null;
            $ticket->purchase_order_id = $po_line->transaction_id ?? null;
            $ticket->ticket_type = $ticket_type;
            $ticket->quantity_damaged = $purchase_line->quantity_damaged;
            $ticket->quantity_lost = $purchase_line->quantity_lost;
            $ticket->damage_loss_reason = $purchase_line->damage_loss_reason;
            $ticket->damage_loss_note = $purchase_line->damage_loss_note;
            $ticket->status = 'open';
            $ticket->raised_by = auth()->user()->id;

            // Snapshot the TAT deadline at raise time, from whatever the business
            // has configured right now - a later change to the setting doesn't
            // retroactively move the deadline for tickets already in flight.
            $tat_hours = \App\Business::where('id', $business_id)->value('support_ticket_tat_hours') ?: 48;
            $ticket->tat_due_at = now()->addHours($tat_hours);

            $ticket->save();

            $this->transactionUtil->activityLog($ticket, 'ticket_raised', null, [
                'ticket_number' => $ticket->ticket_number,
                'quantity_damaged' => $ticket->quantity_damaged,
                'quantity_lost' => $ticket->quantity_lost,
                'tat_due_at' => $ticket->tat_due_at->toDateTimeString(),
            ]);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.ticket_raised_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage() ?: trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * View modal: full ticket detail + damage/loss info + activity log.
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $ticket = SupportTicket::where('business_id', $business_id)
            ->with([
                'location',
                'purchase_line.product',
                'purchase_line.variations',
                'transaction',
                'purchase_order',
                'closure_reason',
                'raised_by_user',
                'closed_by_user',
            ])
            ->findOrFail($id);

        if (! auth()->user()->can('support_ticket.view_all')) {
            if (! auth()->user()->can('support_ticket.view_own')) {
                abort(403, 'Unauthorized action.');
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all' && ! in_array($ticket->location_id, $permitted_locations)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $closure_reasons = SupportTicketClosureReason::forBusiness($business_id)->active()->get();

        $activities = Activity::forSubject($ticket)
            ->with(['causer', 'subject'])
            ->latest()
            ->get();

        $can_manage = ! $ticket->isClosed() && auth()->user()->can('support_ticket.manage');
        $can_add_log = ! $ticket->isClosed() && auth()->user()->can('support_ticket.add_log');

        return view('supportticket::support_ticket.show', compact('ticket', 'closure_reasons', 'activities', 'can_manage', 'can_add_log'));
    }

    /**
     * Standalone "Add Progress Log" modal, reachable directly from the
     * action column - distinct from opening the full ticket detail.
     */
    public function addLogForm($id)
    {
        if (! auth()->user()->can('support_ticket.add_log')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ticket = SupportTicket::where('business_id', $business_id)->findOrFail($id);

        if ($ticket->isClosed()) {
            abort(422, __('lang_v1.ticket_closed_no_more_logs'));
        }

        return view('supportticket::support_ticket.add_log_modal', compact('ticket'));
    }

    /**
     * Add a progress log entry to a ticket - a running commentary distinct
     * from closing it, visible in the same activity trail. Blocked once the
     * ticket is closed - closing is final for the log timeline too.
     */
    public function addLog(Request $request, $id)
    {
        if (! auth()->user()->can('support_ticket.add_log')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'log_note' => 'required|string|max:2000',
            ]);

            $business_id = $request->session()->get('user.business_id');
            $ticket = SupportTicket::where('business_id', $business_id)->findOrFail($id);

            if ($ticket->isClosed()) {
                throw new \Exception(__('lang_v1.ticket_closed_no_more_logs'));
            }

            $this->transactionUtil->activityLog($ticket, 'progress_log_added', null, [
                'update_note' => $request->input('log_note'),
            ]);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.progress_log_added_successfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage() ?: trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Close a ticket: "no resend" finalizes the PO as-is; "resend" reopens
     * exactly the damaged/lost quantity on the PO line for one more GRN.
     */
    public function close(Request $request, $id)
    {
        if (! auth()->user()->can('support_ticket.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = $request->session()->get('user.business_id');
            $ticket = SupportTicket::where('business_id', $business_id)->findOrFail($id);

            if ($ticket->isClosed()) {
                throw new \Exception(__('lang_v1.ticket_already_closed'));
            }

            $closure_reason = SupportTicketClosureReason::forBusiness($business_id)
                ->active()
                ->findOrFail($request->input('closure_reason_id'));

            $ticket_before = $ticket->replicate();

            $ticket->closure_reason_id = $closure_reason->id;
            $ticket->closure_note = $request->input('closure_note');
            $ticket->status = 'closed';
            $ticket->closed_by = auth()->user()->id;
            $ticket->closed_at = now();
            $ticket->save();

            if ($closure_reason->requires_resend && ! empty($ticket->purchase_order_line_id)) {
                // Pull the damaged/lost quantity back out of "accounted for" on the
                // PO line - this is the only thing that reopens room for a GRN, and
                // it reopens exactly this much, nothing more.
                $this->productUtil->updatePurchaseOrderLine(
                    $ticket->purchase_order_line_id,
                    0,
                    0,
                    0,
                    $ticket->quantity_damaged,
                    0,
                    $ticket->quantity_lost
                );

                // Trace the PO line back to this ticket so the resend GRN is identifiable.
                PurchaseLine::where('id', $ticket->purchase_order_line_id)->update(['support_ticket_id' => $ticket->id]);
            }

            if (! empty($ticket->purchase_order_id)) {
                $this->transactionUtil->updatePurchaseOrderStatus([$ticket->purchase_order_id]);
            }

            $this->transactionUtil->activityLog($ticket, 'ticket_closed', $ticket_before, [
                'closure_reason' => $closure_reason->label,
                'requires_resend' => $closure_reason->requires_resend,
                'closure_note' => $ticket->closure_note,
            ]);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.ticket_closed_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage() ?: trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}
