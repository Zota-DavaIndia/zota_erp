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
    <table class="table table-bordered table-striped" id="movement_analysis_table" style="width:100%; table-layout: auto;">
        <thead>
            <tr>
                <th style="min-width: 220px; width: 22%;">@lang('sale.product')</th>
                <th style="min-width: 130px; width: 10%;">@lang('product.sku')</th>
                <th style="min-width: 150px; width: 12%;">@lang('lang_v1.manufacturer')</th>
                <th style="min-width: 130px; width: 10%;">@lang('lang_v1.movement_tag')</th>
                <th style="min-width: 120px; width: 9%;">@lang('report.current_stock')</th>
                <th style="min-width: 100px; width: 7%;">@lang('superadmin::lang.min_qty')</th>
                <th style="min-width: 100px; width: 7%;">@lang('superadmin::lang.max_qty')</th>
                <th style="min-width: 150px; width: 11%;">@lang('lang_v1.last_updated')</th>
                <th style="min-width: 150px; width: 12%;">@lang('lang_v1.next_update')</th>
            </tr>
        </thead>
    </table>
</div>
