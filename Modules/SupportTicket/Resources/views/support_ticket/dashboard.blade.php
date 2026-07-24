@extends('layouts.app')
@section('title', __('lang_v1.support_ticket_dashboard'))

@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.support_ticket_dashboard') }}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="support_ticket_dashboard_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.ticket_number')</th>
                                <th>@lang('sale.product')</th>
                                <th>@lang('purchase.grn_no')</th>
                                <th>@lang('lang_v1.purchase_order')</th>
                                <th>@lang('purchase.location')</th>
                                <th>@lang('lang_v1.ticket_type')</th>
                                <th>@lang('purchase.quantity_damaged')</th>
                                <th>@lang('purchase.quantity_lost')</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>

@endsection

@section('css')
<style>
    #support_ticket_dashboard_table tr.support-ticket-delayed-row > td {
        background-color: #f8d7da !important;
    }
</style>
@endsection

@section('javascript')
<script>
    var support_ticket_dashboard_table_url = '{{ action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, "dashboard"]) }}';
</script>
<script src="{{ asset('js/support_ticket.js?v=' . $asset_v) }}"></script>
@endsection
