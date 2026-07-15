@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.master_products'))

@section('content')
    @include('superadmin::layouts.nav')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            {{ $master_product->name }}
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('superadmin::lang.master_products')</small>
        </h1>
        <a href="{{ action([\Modules\Superadmin\Http\Controllers\SuperadminProductController::class, 'index']) }}"
           class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> @lang('superadmin::lang.all_business')
        </a>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('sale.products')</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">@lang('sale.products')</th>
                                    <td>{{ $master_product->name }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('product.type')</th>
                                    <td><span class="label label-info">{{ ucfirst($master_product->type ?? 'single') }}</span></td>
                                </tr>
                                <tr>
                                    <th>@lang('product.sku')</th>
                                    <td>{{ $master_product->sku }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('product.category')</th>
                                    <td>{{ $master_product->category->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('product.brand')</th>
                                    <td>{{ $master_product->brand->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('product.unit')</th>
                                    <td>
                                        {{ $master_product->unit->actual_name ?? '—' }}
                                        @if (!empty($master_product->unit->short_name))
                                            ({{ $master_product->unit->short_name }})
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('lang_v1.enable_stock')</th>
                                    <td>{{ !empty($master_product->enable_stock) ? __('messages.yes') : __('messages.no') }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('lang_v1.inactive')</th>
                                    <td>{{ !empty($master_product->is_inactive) ? __('messages.yes') : __('messages.no') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Variations ({{ $master_product->variations->count() }})</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('sale.products')</th>
                                    <th>SKU</th>
                                    <th>@lang('lang_v1.default_sell_price')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($master_product->variations as $v)
                                    <tr>
                                        <td>{{ $v->name }}</td>
                                        <td>{{ $v->sub_sku }}</td>
                                        <td>{{ number_format((float) $v->default_sell_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No variations</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Synced copies across businesses
                            <span class="badge bg-blue">{{ $synced_copies->count() }}</span>
                        </h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('superadmin::lang.all_business')</th>
                                    <th>@lang('sale.products')</th>
                                    <th>SKU</th>
                                    <th>@lang('lang_v1.inactive')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($synced_copies as $copy)
                                    <tr>
                                        <td>{{ $copy->business->name ?? ('#' . $copy->business_id) }}</td>
                                        <td>{{ $copy->name }}</td>
                                        <td>{{ $copy->sku }}</td>
                                        <td>
                                            @if (!empty($copy->is_inactive))
                                                <span class="label label-danger">@lang('lang_v1.inactive')</span>
                                            @else
                                                <span class="label label-success">@lang('lang_v1.active')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No businesses have a synced copy yet. New businesses will receive this product automatically.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
