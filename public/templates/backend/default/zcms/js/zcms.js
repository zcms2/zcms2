var ZCMS = {};

ZCMS.setLocation = function ($location) {
    window.location.href = $location;
};

ZCMS.submitForm = function (f) {
    if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
    if ("function" == typeof f.onsubmit) f.onsubmit();
    "function" == typeof f.fireEvent && f.fireEvent("submit");
    var hasErrorRequiredField = false;
    var errorRequiredFieldID = '';
    $('[required]').each(function () {
        if ($(this).val() == '') {
            $(this).parent().addClass('has-error');
            if ($(this).parent().find('.help-block').length == 0) {
                $(this).parent().append('<span class="help-block">This is required field!</span>');
            } else {
                $(this).parent().find('.help-block').css('display', 'block');
            }
            hasErrorRequiredField = true;
            if (errorRequiredFieldID == '') {
                errorRequiredFieldID = $(this).attr('id');
            }

        } else {
            $(this).parent().addClass('has-success').removeClass('has-error');
            $(this).parent().find('.help-block').css('display', 'none');
        }
    });

    if (hasErrorRequiredField == true) {
        if (errorRequiredFieldID != '') {
            $('#' + errorRequiredFieldID).focus();
        }
        return false;
    } else {
        errorRequiredFieldID = '';
    }

    f.submit();
    return false;
};

ZCMS.columnOrdering = function (filterOrder, filterOrderDir) {
    $('#filter_order').val(filterOrder);
    $('#filter_order_dir').val(filterOrderDir);
    $('#adminForm').submit();
};

ZCMS.editButtonSubmit = function (obj) {
    if (ZCMS.hasCheckItems()) {
        var items = document.getElementsByClassName('check_element');
        window.location.href = obj.getAttribute('href') + items[ZCMS.hasCheckItems() - 1].value;
    } else {
        alert('Please choose item to edit!');
    }
    return false;
};

ZCMS.hasCheckItems = function () {
    var items = document.getElementsByClassName('check_element');
    for (var i = 0; i < items.length; i++) {
        if (items[i].checked == true) return (i + 1);
    }
    return 0;
};

ZCMS.publishedSubmit = function (obj) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm('Do you want published item(s) ?');
        if (r == true) {
            var f = document.getElementById('adminForm');
            f.setAttribute('action', obj.getAttribute('href'));
            ZCMS.submitForm(f);
        }
    } else {
        alert('Please choose item to published!');
    }
    return false;
};

ZCMS.customSubmit = function (obj, confirmMessage, errorAlert) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm(confirmMessage);
        if (r == true) {
            var f = document.getElementById('adminForm');
            f.setAttribute('action', obj.getAttribute('href'));
            ZCMS.submitForm(f);
        }
    } else {
        alert(errorAlert);
    }
    return false;
};

ZCMS.unPublishedSubmit = function (obj) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm('Do you want unpublished item(s) ?');
        if (r == true) {
            var f = document.getElementById('adminForm');
            f.setAttribute('action', obj.getAttribute('href'));
            ZCMS.submitForm(f);
        }
    } else {
        alert('Please choose item to unpublished!');
    }
    return false;
};

ZCMS.deleteSubmit = function (obj) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm('Do you want delete item(s) ?');
        if (r == true) {
            var f = document.getElementById('adminForm');
            f.setAttribute('action', obj.getAttribute('href'));
            ZCMS.submitForm(f);
        }
    } else {
        alert('Please choose item to delete!');
    }
    return false;
};

ZCMS.trashSubmit = function (obj) {
    if (ZCMS.hasCheckItems()) {
        var r = confirm('Do you want trash item(s) ?');
        if (r == true) {
            var f = document.getElementById('adminForm');
            f.setAttribute('action', obj.getAttribute('href'));
            ZCMS.submitForm(f);
        }
    } else {
        alert('Please choose item to trash!');
    }
    return false;
};

ZCMS.resetFilter = function () {
    $('#adminForm tr.tr-filter').removeClass('tr-filter-showed').css('display', 'none');
    $('.dataTables_length input').val('');
    $('input[type="text"].zcms-form-filter').val('');
    var filterOrder = $('#filter_order');
    filterOrder.val(filterOrder.attr('data-default'));
    var filterOrderDir = $('#filter_order_dir');
    filterOrderDir.val(filterOrderDir.attr('data-default'));
    $('select.zcms-form-filter option').attr('selected', '').val('');
    $('#adminForm').submit();
};

ZCMS.htmlEncode = function (str) {
    if (str != null) {
        return str.replace(/[&<>"']/g, function ($0) {
            return "&" + {"&": "amp", "<": "lt", ">": "gt", '"': "quot", "'": "#39"}[$0] + ';';
        });
    }
    return str;
};

