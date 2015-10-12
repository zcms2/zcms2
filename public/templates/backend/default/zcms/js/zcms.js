if ("undefined" === typeof ZCMS) var ZCMS = {};

//ZCMS.submitForm = function (f) {
//    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
//    if ("function" == typeof f.onsubmit) f.onsubmit();
//    "function" == typeof f.fireEvent && f.fireEvent("submit");
//    f.submit();
//    return false;
//};

ZCMS.submitForm = function (f) {
    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
    if ("function" == typeof f.onsubmit) f.onsubmit();
    "function" == typeof f.fireEvent && f.fireEvent("submit");
    var hasErrorRequiredField = false;
    var ErrorRequiredFieldID = '';
    $('[required]').each(function () {
        if ($(this).val() == '') {
            $(this).parent().addClass('has-error');
            if ($(this).parent().find('.help-block').length == 0) {
                $(this).parent().append('<span class="help-block">This is required field!</span>');
            } else {
                $(this).parent().find('.help-block').css('display', 'block');
            }
            hasErrorRequiredField = true;
            if (ErrorRequiredFieldID == '') {
                ErrorRequiredFieldID = $(this).attr('id');
            }

        } else {
            $(this).parent().addClass('has-success').removeClass('has-error');
            $(this).parent().find('.help-block').css('display', 'none');
        }
    });

    if (hasErrorRequiredField == true) {
        if (ErrorRequiredFieldID != '') {
            $('#' + ErrorRequiredFieldID).focus();
        }
        return false;
    } else {
        ErrorRequiredFieldID = '';
    }

    f.submit();
    return false;
};

ZCMS.columnOrdering = function (a, b, f) {
    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
    f.filter_order.value = a;
    f.filter_order_dir.value = b;
    ZCMS.submitForm(f);
};

ZCMS.resetFilter = function (f) {
    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
    f.filter_order.value = "";
    f.filter_order_dir.value = "";
    f.filter_search.value = "";
    ZCMS.submitForm(f);
};

ZCMS.editButtonSubmit = function (obj, f) {
    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
    var link = obj.getAttribute("href");
    f.setAttribute("action", link);
    if (ZCMS.hasCheckItems()) {
        var items = document.getElementsByClassName('check_element');
        window.location.href = link + items[ZCMS.hasCheckItems() - 1].value;
    } else {
        alert("Please choose item to edit!");
    }
    return false;
};

ZCMS.hasCheckItems = function () {
    var items = document.getElementsByClassName('check_element');
    for (i = 0; i < items.length; i++) {
        if (items[i].checked == true) return (i + 1);
    }
    return 0;
};

ZCMS.publishedSubmit = function (obj, f) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm("Do you want published item(s) ?");
        if (r == true) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            var link = obj.getAttribute("href");
            f.setAttribute("action", link);
            ZCMS.submitForm(f);
        }
    } else {
        alert("Please choose item to published!");
    }
    return false;
};

ZCMS.customSubmit = function (obj, confirmMessage, errorAlert, f) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm(confirmMessage);
        if (r == true) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            var link = obj.getAttribute("href");
            f.setAttribute("action", link);
            ZCMS.submitForm(f);
        }
    } else {
        alert(errorAlert);
    }
    return false;
};

ZCMS.unPublishedSubmit = function (obj, f) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm("Do you want unpublished item(s) ?");
        if (r == true) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            var link = obj.getAttribute("href");
            f.setAttribute("action", link);
            ZCMS.submitForm(f);
        }
    } else {
        alert("Please choose item to unpublished!");
    }
    return false;
};

ZCMS.deleteSubmit = function (obj, f) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm("Do you want delete item(s) ?");
        if (r == true) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            var link = obj.getAttribute("href");
            f.setAttribute("action", link);
            ZCMS.submitForm(f);
        }
    } else {
        alert("Please choose item to delete!");
    }
    return false;
};

ZCMS.trashSubmit = function (obj, f) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm("Do you want trash item(s) ?");
        if (r == true) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            var link = obj.getAttribute("href");
            f.setAttribute("action", link);
            ZCMS.submitForm(f);
        }
    } else {
        alert("Please choose item to trash!");
    }
    return false;
};

ZCMS.resetFilter = function () {
    $('.dataTables_length input').val('');
    $('input[type="text"].zcms-form-filter').val('');
    $('select.zcms-form-filter option').attr('selected', '').val('');
    $('#adminForm').submit();
};

