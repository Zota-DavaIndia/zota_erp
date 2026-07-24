<?php

namespace Modules\SupportTicket\Http\Controllers;

use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'support_ticket.create',
                'label' => __('lang_v1.raise_support_ticket'),
                'default' => true,
            ],
            [
                'value' => 'support_ticket.view_own',
                'label' => __('lang_v1.my_support_tickets'),
                'default' => true,
            ],
            [
                'value' => 'support_ticket.view_all',
                'label' => __('lang_v1.support_ticket_dashboard'),
                'default' => false,
            ],
            [
                'value' => 'support_ticket.manage',
                'label' => __('lang_v1.close_ticket'),
                'default' => false,
            ],
            [
                'value' => 'support_ticket.add_log',
                'label' => __('lang_v1.add_progress_log'),
                'default' => false,
            ],
        ];
    }

    /**
     * Adds the Support Ticket menu entries to the admin sidebar.
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $can_view_own = auth()->user()->can('support_ticket.view_own');
        $can_view_all = auth()->user()->can('support_ticket.view_all');

        if (! $can_view_own && ! $can_view_all) {
            return;
        }

        Menu::modify('admin-sidebar-menu', function ($menu) use ($can_view_own, $can_view_all) {
            $menu->dropdown(
                __('lang_v1.support_tickets'),
                function ($sub) use ($can_view_own, $can_view_all) {
                    if ($can_view_own) {
                        $sub->url(
                            action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'index']),
                            __('lang_v1.my_support_tickets'),
                            ['icon' => '', 'active' => request()->segment(1) == 'support-tickets' && request()->segment(2) == null]
                        );
                    }
                    if ($can_view_all) {
                        $sub->url(
                            action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'dashboard']),
                            __('lang_v1.support_ticket_dashboard'),
                            ['icon' => '', 'active' => request()->segment(2) == 'dashboard']
                        );
                    }
                },
                ['icon' => 'fas fa fa-ticket-alt']
            )->order(86);
        });
    }
}
