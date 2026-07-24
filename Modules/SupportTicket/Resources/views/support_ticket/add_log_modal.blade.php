<div class="modal-dialog" role="document">
    <form action="{{ action([\Modules\SupportTicket\Http\Controllers\SupportTicketController::class, 'addLog'], [$ticket->id]) }}"
        method="post" id="support_ticket_log_form">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('lang_v1.add_progress_log') - {{ $ticket->ticket_number }}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <textarea name="log_note" class="form-control" rows="3" required placeholder="@lang('lang_v1.add_progress_log_placeholder')"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white ladda-button">
                    @lang('lang_v1.add_progress_log')
                </button>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