ZCMS.__processMediaBeforeDisplay = function (media) {
    var segment = media.mime_type.split('/');
    if (segment.length == 2) {
        var normalType = segment[0];
        if (normalType == 'video') {
            media.image_display = _baseUri + '/media/default/video.png';
        } else if (normalType == 'audio') {
            media.image_display = _baseUri + '/media/default/audio.png';
        } else if (normalType == 'image') {
            if (media.src && (media.src.indexOf('http://') >= 0 || media.src.indexOf('https://') >= 0)) {
                media.image_display = media.src;
            } else {
                media.image_display = _baseUri + media.src;
            }
        } else {
            media.image_display = _baseUri + '/media/default/file.png';
        }
        return media;
    } else {
        return '';
    }
};

ZCMS.__processMediaBeforeInsert = function (media) {
    var segment = media.mime_type.split('/'),
        result = '';
    if (segment.length == 2) {
        var normalType = segment[0];
        if (normalType == 'video') {
            result = '<video controls><source src="' + _baseUri + media.src + '" type="' + media.mime_type + '">Your browser does not support the video tag.</video>';
        } else if (normalType == 'audio') {

        } else if (normalType == 'image') {
            //result = '<figure id="' + media.media_id + '" class="img-caption aligncenter"><a href="' + media.src + '"><img class="size-full img-image-' + media.media_id + '" src="' + media.src + '" alt="'+ media.title +'"></a><figcaption class="img-caption-text">' + media.title + '</figcaption></figure>';
            result = '<a href="' + media.src + '"><img class="size-full img-image-' + media.media_id + '" src="' + _baseUri + media.src + '" alt="' + media.title + '"></a>';
        } else {
            result = '<a title="' + media.src + '" href="' + _baseUri + media.src + '">' + media.title + '</a>';
        }
        result = $.parseHTML(result);
        return result[0];
    } else {
        return '';
    }
};

ZCMS.__initSelectMedia = function () {
    var mediaDialog = $('.zcms-custom-main-media-dialog');
    if (mediaDialog.hasClass('init-success')) {
        return true;
    } else {
        mediaDialog.addClass('init-success');
    }
    var body =
        '<div class="form-group row-fluid">' +
        '<div>' +
        '<ul class="nav nav-tabs" role="tablist">' +
        '<li role="presentation" class="active"><a href="#zcms-media-library" aria-controls="zcms-media-library" role="tab" data-toggle="tab">Media Library</a></li>' +
        '<li role="presentation"><a href="#zcms-upload-files" aria-controls="zcms-upload-files" role="tab" data-toggle="tab">Upload Files</a></li>' +
        '<li role="presentation"><a href="#zcms-insert-image-url" aria-controls="zcms-insert-image-url" role="tab" data-toggle="tab">Insert from URL</a></li>' +
        '</ul>' +
        '<div class="tab-content">' +
        '<div role="tabpanel" class="tab-pane active" id="zcms-media-library">' +
        '<div class="zcms-media-library-search-bar"><input class="zcms_media_keyword pull-right col-md-6" style="margin:5px 0;" placeholder="' + 'Search' + '" name="media_keyword"><div class="clearfix"></div></div>' +
        '<div class="zcms-media-files">' +
        '<ul id="smn-custom-attachments">';
    $.get(_baseUri + '/admin/media/manager/getMedia/', function (result) {
        var append = '';
        if (result.code) {
            for (var i = 0; i < result.data.length; i++) {
                var tmp = ZCMS.__processMediaBeforeDisplay(result.data[i]);
                append += '<li class="smn-thumb"><div class="smn-nav"><i class="glyphicon glyphicon-ok"></i></div><img src="' + tmp.image_display + '" alt="' + tmp.title + '" data-content=\'' + JSON.stringify(tmp) + '\'></li>';
            }
        }
        $('#smn-custom-attachments').html(append);
    }, 'JSON');
    body += '</ul>' +
        '</div>' +
        '</div>' +
        '<div role="tabpanel" class="tab-pane" id="zcms-upload-files">...</div>' +
        '<div role="tabpanel" class="tab-pane" id="zcms-insert-image-url">...</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<input type="hidden" name="zcms_select_item" id="zcms_select_item">' +
        '<div class="clearfix"></div>';
    var footer = '<button type="button" href="#" class="btn btn-primary note-media-btn disabled" disabled>Insert Media</button>';
    $('.zcms-custom-main-media-dialog .modal-body').html(body);
    $('.zcms-custom-main-media-dialog .modal-footer').html(footer);
};

ZCMS.__toggleBtn = function ($btn, isEnable) {
    $btn.toggleClass('disabled', !isEnable);
    $btn.attr('disabled', !isEnable);
};

