$(document).ready(function() {
    $(document).on('change', '.purchase_quantity', function() {
        update_table_total($(this).closest('table'));
    });
    $(document).on('change', '.unit_price', function() {
        update_table_total($(this).closest('table'));
    });

    // Sub-unit (e.g. Baby Box) for an opening stock row scales the
    // qty and price the user enters to base units on save. The unit
    // table is the source of truth for the multiplier; the price
    // displayed next to the qty is the per-sub-unit price (e.g. per
    // Baby Box), which mirrors how purchases normally happen.
    $(document).on('change', '.os_sub_unit', function() {
        var $sel = $(this);
        var $tr = $sel.closest('tr');
        var multiplier = parseFloat($sel.find(':selected').data('multiplier')) || 1;
        var unitName = $sel.find(':selected').text() || $tr.find('.os_qty_unit_label').data('default-label');
        $tr.find('.os_qty_unit_label').text(unitName);

        var $qty = $tr.find('input.os_qty');
        var $price = $tr.find('input.os_unit_price');

        // Anchor on the *original* base values once (first time
        // we touch this row), so flipping units back and forth
        // does not compound the multiplication.
        if ($qty.data('base-qty-anchored') === undefined) {
            var initialQ = __read_number($qty);
            var initialP = __read_number($price);
            $qty.data('base-qty', isNaN(initialQ) ? 0 : initialQ);
            $price.data('base-price', isNaN(initialP) ? 0 : initialP);
            $qty.data('base-qty-anchored', 1);
            $price.data('base-price-anchored', 1);
        }

        var baseQty = parseFloat($qty.data('base-qty')) || 0;
        var basePrice = parseFloat($price.data('base-price')) || 0;

        if (multiplier === 1) {
            $qty.val(__number_f(baseQty));
            $price.val(__number_f(basePrice));
        } else {
            $qty.val(__number_f(baseQty / multiplier));
            $price.val(__number_f(basePrice * multiplier));
        }
        update_table_total($tr.closest('table'));
    });

    // On page load, render each row in its pre-selected sub-unit so
    // the user sees "1 Baby Box @ 100" not "100 Tablets @ 1".
    $('.os_sub_unit').each(function() {
        var $sel = $(this);
        var init = $sel.data('initial-sub-unit');
        if (init && $sel.val() == init) {
            $sel.trigger('change');
        }
    });

    $('.os_exp_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });

    $(document).on('click', '.add_stock_row', function() {
        var tr = $(this).data('row-html');
        var key = parseInt($(this).data('sub-key'));
        tr = tr.replace(/\__subkey__/g, key);
        $(this).data('sub-key', key + 1);

        $(tr)
            .insertAfter($(this).closest('tr'))
            .find('.os_exp_date')
            .datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });

            $(this).closest('tr').next('tr').find('.os_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    });

    $(document).on('click', '.add-opening-stock', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('#opening_stock_modal')
                    .html(result)
                    .modal('show');
            },
        });
    });
});

//Re-initialize data picker on modal opening
 $('#opening_stock_modal').on('shown.bs.modal', function(e) {
    $('#opening_stock_modal .os_exp_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });
    $('#opening_stock_modal .os_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'right',
            vertical: 'bottom'
        }
    });
 });

$(document).on('click', 'button#add_opening_stock_btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var data = $('form#add_opening_stock_form').serialize();

    $.ajax({
        method: 'POST',
        url: $('form#add_opening_stock_form').attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(btn);
        },
        success: function(result) {
            if (result.success == true) {
                $('#opening_stock_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
    return false;
});

function update_table_total(table) {
    var total_subtotal = 0;
    table.find('tbody tr').each(function() {
        var qty = __read_number($(this).find('.purchase_quantity'));
        var unit_price = __read_number($(this).find('.unit_price'));
        var row_subtotal = qty * unit_price;
        $(this)
            .find('.row_subtotal_before_tax')
            .text(__number_f(row_subtotal));
        total_subtotal += row_subtotal;
    });
    table.find('tfoot tr #total_subtotal').text(__currency_trans_from_en(total_subtotal, true));
    table.find('tfoot tr #total_subtotal_hidden').val(total_subtotal);
}
