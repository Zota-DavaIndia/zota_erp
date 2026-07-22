@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.stock_min_max_settings'))

@section('content')
    @include('superadmin::layouts.nav')

    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @lang('superadmin::lang.stock_min_max_settings')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('superadmin::lang.stock_settings_desc')</small>
        </h1>
    </section>

    <section class="content">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ is_array(session('status')) ? session('status.msg') : session('status') }}
            </div>
        @endif

        <div class="box box-solid">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('superadmin::lang.select_store'):</label>
                            <select id="location_filter" class="form-control">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ $selected_location == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->business_name }} - {{ $loc->location_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" style="padding-top: 25px;">
                        <a href="{{ action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'index']) }}"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> @lang('superadmin::lang.back_to_tags')
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                @if(count($stock_data) > 0)
                    {!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'saveStockSettings']), 'method' => 'POST']) !!}

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>@lang('sale.product')</th>
                                    <th>@lang('product.sku')</th>
                                    <th>@lang('superadmin::lang.current_stock')</th>
                                    <th>@lang('superadmin::lang.movement_tag')</th>
                                    <th>@lang('superadmin::lang.min_qty')</th>
                                    <th>@lang('superadmin::lang.max_qty')</th>
                                    <th>@lang('superadmin::lang.source')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stock_data as $row)
                                    @php
                                        $stock_class = '';
                                        if ($row->qty_available < $row->min_quantity && $row->min_quantity > 0) {
                                            $stock_class = 'danger';
                                        } elseif ($row->max_quantity > 0 && $row->qty_available > $row->max_quantity) {
                                            $stock_class = 'warning';
                                        }
                                    @endphp
                                    <tr class="{{ $stock_class }}">
                                        <td>
                                            {{ $row->product_name }}
                                            @if($row->variation_name != 'DUMMY')
                                                <br><small class="text-muted">{{ $row->variation_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $row->sub_sku ?? $row->product_sku }}</td>
                                        <td>
                                            <span class="label {{ $stock_class == 'danger' ? 'label-danger' : ($stock_class == 'warning' ? 'label-warning' : 'label-success') }}">
                                                {{ @number_format($row->qty_available, 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{-- Editable initial movement tag (super admin bootstrap).
                                                 Auto-retagging overwrites this once the product has
                                                 enough sales history at this store. --}}
                                            <select name="stocks[{{ $row->id }}][movement_tag]" class="form-control input-sm" style="width: 90px;">
                                                <option value="" @if(empty($row->movement_tag)) selected @endif>&mdash;</option>
                                                @foreach(['SFM' => 'SFM', 'FM' => 'FM', 'NFM' => 'NFM', 'SM' => 'SM'] as $code => $label)
                                                    <option value="{{ $code }}" @if($row->movement_tag == $code) selected @endif>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="stocks[{{ $row->id }}][min_quantity]"
                                                   value="{{ (int)$row->min_quantity }}" class="form-control input-sm"
                                                   min="0" style="width: 80px;">
                                        </td>
                                        <td>
                                            <input type="number" name="stocks[{{ $row->id }}][max_quantity]"
                                                   value="{{ (int)$row->max_quantity }}" class="form-control input-sm"
                                                   min="0" style="width: 80px;">
                                        </td>
                                        <td>
                                            <span class="label {{ $row->min_max_source == 'auto' ? 'label-info' : 'label-default' }}">
                                                {{ ucfirst($row->min_max_source) }}
                                            </span>
                                            @if($row->last_auto_update_at)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($row->last_auto_update_at)->diffForHumans() }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                            <i class="fa fa-save"></i> @lang('superadmin::lang.save_stock_settings')
                        </button>
                    </div>

                    {!! Form::close() !!}
                @else
                    <p class="text-center text-muted">@lang('superadmin::lang.no_stock_data')</p>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#location_filter').change(function() {
        var loc_id = $(this).val();
        var url = '{{ action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, "stockSettings"]) }}';
        window.location.href = url + '?location_id=' + loc_id;
    });
});
</script>
@endsection
