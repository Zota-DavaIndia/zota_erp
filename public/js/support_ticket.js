$(document).ready(function() {
    if ($('table#support_ticket_index_table').length == 1) {
        support_ticket_index_table = $('table#support_ticket_index_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: support_ticket_index_table_url,
            columns: [
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'product_name', name: 'purchase_line.product.name', orderable: false, searchable: false },
                { data: 'location_name', name: 'location.name', orderable: false, searchable: false },
                { data: 'ticket_type', name: 'ticket_type' },
                { data: 'quantity_damaged', name: 'quantity_damaged' },
                { data: 'quantity_lost', name: 'quantity_lost' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    }

    if ($('table#support_ticket_dashboard_table').length == 1) {
        support_ticket_dashboard_table = $('table#support_ticket_dashboard_table').DataTable({
            processing: true,
            serverSide: true,
            // No client-side default sort - the server already orders delayed
            // tickets first (then open, then closed) so they get immediate
            // attention; a client-side aaSorting would override that.
            order: [],
            ajax: support_ticket_dashboard_table_url,
            columns: [
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'product_name', name: 'purchase_line.product.name', orderable: false, searchable: false },
                { data: 'grn_no', name: 'transaction.ref_no', orderable: false, searchable: false },
                { data: 'po_no', name: 'purchase_order.ref_no', orderable: false, searchable: false },
                { data: 'location_name', name: 'location.name', orderable: false, searchable: false },
                { data: 'ticket_type', name: 'ticket_type' },
                { data: 'quantity_damaged', name: 'quantity_damaged' },
                { data: 'quantity_lost', name: 'quantity_lost' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    }
});
