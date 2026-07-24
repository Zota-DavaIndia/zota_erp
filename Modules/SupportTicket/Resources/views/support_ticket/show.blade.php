<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                {{ $ticket->ticket_number }}
                @if($ticket->status == 'open')
                    <span class="label bg-yellow">@lang('lang_v1.open')</span>
                @elseif($ticket->status == 'delayed')
                    <span class="label bg-red">@lang('lang_v1.delayed')</span>
                @else
                    <span class="label bg-green">@lang('lang_v1.closed')</span>
                @endif
                @if(!empty($ticket->tat_due_at))
                    <small class="text-muted">
                        (@lang('lang_v1.tat_due_by'): {{ @format_datetime($ticket->tat_due_at) }})
                    </small>
                @endif
            </h4>
        </div>
        <div class="modal-body">
            <table class="table table-condensed">
                <tr>
                    <th>@lang('sale.product')</th>
                    <td>
                        {{ $ticket->purchase_line->product->name ?? '' }}
                        @if(!empty($ticket->purchase_line->variations->name) && $ticket->purchase_line->variations->name != 'DUMMY')
                            ({{ $ticket->purchase_line->variations->name }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>@lang('purchase.location')</th>
                    <td>{{ $ticket->location->name ?? '' }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.grn_no')</th>
                    <td>{{ $ticket->transaction->ref_no ?? '' }}</td>
                </tr>
                @if(!empty($ticket->purchase_order))
                <tr>
                    <th>@lang('lang_v1.purchase_order')</th>
                    <td>{{ $ticket->purchase_order->ref_no }} ({{ ucfirst($ticket->purchase_order->status) }})</td>
                </tr>
                @endif
                <tr>
                    <th>@lang('lang_v1.ticket_type')</th>
                    <td>{{ \Modules\SupportTicket\Entities\SupportTicket::ticketTypeLabels()[$ticket->ticket_type] ?? $ticket->ticket_type }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.quantity_damaged')</th>
                    <td>{{ @format_quantity($ticket->quantity_damaged) }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.quantity_lost')</th>
                    <td>{{ @format_quantity($ticket->quantity_lost) }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.damage_loss_reason')</th>
                    <td>{{ !empty($ticket->damage_loss_reason) ? __('lang_v1.'.$ticket->damage_loss_reason) : '--' }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.damage_loss_note')</th>
                    <td>{{ $ticket->damage_loss_note ?? '--' }}</td>
                </tr>
                <tr>
                    <th>@lang('lang_v1.ticket_raised_by')</th>
                    <td>{{ $ticket->raised_by_user->user_full_name ?? '' }} ({{ @format_datetime($ticket->created_at) }})</td>
                </tr>
                @if($ticket->status == 'closed')
                <tr>
                    <th>@lang('lang_v1.closure_reason')</th>
                    <td>{{ $ticket->closure_reason->label ?? '' }}</td>
                </tr>
                @if(!empty($ticket->closure_note))
                <tr>
                    <th>@lang('lang_v1.closure_note')</th>
                    <td>{{ $ticket->closure_note }}</td>
                </tr>
                @endif
                <tr>
                    <th>@lang('lang_v1.closed')</th>
                    <td>{{ $ticket->closed_by_user->user_full_name ?? '' }} ({{ @format_datetime($ticket->closed_at) }})</td>
                </tr>
                @endif
            </table>

            @if($can_add_log)
            <hr>
            <form action="{{ action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'addLog'], [$ticket->id]) }}"
                method="post" id="support_ticket_log_form" data-show-url="{{ action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'show'], [$ticket->id]) }}">
                @csrf
                <h4>@lang('lang_v1.add_progress_log')</h4>
                <div class="form-group">
                    <textarea name="log_note" class="form-control" rows="2" required placeholder="@lang('lang_v1.add_progress_log_placeholder')"></textarea>
                </div>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                    @lang('lang_v1.add_progress_log')
                </button>
            </form>
            @endif

            @if($can_manage)
            <hr>
            {!! Form::open(['url' => action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'close'], [$ticket->id]), 'method' => 'post', 'id' => 'support_ticket_close_form']) !!}
                <h4>@lang('lang_v1.close_ticket')</h4>
                <div class="form-group">
                    {!! Form::label('closure_reason_id', __('lang_v1.closure_reason') . ':') !!}
                    <select name="closure_reason_id" id="ticket_closure_reason_id" class="form-control" required>
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($closure_reasons as $reason)
                            <option value="{{ $reason->id }}" data-requires-resend="{{ $reason->requires_resend ? 1 : 0 }}">
                                {{ $reason->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <p class="help-block" id="ticket_closure_reason_help"></p>
                </div>
                <div class="form-group">
                    {!! Form::label('closure_note', __('lang_v1.closure_note') . ':') !!}
                    {!! Form::textarea('closure_note', null, ['class' => 'form-control', 'rows' => 2]) !!}
                </div>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                    @lang('lang_v1.close_ticket')
                </button>
            {!! Form::close() !!}
            @endif

            <hr>
            <strong>@lang('lang_v1.activities'):</strong><br>
            @includeIf('activity_log.activities')
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

@if($can_manage)
<script type="text/javascript">
    $(document).on('change', '#ticket_closure_reason_id', function() {
        var requires_resend = $(this).find(':selected').data('requires-resend');
        if (requires_resend == 1) {
            $('#ticket_closure_reason_help').text('@lang('lang_v1.resend_closure_help')');
        } else if ($(this).val()) {
            $('#ticket_closure_reason_help').text('@lang('lang_v1.not_resend_closure_help')');
        } else {
            $('#ticket_closure_reason_help').text('');
        }
    });
</script>
@endif
