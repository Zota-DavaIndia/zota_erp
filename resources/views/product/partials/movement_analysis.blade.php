<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('movement_tag_filter', __('lang_v1.movement_tag') . ':') !!}
            {!! Form::select('movement_tag_filter', [
                'SFM' => __('lang_v1.super_fast_moving'),
                'FM' => __('lang_v1.fast_moving'),
                'NFM' => __('lang_v1.normal_fast_moving'),
                'SM' => __('lang_v1.slow_moving'),
            ], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'movement_tag_filter']) !!}
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="movement_analysis_table">
        <thead>
            <tr>
                <th>@lang('sale.product')</th>
                <th>@lang('product.sku')</th>
                <th>@lang('lang_v1.manufacturer')</th>
                <th>@lang('lang_v1.movement_tag')</th>
                <th>@lang('report.current_stock')</th>
                <th>@lang('superadmin::lang.min_qty')</th>
                <th>@lang('superadmin::lang.max_qty')</th>
                <th>@lang('lang_v1.last_updated')</th>
                <th>@lang('lang_v1.next_update')</th>
            </tr>
        </thead>
    </table>
</div>
