<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SuperadminCustomerController extends BaseController
{
    /**
     * Display a chain-wide list of customers. Includes both
     * "universal" (global) customers and store-specific ones,
     * with the originating store highlighted.
     *
     * @return Response
     */
    public function index()
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $businesses = Business::orderBy('name')->pluck('name', 'id');

        if (request()->ajax()) {
            $query = Contact::leftjoin('business AS src_biz', 'contacts.source_business_id', '=', 'src_biz.id')
                ->leftjoin('business AS owner_biz', 'contacts.business_id', '=', 'owner_biz.id')
                ->whereIn('contacts.type', ['customer', 'both'])
                ->select(
                    'contacts.*',
                    'src_biz.name as source_business_name',
                    'owner_biz.name as owner_business_name',
                );

            if (! is_null(request()->input('is_global_filter'))) {
                $query->where('contacts.is_global', request()->input('is_global_filter'));
            }

            if (! empty(request()->input('business_id'))) {
                $query->where('contacts.business_id', request()->input('business_id'));
            }

            if (! empty(request()->input('search_text'))) {
                $term = request()->input('search_text');
                $query->where(function ($q) use ($term) {
                    $q->where('contacts.name', 'like', "%{$term}%")
                        ->orWhere('contacts.mobile', 'like', "%{$term}%")
                        ->orWhere('contacts.email', 'like', "%{$term}%")
                        ->orWhere('contacts.contact_id', 'like', "%{$term}%");
                });
            }

            return Datatables::of($query)
                ->addColumn('is_global_label', function ($row) {
                    if ($row->is_global) {
                        return '<span class="label label-success"><i class="fa fa-globe"></i> '.__('superadmin::lang.universally_shared').'</span>';
                    }

                    return '<span class="label label-default"><i class="fa fa-home"></i> '.__('superadmin::lang.store_specific').'</span>';
                })
                ->addColumn('source_business', function ($row) {
                    if (! empty($row->source_business_name)) {
                        return e($row->source_business_name);
                    }

                    return ! empty($row->owner_business_name) ? e($row->owner_business_name) : '-';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">'
                        .'<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info tw-w-max dropdown-toggle" '
                        .'data-toggle="dropdown" aria-expanded="false">'
                        .__('messages.actions')
                        .'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>'
                        .'</button>'
                        .'<ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li>'
                        .'<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]).'" target="_blank">'
                        .'<i class="fas fa-eye"></i> '.__('messages.view')
                        .'</a></li>';

                    $html .= '<li>'
                        .'<a data-href="'.action([\App\Http\Controllers\ContactController::class, 'edit'], [$row->id]).'" class="edit_contact_button">'
                        .'<i class="glyphicon glyphicon-edit"></i> '.__('messages.edit')
                        .'</a></li>';

                    if ($row->is_global) {
                        $html .= '<li>'
                            .'<a href="'.action([\Modules\Superadmin\Http\Controllers\SuperadminCustomerController::class, 'toggleGlobal'], [$row->id, 0]).'" '
                            .'class="toggle-global-customer" data-target_id="'.$row->id.'" data-target_global="0">'
                            .'<i class="fa fa-home"></i> '.__('superadmin::lang.demote_to_business')
                            .'</a></li>';
                    } else {
                        $html .= '<li>'
                            .'<a href="'.action([\Modules\Superadmin\Http\Controllers\SuperadminCustomerController::class, 'toggleGlobal'], [$row->id, 1]).'" '
                            .'class="toggle-global-customer" data-target_id="'.$row->id.'" data-target_global="1">'
                            .'<i class="fa fa-globe"></i> '.__('superadmin::lang.promote_to_global')
                            .'</a></li>';
                    }

                    if (! $row->is_default) {
                        $html .= '<li>'
                            .'<a href="'.action([\App\Http\Controllers\ContactController::class, 'destroy'], [$row->id]).'" '
                            .'class="delete_contact_button">'
                            .'<i class="glyphicon glyphicon-trash"></i> '.__('messages.delete')
                            .'</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                })
                ->editColumn('name', function ($row) {
                    $name = e($row->name);

                    return $name;
                })
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->rawColumns(['is_global_label', 'action'])
                ->make(true);
        }

        return view('superadmin::customers.index')->with(compact('businesses'));
    }

    /**
     * Promote / demote a customer to/from global (chain-wide).
     *
     * @param  int  $id
     * @param  int  $global  1 to promote, 0 to demote
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleGlobal($id, $global)
    {
        if (! auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $contact = Contact::findOrFail($id);
            $contact->is_global = (int) $global;
            if ($global && empty($contact->source_business_id)) {
                $contact->source_business_id = $contact->business_id;
            }
            $contact->save();

            $msg = $global
                ? __('superadmin::lang.customer_promoted')
                : __('superadmin::lang.customer_demoted');

            $output = ['success' => true, 'msg' => $msg];
        } catch (\Exception $e) {
            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return $output;
        }

        return redirect()
            ->action([\Modules\Superadmin\Http\Controllers\SuperadminCustomerController::class, 'index'])
            ->with('status', $output);
    }
}
