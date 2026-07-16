@extends('layouts.app')
@section('title', __( 'user.users' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'user.users' )
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang( 'user.manage_users' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_users' )])
        @can('user.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full" href="{{action([\App\Http\Controllers\ManageUserController::class, 'create'])}}">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>                        @lang( 'messages.add' )
                    </a>
                 </div>
            @endslot
        @endcan
        {{-- Status tabs. The current tab is read on the client and
             passed back to the server as a `status` query string so
             the DataTables AJAX endpoint can filter the union query
             accordingly. Each tab shows an icon, the label and a
             count badge. The active tab uses the Dava India brand
             green. --}}
        @php
            $current_status = request()->query('status', 'all');
            $tabs = [
                'all' => [
                    'icon'  => 'fa-users',
                    'label' => __('superadmin::lang.all_users'),
                    'count' => $count_all ?? 0,
                    'class' => 'users-tab--all',
                ],
                'precreated' => [
                    'icon'  => 'fa-user-clock',
                    'label' => __('superadmin::lang.precreated'),
                    'count' => $count_precreated ?? 0,
                    'class' => 'users-tab--precreated',
                ],
                'assigned' => [
                    'icon'  => 'fa-user-check',
                    'label' => __('superadmin::lang.assigned'),
                    'count' => $count_assigned ?? 0,
                    'class' => 'users-tab--assigned',
                ],
            ];
        @endphp
        <style>
            /* Dava India brand-styled status tabs on the /users
               page. Primary green (#1F7A4D) for the active tab and
               secondary orange (#F26A21) for the pre-created count
               badge, mirroring the dashboard palette. */
            #users_status_tabs {
                border-bottom: 1px solid #e5e7eb;
                margin-bottom: 18px;
            }
            #users_status_tabs > li > a {
                position: relative;
                color: #4b5563;
                font-weight: 600;
                padding: 10px 18px 10px 14px;
                border: 1px solid transparent;
                border-radius: 8px 8px 0 0;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.15s ease;
            }
            #users_status_tabs > li > a:hover {
                background: #f3f4f6;
                color: #1f2937;
                border-color: transparent;
            }
            #users_status_tabs > li > a i {
                font-size: 15px;
                opacity: 0.8;
            }
            #users_status_tabs > li > a .users-tab__count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 26px;
                height: 22px;
                padding: 0 8px;
                border-radius: 999px;
                background: #e5e7eb;
                color: #374151;
                font-size: 12px;
                font-weight: 700;
                line-height: 1;
            }
            #users_status_tabs > li > a.active {
                color: #ffffff;
                background: #1F7A4D;
                border-color: #1F7A4D;
            }
            #users_status_tabs > li > a.active i {
                opacity: 1;
            }
            #users_status_tabs > li > a.active .users-tab__count {
                background: #ffffff;
                color: #1F7A4D;
            }
            #users_status_tabs > li > a.users-tab--precreated.active {
                background: #F26A21;
                border-color: #F26A21;
            }
            #users_status_tabs > li > a.users-tab--precreated.active .users-tab__count {
                color: #F26A21;
            }
            #users_status_tabs > li > a.users-tab--precreated .users-tab__count {
                background: #FDE6D6;
                color: #B14610;
            }
            #users_status_tabs > li > a.users-tab--assigned .users-tab__count {
                background: #D7EFE3;
                color: #115C39;
            }
        </style>
        <ul class="nav nav-tabs tw-mb-3" id="users_status_tabs" role="tablist">
            @foreach ($tabs as $key => $tab)
                <li class="nav-item">
                    <a class="nav-link users-tab__btn {{ $tab['class'] }} {{ $current_status === $key ? 'active' : '' }}"
                       href="{{ request()->fullUrlWithQuery(['status' => $key]) }}">
                        <i class="fa {{ $tab['icon'] }}"></i>
                        <span>{{ $tab['label'] }}</span>
                        <span class="users-tab__count">{{ $tab['count'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @can('user.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users_table">
                    <thead>
                        <tr>
                            <th>@lang( 'business.username' )</th>
                            <th>@lang( 'user.name' )</th>
                            <th>@lang( 'user.role' )</th>
                            <th>@lang( 'business.business' )</th>
                            <th>@lang( 'business.email' )</th>
                            <th class="not-export">@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        // Read the active tab from the URL so DataTables can
        // forward it to the AJAX endpoint and refresh the listing
        // when the user clicks a different tab.
        function getActiveStatus() {
            var params = new URLSearchParams(window.location.search);
            var s = params.get('status');
            return s && s.length ? s : 'all';
        }

        var users_table = $('#users_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    ajax: {
                        url: '/users',
                        data: function (d) {
                            d.status = getActiveStatus();
                        }
                    },
                    columnDefs: [ {
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    } ],
                    "columns":[
                        {"data":"username"},
                        {"data":"full_name"},
                        {"data":"role"},
                        {"data":"business_name"},
                        {"data":"email"},
                        {"data":"action"}
                    ]
                });
        $(document).on('click', 'button.delete_user_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_user,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
             });
        });
        
    });
    
    
</script>
@endsection
