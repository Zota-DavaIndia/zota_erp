@extends('layouts.app')
@section('title', __('superadmin::lang.all_customers'))
@section('content')
    @include('superadmin::layouts.partials.currency')

    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            <i class="fa fa-users"></i>
            @lang('superadmin::lang.all_customers')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">
                @lang('superadmin::lang.all_customers_chain')
            </small>
        </h1>
    </section>

    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('is_global_filter', __('superadmin::lang.universally_shared') . ':') !!}
                    {!! Form::select('is_global_filter', [
                        '' => __('lang_v1.all'),
                        1 => __('superadmin::lang.universally_shared'),
                        0 => __('superadmin::lang.store_specific'),
                    ], null, ['class' => 'form-control select2', 'id' => 'is_global_filter', 'style' => 'width:100%']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('business_id', __('superadmin::lang.source_business') . ':') !!}
                    {!! Form::select('business_id', $businesses, null, ['class' => 'form-control select2', 'id' => 'business_id', 'placeholder' => __('lang_v1.all'), 'style' => 'width:100%']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('search_text', __('lang_v1.search') . ':') !!}
                    {!! Form::text('search_text', null, ['class' => 'form-control', 'id' => 'search_text', 'placeholder' => __('lang_v1.search_by_name_mobile_email')]) !!}
                </div>
            </div>
        @endcomponent

        <div class="box box-solid">
            <div class="box-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped" id="superadmin_customers_table">
                    <thead>
                    <tr>
                        <th>@lang('contact.name')</th>
                        <th>@lang('contact.mobile')</th>
                        <th>@lang('business.email')</th>
                        <th>@lang('contact.contact_id')</th>
                        <th>@lang('superadmin::lang.universally_shared')</th>
                        <th>@lang('superadmin::lang.source_business')</th>
                        <th>@lang('messages.created_at')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            var table = $('#superadmin_customers_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\Superadmin\Http\Controllers\SuperadminCustomerController::class, 'index']) }}",
                    data: function (d) {
                        d.is_global_filter = $('#is_global_filter').val();
                        d.business_id = $('#business_id').val();
                        d.search_text = $('#search_text').val();
                    },
                },
                columnDefs: [
                    { targets: [5, 6], orderable: false, searchable: false },
                    { targets: [7], orderable: false, searchable: false },
                ],
                columns: [
                    { data: 'name', name: 'contacts.name' },
                    { data: 'mobile', name: 'contacts.mobile' },
                    { data: 'email', name: 'contacts.email' },
                    { data: 'contact_id', name: 'contacts.contact_id' },
                    { data: 'is_global_label', name: 'contacts.is_global', orderable: false, searchable: false },
                    { data: 'source_business', name: 'source_business_name', orderable: false, searchable: false },
                    { data: 'created_at', name: 'contacts.created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });

            $('#is_global_filter, #business_id').change(function () {
                table.ajax.reload();
            });

            // Live search with a small debounce
            var searchTimer;
            $('#search_text').on('keyup', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    table.ajax.reload();
                }, 400);
            });

            $(document).on('click', '.toggle-global-customer', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var targetId = $(this).data('target_id');
                var targetGlobal = $(this).data('target_global');
                var label = targetGlobal == 1
                    ? "@lang('superadmin::lang.promote_to_global')"
                    : "@lang('superadmin::lang.demote_to_business')";
                if (! confirm("@lang('messages.are_you_sure')" + ' (' + label + ')')) {
                    return;
                }
                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data.msg);
                            table.ajax.reload();
                        } else {
                            toastr.error(data.msg);
                        }
                    },
                });
            });
        });
    </script>
@endsection