ZCMS.selectMedia = function (type, idInput, idImage) {
    _mediaTarget = idInput;
    ZCMS.__initSelectMedia();
    var $mediaDialog = $('.zcms-custom-main-media-dialog'),
        $mediaUrl = $mediaDialog.find('#zcms_select_item'),
        $mediaBtn = $mediaDialog.find('.note-media-btn'),
        $mediaSearch = $mediaDialog.find('.zcms_media_keyword'),
        $mediaItems = $mediaDialog.find(".smn-thumb"),
        $mediaFiles = $mediaDialog.find(".zcms-media-files"),
        $mediaInfo = {};
    $mediaInfo.keyword = $mediaSearch.val();

    $mediaDialog.one('shown.bs.modal', function () {
        $mediaDialog.on('click', '.smn-thumb', function (event) {
            if ($(this).hasClass('selected')) {
                $('.zcms-custom-main-media-dialog .smn-thumb').removeClass('selected');
                $mediaUrl.val('');
                $mediaInfo.data = JSON.parse($(this).find('img').attr('data-content'));
                ZCMS.__toggleBtn($mediaBtn, false);
            } else {
                $('.zcms-custom-main-media-dialog .smn-thumb').removeClass('selected');
                $(this).addClass('selected');
                $mediaUrl.val(JSON.parse($(this).find('img').attr('data-content')));
                $mediaInfo.data = JSON.parse($(this).find('img').attr('data-content'));
                ZCMS.__toggleBtn($mediaBtn, true);
            }
        });

        $mediaSearch.keyup(function (event) {
            $mediaInfo.keyword = $(this).val();
            $mediaFiles.attr('data-page', '1');
            $.get(_baseUri + '/admin/media/manager/getMedia/', {keyword: $mediaInfo.keyword}, function (result) {
                var append = '';
                if (result.code) {
                    for (var i = 0; i < result.data.length; i++) {
                        var tmp = ZCMS.__processMediaBeforeDisplay(result.data[i]);
                        append += '<li class="smn-thumb"><div class="smn-nav"><i class="glyphicon glyphicon-ok"></i></div><img src="' + tmp.image_display + '" alt="' + tmp.title + '" data-content=\'' + JSON.stringify(tmp) + '\'></li>';
                    }
                }
                $('#smn-custom-attachments').html(append);
            }, 'JSON');
        });

        $mediaBtn.click(function (event) {
            //event.preventDefault();
            $(_mediaTarget).val($mediaInfo.data.image_display);
            $(idImage).attr('src', $mediaInfo.data.image_display);
            $mediaItems.removeClass('selected');
            $mediaDialog.modal('hide');
        });
    }).one('hidden.bs.modal', function () {
        $mediaDialog.off();
        $mediaFiles.off();
        $mediaBtn.off();
    }).modal('show');
};

$(function () {
    //Check & UnCheck all checkbox
    $('#item_check_all').click(function () {
        var check_all = $('.check_element');
        if ($('#item_check_all').is(':checked')) {
            check_all.prop('checked', true);
            check_all.each(function () {
                $(this).parent().addClass('checked').attr('aria-checked', 'true');
            });
        } else {
            check_all.prop('checked', false);
            check_all.each(function () {
                $(this).parent().removeClass('checked').attr('aria-checked', 'false');
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
    $('#adminForm input, #adminForm textarea, #adminForm select').each(function () {
        if (!$(this).hasClass('custom-attribute')) {
            if ($(this).hasClass('has-error')) {
                var message = $(this).attr('data-content');
                $(this).parent().addClass('has-error');
                $(this).parent().append('<span class="help-block">' + message + '</span>');
            } else if ($(this).hasClass('has-success')) {
                $(this).parent().addClass('has-success');
                $(this).parent().append('<span class="help-block">&nbsp;</span>');
            }
        }

    });

    //Render filter date picker
    $('.date-picker.zcms-form-filter').each(function () {
        if ($(this).val() == '') {
            $(this).daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: _ZCMS.dateFormat['gb_js_standard_table_date_format']
                }
            }),$(this).val('');
        }else{
            $(this).daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: _ZCMS.dateFormat['gb_js_standard_table_date_format']
                }
            });
        }
    });

    $('.date-time-picker').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        timePickerIncrement: 5,
        locale: {
            format: _ZCMS.dateFormat['gb_js_date_time_format']
        }
    });

    $('.zcms-form-filter').keypress(function (e) {
        if (e.which == 13) {
            $('#adminForm').submit();
        }
    });

    $('.alert button.close').click(function () {
        var alert = $(this).parent().parent();
        if (alert.hasClass('zcms-toolbar-helper')) {
            alert.remove();
        }
    });

    $('#zcms-search').click(function () {
        var trFilter = $('#adminForm tr.tr-filter');
        if (trFilter.hasClass('tr-filter-showed')) {
            $('#adminForm').submit();
        } else {
            trFilter.addClass('tr-filter-showed');
            return false;
        }
    });

    //ZCMS.balanceColumnHeight();
});

// Fixed toolbar button
//var check_scroll = false;
//$(window).scroll(function () {
//    var page_header = $('.page-header');
//    if ($(window).scrollTop() >= 100) {
//        if (!check_scroll) {
//            page_header.clone().addClass('navbar-fixed-top').appendTo(page_header.parent());
//            check_scroll = true;
//        }
//    }
//    else {
//        $('.page-header.navbar-fixed-top').remove();
//        check_scroll = false;
//    }
//});