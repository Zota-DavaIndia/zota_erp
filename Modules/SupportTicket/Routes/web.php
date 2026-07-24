<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportTicket\Http\Controllers\SupportTicketController;

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::get('support-tickets/dashboard', [SupportTicketController::class, 'dashboard']);
    Route::get('support-tickets/create/{purchase_line_id}', [SupportTicketController::class, 'create']);
    Route::post('support-tickets/{id}/close', [SupportTicketController::class, 'close']);
    Route::get('support-tickets/{id}/log', [SupportTicketController::class, 'addLogForm']);
    Route::post('support-tickets/{id}/log', [SupportTicketController::class, 'addLog']);
    Route::resource('support-tickets', SupportTicketController::class)->only(['index', 'store', 'show']);
});
