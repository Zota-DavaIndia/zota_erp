<div class="modal-dialog" role="document">
    {!! Form::open(['url' => action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'store']), 'method' => 'post', 'id' => 'support_ticket_create_form']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('lang_v1.raise_support_ticket')</h4>
        </div>
        <div class="modal-body">
            {!! Form::hidden('purchase_line_id', $purchase_line->id) !!}

            <table class="table table-condensed">
                <tr>
                    <th>@lang('sale.product')</th>
                    <td>
                        {{ $purchase_line->product->name }}
                        @if(!empty($purchase_line->variations->name) && $purchase_line->variations->name != 'DUMMY')
                            ({{ $purchase_line->variations->name }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>@lang('purchase.grn_no')</th>
                    <td>{{ $purchase_line->transaction->ref_no }}</td>
                </tr>
                @if(!empty($purchase_line->purchase_order_line))
                <tr>
                    <th>@lang('lang_v1.purchase_order')</th>
                    <td>{{ $purchase_line->purchase_order_line->transaction->ref_no }}</td>
                </tr>
                @endif
                <tr>
                    <th>@lang('purchase.quantity_damaged')</th>
                    <td>{{ @format_quantity($purchase_line->quantity_damaged) }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.quantity_lost')</th>
                    <td>{{ @format_quantity($purchase_line->quantity_lost) }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.damage_loss_reason')</th>
                    <td>{{ !empty($purchase_line->damage_loss_reason) ? __('lang_v1.'.$purchase_line->damage_loss_reason) : '--' }}</td>
                </tr>
                <tr>
                    <th>@lang('purchase.damage_loss_note')</th>
                    <td>{{ $purchase_line->damage_loss_note ?? '--' }}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                @lang('lang_v1.raise_support_ticket')
            </button>
        </div>
    </div><!-- /.modal-content -->
    {!! Form::close() !!}
</div><!-- /.modal-dialog -->
