<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                @lang('lang_v1.purchase_history_of'): {{ $contact->name }}
                @if(!empty($contact->mobile))
                    <small class="text-muted">({{ $contact->mobile }})</small>
                @endif
            </h4>
            <p class="text-muted" style="margin-bottom:0;">
                <i class="fa fa-info-circle"></i> @lang('lang_v1.purchase_history_chain_wide_note')
            </p>
        </div>
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            @if($transactions->isEmpty())
                <div class="text-center text-muted" style="padding: 30px 0;">
                    <i class="fa fa-shopping-cart fa-2x"></i>
                    <p>@lang('lang_v1.no_purchase_history_found')</p>
                </div>
            @else
                @foreach($transactions as $transaction)
                    <div class="box box-solid box-default" style="margin-bottom: 15px;">
                        <div class="box-header with-border">
                            <h5 style="margin: 0;">
                                <span class="label label-primary">
                                    <i class="fa fa-bank"></i> {{ $transaction->business->name ?? '-' }}
                                </span>
                                &nbsp;
                                <strong>{{ $transaction->invoice_no }}</strong>
                                &nbsp;
                                <span class="text-muted">{{ $transaction->formatted_date }}</span>
                                <span class="pull-right">
                                    @if($transaction->payment_status == 'paid')
                                        <span class="label label-success">@lang('lang_v1.paid')</span>
                                    @elseif($transaction->payment_status == 'due')
                                        <span class="label label-danger">@lang('lang_v1.due')</span>
                                    @else
                                        <span class="label label-warning">@lang('lang_v1.partial')</span>
                                    @endif
                                </span>
                            </h5>
                        </div>
                        <div class="box-body" style="padding-top:0;">
                            <table class="table table-condensed" style="margin-bottom:5px;">
                                <thead>
                                    <tr>
                                        <th>@lang('sale.product')</th>
                                        <th class="text-center">@lang('lang_v1.quantity')</th>
                                        <th class="text-right">@lang('sale.unit_price')</th>
                                        <th class="text-right">@lang('sale.subtotal')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->sell_lines as $line)
                                        @php
                                            $unit_short = !empty($line->sub_unit) ? $line->sub_unit->short_name : (!empty($line->product->unit) ? $line->product->unit->short_name : '');
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ !empty($line->product) ? $line->product->name : __('lang_v1.deleted_product') }}
                                                @if(!empty($line->quantity_returned) && $line->quantity_returned > 0)
                                                    <span class="label label-default">@lang('lang_v1.partially_returned')</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $line->formatted_quantity }} {{ $unit_short }}</td>
                                            <td class="text-right">{{ $line->formatted_unit_price }}</td>
                                            <td class="text-right">{{ $line->formatted_subtotal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-right">
                                <strong>@lang('sale.total'): {{ $transaction->formatted_final_total }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>
