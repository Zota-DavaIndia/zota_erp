@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.movement_tags'))

@section('content')
    @include('superadmin::layouts.nav')

    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @lang('superadmin::lang.movement_tags')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('superadmin::lang.movement_tags_desc')</small>
        </h1>
    </section>

    <section class="content">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ is_array(session('status')) ? session('status.msg') : session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        {{-- Global Tag Configuration --}}
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-globe"></i> @lang('superadmin::lang.global_tag_config')
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'stockSettings']) }}"
                       class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-dw-btn-info">
                        <i class="fa fa-sliders"></i> @lang('superadmin::lang.stock_min_max_settings')
                    </a>
                    <a href="{{ action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'runAutoCalculation']) }}"
                       class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-dw-btn-success"
                       onclick="return confirm('@lang('superadmin::lang.confirm_auto_calculate')')">
                        <i class="fa fa-refresh"></i> @lang('superadmin::lang.run_auto_calculation')
                    </a>
                </div>
            </div>
            <div class="box-body">
                <p class="text-muted">@lang('superadmin::lang.global_config_help')</p>

                {!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'saveGlobal']), 'method' => 'POST']) !!}

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="global_tags_table">
                        <thead>
                            <tr>
                                <th>@lang('superadmin::lang.tag_code')</th>
                                <th>@lang('superadmin::lang.tag_name')</th>
                                <th>@lang('superadmin::lang.min_monthly_sales')</th>
                                <th>@lang('superadmin::lang.max_monthly_sales')</th>
                                <th>@lang('superadmin::lang.avg_days_for_min')</th>
                                <th>@lang('superadmin::lang.buffer_percent')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($global_configs as $i => $config)
                                <tr>
                                    <td>
                                        <input type="text" name="tags[{{ $i }}][tag_code]" value="{{ $config->tag_code }}" class="form-control" required maxlength="10">
                                    </td>
                                    <td>
                                        <input type="text" name="tags[{{ $i }}][tag_name]" value="{{ $config->tag_name }}" class="form-control" required maxlength="50">
                                    </td>
                                    <td>
                                        <input type="number" name="tags[{{ $i }}][min_monthly_sales]" value="{{ (int)$config->min_monthly_sales }}" class="form-control" min="0" step="1">
                                    </td>
                                    <td>
                                        <input type="number" name="tags[{{ $i }}][max_monthly_sales]" value="{{ $config->max_monthly_sales !== null ? (int)$config->max_monthly_sales : '' }}" class="form-control" min="0" step="1" placeholder="@lang('superadmin::lang.unlimited')">
                                    </td>
                                    <td>
                                        <input type="number" name="tags[{{ $i }}][avg_days_for_min_stock]" value="{{ $config->avg_days_for_min_stock }}" class="form-control" min="0" step="1">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="tags[{{ $i }}][max_stock_buffer_percent]" value="{{ (int)$config->max_stock_buffer_percent }}" class="form-control" min="0" step="1">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-xs remove_tag_row"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-default btn-sm add_tag_row" data-table="global_tags_table">
                    <i class="fa fa-plus"></i> @lang('superadmin::lang.add_tag')
                </button>

                <div class="text-right" style="margin-top: 15px;">
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                        <i class="fa fa-save"></i> @lang('superadmin::lang.save_global_config')
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>

        {{-- Per-Location Overrides --}}
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-map-marker"></i> @lang('superadmin::lang.location_overrides')
                </h3>
            </div>
            <div class="box-body">
                <p class="text-muted">@lang('superadmin::lang.location_override_help')</p>

                {{-- Dropdown to add override for a location --}}
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-4">
                        <select id="add_location_override" class="form-control">
                            <option value="">@lang('superadmin::lang.select_location_for_override')</option>
                            @foreach($locations as $loc)
                                @if(!isset($location_configs[$loc->id]))
                                    <option value="{{ $loc->id }}">{{ $loc->business_name }} - {{ $loc->location_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-sm" id="btn_add_location_override">
                            <i class="fa fa-plus"></i> @lang('superadmin::lang.add_override')
                        </button>
                    </div>
                </div>

                {{-- Existing location overrides --}}
                @foreach($location_configs as $loc_id => $configs)
                    @php
                        $loc_info = $locations->firstWhere('id', $loc_id);
                    @endphp
                    @if($loc_info)
                        <div class="panel panel-default location-override-panel" data-location-id="{{ $loc_id }}">
                            <div class="panel-heading">
                                <strong>{{ $loc_info->business_name }} - {{ $loc_info->location_name }}</strong>
                                {!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'removeLocationOverride']), 'method' => 'POST', 'style' => 'display:inline;']) !!}
                                    <input type="hidden" name="location_id" value="{{ $loc_id }}">
                                    <button type="submit" class="btn btn-danger btn-xs pull-right" onclick="return confirm('@lang('superadmin::lang.confirm_remove_override')')">
                                        <i class="fa fa-times"></i> @lang('superadmin::lang.remove_override')
                                    </button>
                                {!! Form::close() !!}
                            </div>
                            <div class="panel-body">
                                {!! Form::open(['url' => action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'saveLocation']), 'method' => 'POST']) !!}
                                    <input type="hidden" name="location_id" value="{{ $loc_id }}">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-condensed" id="loc_tags_{{ $loc_id }}">
                                            <thead>
                                                <tr>
                                                    <th>@lang('superadmin::lang.tag_code')</th>
                                                    <th>@lang('superadmin::lang.tag_name')</th>
                                                    <th>@lang('superadmin::lang.min_monthly_sales')</th>
                                                    <th>@lang('superadmin::lang.max_monthly_sales')</th>
                                                    <th>@lang('superadmin::lang.avg_days_for_min')</th>
                                                    <th>@lang('superadmin::lang.buffer_percent')</th>
                                                    <th>@lang('messages.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($configs as $j => $cfg)
                                                    <tr>
                                                        <td><input type="text" name="tags[{{ $j }}][tag_code]" value="{{ $cfg->tag_code }}" class="form-control input-sm" required maxlength="10"></td>
                                                        <td><input type="text" name="tags[{{ $j }}][tag_name]" value="{{ $cfg->tag_name }}" class="form-control input-sm" required maxlength="50"></td>
                                                        <td><input type="number" name="tags[{{ $j }}][min_monthly_sales]" value="{{ (int)$cfg->min_monthly_sales }}" class="form-control input-sm" min="0"></td>
                                                        <td><input type="number" name="tags[{{ $j }}][max_monthly_sales]" value="{{ $cfg->max_monthly_sales !== null ? (int)$cfg->max_monthly_sales : '' }}" class="form-control input-sm" min="0" placeholder="@lang('superadmin::lang.unlimited')"></td>
                                                        <td><input type="number" name="tags[{{ $j }}][avg_days_for_min_stock]" value="{{ $cfg->avg_days_for_min_stock }}" class="form-control input-sm" min="0"></td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" name="tags[{{ $j }}][max_stock_buffer_percent]" value="{{ (int)$cfg->max_stock_buffer_percent }}" class="form-control" min="0">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </td>
                                                        <td><button type="button" class="btn btn-danger btn-xs remove_tag_row"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-default btn-xs add_tag_row" data-table="loc_tags_{{ $loc_id }}">
                                        <i class="fa fa-plus"></i> @lang('superadmin::lang.add_tag')
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm pull-right">
                                        <i class="fa fa-save"></i> @lang('messages.save')
                                    </button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Template for new location override (hidden) --}}
                <div id="new_location_override_template" style="display:none;">
                    <div class="panel panel-default location-override-panel">
                        <div class="panel-heading">
                            <strong class="loc_label"></strong>
                        </div>
                        <div class="panel-body">
                            <form action="{{ action([\Modules\Superadmin\Http\Controllers\MovementTagController::class, 'saveLocation']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="location_id" class="loc_id_input" value="">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed new_loc_table">
                                        <thead>
                                            <tr>
                                                <th>@lang('superadmin::lang.tag_code')</th>
                                                <th>@lang('superadmin::lang.tag_name')</th>
                                                <th>@lang('superadmin::lang.min_monthly_sales')</th>
                                                <th>@lang('superadmin::lang.max_monthly_sales')</th>
                                                <th>@lang('superadmin::lang.avg_days_for_min')</th>
                                                <th>@lang('superadmin::lang.buffer_percent')</th>
                                                <th>@lang('messages.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-default btn-xs add_tag_row_new">
                                    <i class="fa fa-plus"></i> @lang('superadmin::lang.add_tag')
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm pull-right">
                                    <i class="fa fa-save"></i> @lang('messages.save')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var tag_row_counter = 100;

    // Add tag row to existing table
    $(document).on('click', '.add_tag_row', function() {
        var table_id = $(this).data('table');
        var idx = tag_row_counter++;
        var row = '<tr>' +
            '<td><input type="text" name="tags[' + idx + '][tag_code]" class="form-control input-sm" required maxlength="10"></td>' +
            '<td><input type="text" name="tags[' + idx + '][tag_name]" class="form-control input-sm" required maxlength="50"></td>' +
            '<td><input type="number" name="tags[' + idx + '][min_monthly_sales]" class="form-control input-sm" min="0" value="0"></td>' +
            '<td><input type="number" name="tags[' + idx + '][max_monthly_sales]" class="form-control input-sm" min="0" placeholder="{{ __("superadmin::lang.unlimited") }}"></td>' +
            '<td><input type="number" name="tags[' + idx + '][avg_days_for_min_stock]" class="form-control input-sm" min="0" value="0"></td>' +
            '<td><div class="input-group input-group-sm"><input type="number" name="tags[' + idx + '][max_stock_buffer_percent]" class="form-control" min="0" value="20"><span class="input-group-addon">%</span></div></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs remove_tag_row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
        $('#' + table_id + ' tbody').append(row);
    });

    // Remove tag row
    $(document).on('click', '.remove_tag_row', function() {
        $(this).closest('tr').remove();
    });

    // Add location override
    $('#btn_add_location_override').click(function() {
        var sel = $('#add_location_override');
        var loc_id = sel.val();
        var loc_text = sel.find('option:selected').text();
        if (!loc_id) return;

        // Clone template
        var template = $('#new_location_override_template').children().first().clone();
        template.find('.loc_label').text(loc_text);
        template.find('.loc_id_input').val(loc_id);
        template.attr('data-location-id', loc_id);

        // Set table id
        var table_id = 'loc_tags_new_' + loc_id;
        template.find('.new_loc_table').attr('id', table_id);
        template.find('.add_tag_row_new').addClass('add_tag_row').removeClass('add_tag_row_new').attr('data-table', table_id);

        // Add default rows from global config
        @foreach($global_configs as $gc)
        var idx = tag_row_counter++;
        template.find('.new_loc_table tbody').append(
            '<tr>' +
            '<td><input type="text" name="tags[' + idx + '][tag_code]" class="form-control input-sm" required value="{{ $gc->tag_code }}"></td>' +
            '<td><input type="text" name="tags[' + idx + '][tag_name]" class="form-control input-sm" required value="{{ $gc->tag_name }}"></td>' +
            '<td><input type="number" name="tags[' + idx + '][min_monthly_sales]" class="form-control input-sm" value="{{ (int)$gc->min_monthly_sales }}"></td>' +
            '<td><input type="number" name="tags[' + idx + '][max_monthly_sales]" class="form-control input-sm" value="{{ $gc->max_monthly_sales !== null ? (int)$gc->max_monthly_sales : "" }}" placeholder="{{ __("superadmin::lang.unlimited") }}"></td>' +
            '<td><input type="number" name="tags[' + idx + '][avg_days_for_min_stock]" class="form-control input-sm" value="{{ $gc->avg_days_for_min_stock }}"></td>' +
            '<td><div class="input-group input-group-sm"><input type="number" name="tags[' + idx + '][max_stock_buffer_percent]" class="form-control" value="{{ (int)$gc->max_stock_buffer_percent }}"><span class="input-group-addon">%</span></div></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs remove_tag_row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>'
        );
        @endforeach

        template.show();
        $('#new_location_override_template').before(template);

        // Remove option from dropdown
        sel.find('option[value="' + loc_id + '"]').remove();
        sel.val('');
    });
});
</script>
@endsection
