{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <div class="col-sm-6 widget-main" style="padding-left: 0; padding-right: 0">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('m_template_widget_widget_available') }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body panel-available-widget">
                    <div class="box available-widget">
                        {{ widget_html }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 sidebar-main" style="padding-right: 0">
            {{ sidebar_html }}
        </div>
    </div>
    <div class="clearfix"></div>

    <div id="static" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false"
         style="display: none; margin-top: -78.5px;" aria-hidden="true">
        <div class="modal-body">
            <h4>
                Do you want delete this widget?
            </h4>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">
                {{ __('gb_cancel') }}
            </button>
            <button type="button" data-dismiss="modal" class="btn btn-warning btn-sm">
                {{ __('gb_continue_delete') }}
            </button>
        </div>
    </div>

{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        $(function () {
            $(".available-widget .widget_active").draggable({
                connectToSortable: ".zsidebar-content",
                helper: "clone",
                revert: "invalid"
            });

            $("{{ sortable }}").sortable({
                connectWith: ".zsidebar-content"
            }).disableSelection();

            $(".zsidebar-content").sortable({
                placeholder: "portlet-placeholder ui-corner-all",
                update: function (event, ui) {
                    var sidebar_name = ui.item.parents().parents().attr('data-content');
                    var indexNewItem = ui.item.index();
                    var widget_class = ui.item.attr('data-content');
                    if ($(".zsidebar_" + sidebar_name + " .widget_active").eq(indexNewItem).hasClass('widget_new')) {
                        $(".zsidebar_" + sidebar_name + " .zsidebar-content .widget_active").eq(indexNewItem).remove();
                        var zdata = {widget_class: widget_class, index: (indexNewItem + 1), sidebar_name: sidebar_name};
                        console.log(zdata);
                        $.ajax({
                            url: "{{ _baseUri }}/admin/template/sidebar/addNewWidget/",
                            type: 'POST',
                            data: zdata,
                            success: function (html) {
                                insertWidget(sidebar_name, html, indexNewItem);
                            }
                        }).fail(function (jqXHR, textStatus) {
                            console.log('Cannot connect to server, Error code : ' + textStatus);
                        });
                    }
                },
                change: function (event, ui) {
                    if (!ui.item.hasClass('widget_new')) {

                    }
                },
                stop: function (event, ui) {
                    if (!ui.item.hasClass('widget_new')) {
                        var sidebar_name = ui.item.parents().parents().attr('data-content');
                        var zdata = {widget_id: ui.item.attr('data-content-id'), index: (ui.item.index() + 1), sidebar_name: sidebar_name};
                        $.ajax({
                            url: "{{ _baseUri }}/admin/template/sidebar/updateWidgetOrder/",
                            type: 'POST',
                            data: zdata,
                            success: function (html) {
                                //Do some thing
                            }
                        }).fail(function (jqXHR, textStatus) {
                            alert('Cannot connect to server. Try again later. Error code : ' + textStatus);
                        });
                    }
                }

            });

            $(".zsidebar").delegate('.widget-control-save', 'click', function () {
                var element = $(this);
                var sidebar_name = $(this).parents().parents().parents().parents().parents().parents().attr('data-content');
                var id = '#p-' + $(this).parents().parents().attr('id');
                var data = $(this).parents().parents().serialize();
                element.button('loading');
                $.ajax({
                    url: "{{ _baseUri }}/admin/template/sidebar/addSaveWidget/",
                    type: 'POST',
                    data: data,
                    success: function (html) {
                        insertWidget(sidebar_name, html);
                        setTimeout(function () {
                            element.button('complete');
                        }, 500);
                    }
                }).fail(function (jqXHR, textStatus) {
                    console.log('Cannot connect to server, Error code : ' + textStatus);
                    element.button('complete');
                });
                return false;
            });

            $(document).on("click", ".zwidget-title", function () {
                if ($(this).hasClass('widget-close')) {
                    $(this).removeClass('widget-close').addClass('widget-open');
                    $(this).next().slideDown();
                } else {
                    if ($(this).hasClass('widget-open')) {
                        $(this).removeClass('widget-open').addClass('widget-close');
                        $(this).next().slideUp();
                    }
                }

            });

            $('.zsidebar').delegate('.widget-control-delete', 'click', function () {
                var r = confirm("Do you want delete item(s) ?");
                if (r == true) {
                    var id = '#p-' + $(this).parents().parents().attr('id');
                    var data = $(this).parents().parents().serialize();
                    $.ajax({
                        url: "{{ _baseUri }}/admin/template/sidebar/deleteWidget/",
                        type: 'POST',
                        data: data,
                        success: function (html) {
                            if (html == '1') {
                                $(id).remove();
                            }
                        }
                    }).fail(function (jqXHR, textStatus) {
                        alert('Cannot connect to server, Error code : ' + textStatus);
                    });
                }
                return false;
            });

            $('.zsidebar').delegate('.zForm form', 'keypress', function (e) {
                if (e.which == 13) {
                    return false;
                }
                return true;
            });
        });

        function insertWidget(sidebar_name, html, index) {
            if ($(".zsidebar_" + sidebar_name + " .zsidebar-content .widget_active").length > 0) {
                var obj;
                if (index == 0) {
                    obj = $(".zsidebar_" + sidebar_name + " .zsidebar-content .widget_active").eq(0);
                    $(html).insertBefore(obj);
                } else if (index > 0) {
                    obj = $(".zsidebar_" + sidebar_name + " .zsidebar-content .widget_active").eq(index - 1);
                    $(html).insertAfter(obj);
                }
            } else {
                $(".zsidebar_" + sidebar_name + " .zsidebar-content").append(html);
            }
        }
    </script>
{% endblock %}