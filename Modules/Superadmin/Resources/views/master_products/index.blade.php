@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.master_products'))

@section('content')
    @include('superadmin::layouts.nav')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
                @lang('superadmin::lang.master_products')
                <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('lang_v1.manage_products')</small>
            </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('status') }}
            </div>
        @endif

        <div class="box box-solid">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>@lang('sale.products')</th>
                            <th>@lang('product.type')</th>
                            <th>@lang('product.sku')</th>
                            <th>@lang('superadmin::lang.master_products') &raquo; @lang('superadmin::lang.all_business')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($master_products as $mp)
                            <tr>
                                <td>
                                    {{ $mp->name }}
                                    @if (!empty($mp->image))
                                        <br><small class="text-muted">{{ $mp->image }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="label label-info">
                                        {{ ucfirst($mp->type ?? 'single') }}
                                    </span>
                                </td>
                                <td>{{ $mp->sku }}</td>
                                <td>
                                    <span class="badge bg-blue">
                                        {{ $mp->master_product_copies_count ?? 0 }}
                                    </span>
                                    <small class="text-muted">@lang('superadmin::lang.all_business')</small>
                                </td>
                                <td>
                                    <a href="{{ action([\Modules\Superadmin\Http\Controllers\SuperadminProductController::class, 'show'], [$mp->id]) }}"
                                       class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i> @lang('messages.view')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No master products yet. Create a product as a superadmin to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                {{ $master_products->links() }}
            </div>
        </div>
    </section>
@endsection
