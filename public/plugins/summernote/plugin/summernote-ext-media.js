(function (factory) {
    /* global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals: jQuery
        factory(window.jQuery);
    }
}(function ($) {
    // template
    var tmpl = $.summernote.renderer.getTemplate();

    // core functions: range, dom
    var range = $.summernote.core.range;
    var dom = $.summernote.core.dom;

    var processMediaBeforeDisplay = function (media) {
        var segment = media.mime_type.split('/');
        if (segment.length == 2) {
            var normalType = segment[0];
            if (normalType == 'video') {
                media.image_display = _baseUri + '/media/default/video.png';
            } else if (normalType == 'audio') {
                media.image_display = _baseUri + '/media/default/audio.png';
            } else if (normalType == 'image') {
                media.image_display = media.src;
            } else {
                media.image_display = _baseUri + '/media/default/file.png';
            }
            return media;
        } else {
            return '';
        }
    };

    var processMediaBeforeInsert = function (media) {
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

    /**
     * toggle button status
     *
     * @member plugin.media
     * @private
     * @param {jQuery} $btn
     * @param {Boolean} isEnable
     */
    var toggleBtn = function ($btn, isEnable) {
        $btn.toggleClass('disabled', !isEnable);
        $btn.attr('disabled', !isEnable);
    };

    /**
     * Show media dialog and set event handlers on dialog controls.
     *
     * @member plugin.media
     * @private
     * @param {jQuery} $dialog
     * @param {jQuery} $dialog
     * @return {Promise}
     */
    var showMediaDialog = function ($editable, $dialog) {
        return $.Deferred(function (deferred) {
            var $mediaDialog = $dialog.find('.zcms-main-media-dialog'),
                $mediaUrl = $mediaDialog.find('#zcms_select_item'),
                $mediaBtn = $mediaDialog.find('.note-media-btn'),
                $mediaSearch = $mediaDialog.find('.zcms_media_keyword'),
                $mediaItems = $dialog.find(".smn-thumb"),
                $mediaFiles = $dialog.find(".zcms-media-files"),
                $mediaInfo = {};
            $mediaInfo.keyword = $mediaSearch.val();

            $mediaFiles.scroll(function () {
                //console.log($(this).height(),$(this).scrollTop(),$(this)[0].scrollHeight);
                //if($(window).scrollTop() == $(document).height() - $(window).height()) {
                //    // ajax call get data from server and append to the div
                //}
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                    //alert('end reached');
                    //console.log('end reached');
                    //$mediaInfo.keyword = $mediaSearch.val();
                    //$mediaInfo.page = $mediaFiles.attr('data-page');
                    //if ($mediaInfo.page == '') {
                    //    $mediaInfo.page = 1;
                    //} else {
                    //    $mediaInfo.page++;
                    //}
                    //$.get(_baseUri + '/admin/media/manager/getMedia/', {keyword: $mediaInfo.keyword, page: $mediaInfo.page}, function (result) {
                    //    var append = '';
                    //    if (result.code) {
                    //        for (var i = 0; i < result.data.length; i++) {
                    //            var tmp = processMediaBeforeDisplay(result.data[i]);
                    //            append += '<li class="smn-thumb"><div class="smn-nav"><i class="glyphicon glyphicon-ok"></i></div><img src="' + _baseUri + tmp.image_display + '" alt="' + tmp.title + '" data-content=\'' + JSON.stringify(tmp) + '\'></li>';
                    //        }
                    //    }
                    //    $('#smn-attachments').html(append);
                    //}, 'JSON');
                }
            });

            $mediaDialog.one('shown.bs.modal', function () {
                if (!$mediaDialog.hasClass('modal-fullscreen')) {
                    $mediaDialog.addClass('modal-fullscreen');
                }
                $mediaDialog.on('click', '.smn-thumb', function (event) {
                    //console.log($(this).hasClass('selected'));
                    if ($(this).hasClass('selected')) {
                        $(".smn-thumb").removeClass('selected');
                        $mediaUrl.val('');
                        $mediaInfo.data = JSON.parse($(this).find('img').attr('data-content'));
                        toggleBtn($mediaBtn, false);
                    } else {
                        $(".smn-thumb").removeClass('selected');
                        $(this).addClass('selected');
                        $mediaUrl.val(JSON.parse($(this).find('img').attr('data-content')));
                        $mediaInfo.data = JSON.parse($(this).find('img').attr('data-content'));
                        toggleBtn($mediaBtn, true);
                    }
                });

                $mediaSearch.keyup(function (event) {
                    $mediaInfo.keyword = $(this).val();
                    $mediaFiles.attr('data-page', '1');
                    $.get(_baseUri + '/admin/media/manager/getMedia/', {keyword: $mediaInfo.keyword}, function (result) {
                        var append = '';
                        if (result.code) {
                            for (var i = 0; i < result.data.length; i++) {
                                var tmp = processMediaBeforeDisplay(result.data[i]);
                                append += '<li class="smn-thumb"><div class="smn-nav"><i class="glyphicon glyphicon-ok"></i></div><img src="' + _baseUri + tmp.image_display + '" alt="' + tmp.title + '" data-content=\'' + JSON.stringify(tmp) + '\'></li>';
                            }
                        }
                        $('#smn-attachments').html(append);
                    }, 'JSON');
                });

                $mediaBtn.click(function (event) {
                    //event.preventDefault();
                    $mediaItems.removeClass('selected');
                    deferred.resolve($mediaInfo);
                    $mediaDialog.modal('hide');
                });
            }).one('hidden.bs.modal', function () {
                $mediaDialog.off();
                $mediaFiles.off();
            }).modal('show');
        });
    };

    /**
     * @class plugin.media
     *
     * Media Plugin
     *
     * media plugin is to make embeded media tag.
     *
     * ### load script
     *
     * ```
     * < script src="plugin/summernote-ext-media.js"></script >
     * ```
     *
     * ### use a plugin in toolbar
     * ```
     *    $("#editor").summernote({
     *    ...
     *    toolbar : [
     *        ['group', [ 'media' ]]
     *    ]
     *    ...
     *    });
     * ```
     */
    $.summernote.addPlugin({
        /**
         * @property {String} name name of plugin
         */
        name: 'media',
        /**
         * @property {Object} buttons
         * @property {function(object): string} buttons.media
         */
        buttons: {
            media: function (lang, options) {
                return tmpl.iconButton(options.iconPrefix + 'picture-o', {
                    event: 'showMediaDialog',
                    title: lang.media.media,
                    hide: true
                });
            }
        },

        /**
         * @property {Object} dialogs
         * @property {function(object, object): string} dialogs.media
         */
        dialogs: {
            media: function (lang) {
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
                    '<div class="zcms-media-library-search-bar"><input class="zcms_media_keyword pull-right col-md-6" style="margin:5px 0;" placeholder="' + lang.media.search + '" name="media_keyword"><div class="clearfix"></div></div>' +
                    '<div class="zcms-media-files">' +
                    '<ul id="smn-attachments">';

                $.get(_baseUri + '/admin/media/manager/getMedia/', function (result) {

                    var append = '';
                    if (result.code) {
                        for (var i = 0; i < result.data.length; i++) {
                            var tmp = processMediaBeforeDisplay(result.data[i]);
                            append += '<li class="smn-thumb"><div class="smn-nav"><i class="glyphicon glyphicon-ok"></i></div><img src="' + _baseUri + tmp.image_display + '" alt="' + tmp.title + '" data-content=\'' + JSON.stringify(tmp) + '\'></li>';
                        }
                    }
                    $('#smn-attachments').html(append);
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
                var footer = '<button type="button" href="#" class="btn btn-primary note-media-btn disabled" disabled>' + lang.media.insert + '</button>';
                return tmpl.dialog('zcms-main-media-dialog', lang.media.insert, body, footer);
            }
        },
        /**
         * @property {Object} events
         * @property {Function} events.showMediaDialog
         */
        events: {
            showMediaDialog: function (event, editor, layoutInfo) {
                var $dialog = layoutInfo.dialog(),
                    $editable = layoutInfo.editable();

                // save current range
                editor.saveRange($editable);

                //show dialog
                showMediaDialog($editable, $dialog).then(function (info) {
                    var data = processMediaBeforeInsert(info.data);
                    editor.restoreRange($editable);
                    editor.insertNode($editable, data);
                }).fail(function () {
                    editor.restoreRange($editable);
                });
            }
        },

        // define language
        langs: {
            'en-US': {
                media: {
                    media: 'Media',
                    search: 'Search...',
                    mediaLink: 'Media Link',
                    insert: 'Insert Media',
                    url: 'Media URL?',
                    providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion or Youku)'
                }
            },
            'vi-VN': {
                media: {
                    media: 'Media',
                    search: 'Search...',
                    mediaLink: 'Media Link',
                    insert: 'Insert Media',
                    url: 'Media URL?',
                    providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion or Youku)'
                }
            }
        }
    });
}));