ZCMS.initDateRange = function (idStartDate, idEndDate) {
    $('#' + idEndDate).attr("disabled", "disabled");

    $('#' + idStartDate).datepicker({
        autoclose: true
    }).on('changeDate', function (ev) {
        toDate.setStartDate(ev.date);
        $('#' + idEndDate).removeAttr("disabled");
        $('#' + idEndDate).focus();
    }).data('datepicker');
    var toDate = $('#' + idEndDate).datepicker({
        autoclose: true
    }).data('datepicker');
};

ZCMS.htmlencode = function (str) {
    if (str != null) {
        return str.replace(/[&<>"']/g, function ($0) {
            return "&" + {"&": "amp", "<": "lt", ">": "gt", '"': "quot", "'": "#39"}[$0] + ";";
        });
    }
    return str;
};

ZCMS.balanceColumnHeight = function () {
    if ($(window).width() >= 992) {
        var pnl_left = $('.pnl-left');
        var pnl_right = $('.pnl-right');
        var pnl_left_height = pnl_left.height();
        var pnl_right_height = pnl_right.height();
        if (pnl_left_height >= pnl_right_height) {
            pnl_right.height(pnl_left_height);
        } else {
            pnl_left.height(pnl_right_height);
        }
    }
};

ZCMS.alertModal = function (title, message, title_theme, title_is_html, message_is_html) {
    if (title_is_html === undefined) title_is_html = true;
    if (message_is_html === undefined) message_is_html = true;

    var modal_header = $('#zcms-alert-modal > .modal-header > h4');
    var modal_body = $('#zcms-alert-modal > .modal-body');

    if (title_is_html) {
        if (title_theme == 'success') {
            title = '<i class="fa fa-check-circle" style="color: #3c763d"></i> ' + title;
        } else if (title_theme == 'info') {
            title = '<i class="fa fa-info-circle" style="color: #31708f"></i> ' + title;
        } else if (title_theme == 'warning') {
            title = '<i class="fa fa-exclamation-triangle" style="color: #bb995f"></i> ' + title;
        } else if (title_theme == 'error') {
            title = '<i class="fa fa-times-circle" style="color: #a94442"></i> ' + title;
        }

        modal_header.html(title);
    } else {
        modal_header.text(title);
    }

    if (message_is_html) {
        modal_body.html(message);
    } else {
        modal_body.text(message);
    }

    $('#zcms-alert-modal').modal('show');
};

jQuery(document).ready(function () {
    //Check & UnCheck all checkbox
    jQuery('#adminForm #item_check_all').click(function () {
        var check_all = $('.check_element');
        if (jQuery('#item_check_all').is(':checked')) {
            check_all.prop('checked', true);
            check_all.each(function () {
                jQuery(this).parent().addClass('checked').attr('aria-checked', 'true');
            });
        } else {
            check_all.prop('checked', false);
            check_all.each(function () {
                jQuery(this).parent().removeClass('checked').attr('aria-checked', 'false');
            });
        }
    });

    // Click sorting link when cell clicked
    $('th.sorting').click(function () {
        if ($(this).children('a').length > 0) {
            $(this).children('a')[0].click();
        }
    });

    // Add validate message
    jQuery('#adminForm input, #adminForm textarea, #adminForm select').each(function () {
        if (!jQuery(this).hasClass('custom-attribute')) {
            if (jQuery(this).hasClass('has-error')) {
                var message = $(this).attr('data-content');
                jQuery(this).parent().addClass('has-error');
                jQuery(this).parent().append('<span class="help-block">' + message + '</span>');
            } else if (jQuery(this).hasClass('has-success')) {
                jQuery(this).parent().addClass('has-success');
                jQuery(this).parent().append('<span class="help-block">&nbsp;</span>');
            }
        }

    });

    //Render date picker
    jQuery('body').delegate('.date-picker', 'mouseover', function () {
        jQuery(this).datepicker({
            autoclose: true
        });
    });

    jQuery('.zcms-form-filter').keypress(function (e) {
        if (e.which == 13) {
            jQuery('#adminForm').submit();
        }
    });

    ZCMS.balanceColumnHeight();

});

//Redirect location
ZCMS.setLocation = function ($location) {
    window.location.href = $location;
};

// Fixed toolbar button
var check_scroll = false;
jQuery(window).scroll(function () {
    var page_header = jQuery('.page-header');
    if ($(window).scrollTop() >= 100) {
        if (!check_scroll) {
            page_header.clone().addClass('navbar-fixed-top').appendTo(page_header.parent());
            check_scroll = true;
        }
    }
    else {
        jQuery('.page-header.navbar-fixed-top').remove();
        check_scroll = false;
    }
});

$('.alert button.close').click(function () {
    var alert = $(this).parent().parent();
    if (alert.hasClass('zcms-toolbar-helper')) {
        alert.remove();
    }
});