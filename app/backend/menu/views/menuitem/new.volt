{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm"
                              novalidate="novalidate" class="form-horizontal" enctype="multipart/form-data">
                            <div class="form-group">
                                {{ form.label('name', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render('name') }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ form.label('link', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("link") }}
                                    <br/>
                                    <a data-target="#ajax-modal-menu"
                                       href="{{ _baseUri }}/admin/menu/router/menu/"
                                       data-toggle="modal" class="btn btn-success btn-sm"><span
                                                class="fa fa-link"></span> Choose Menu</a>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ form.label('thumbnail', ['class' : 'col-sm-2']) }}

                                <div class="col-sm-9">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <input type="hidden" value="" name="thumbnail_images">

                                        <div class="fileupload-new thumbnail" style="height: 150px;">
                                            {% if item is defined and item.thumbnail|length %}
                                                <img src="{{ _baseUri ~ item.thumbnail }}" alt="">
                                            {% else %}
                                                <img src="{{ _baseUri }}/media/default/no-image.png" alt="">
                                            {% endif %}
                                        </div>
                                        <div class="fileupload-preview fileupload-exists thumbnail"
                                             style="height: 150px; line-height: 10px;"></div>
                                        <div>
														<span class="btn btn-light-grey btn-file"><span
                                                                    class="fileupload-new"><i
                                                                        class="fa fa-picture-o"></i> Select image</span><span
                                                                    class="fileupload-exists"><i
                                                                        class="fa fa-picture-o"></i> Change</span>
															<input type="file" accept="image/*" name="thumbnail">
														</span>
                                            <a href="#" class="btn fileupload-exists btn-light-grey"
                                               data-dismiss="fileupload">
                                                <i class="fa fa-times"></i> Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                {{ form.label('published', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("published") }}
                                </div>
                            </div>

                            <div class="form-group">
                                {{ form.label('require_login', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("require_login") }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ form.label('class', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("class") }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ form.label('icon', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("icon") }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ajax-modal-menu" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div id="ajax-modal-menu-detail" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            </div>
        </div>
    </div>

{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        var modalTitle = '';
        $(function () {
            $(document).on('click', 'a.select-menu-item', function () {
                $('#ajax-modal-menu').modal('hide');
                modalTitle = $(this).parent().parent().prev().find('a').text() + ' / ' + $(this).html();
            });

            $(document).on('click', '.select-menu-item-link-fixed', function () {
                $('#link').val($(this).attr('data-content'));
                $('#ajax-modal-menu').modal('hide');
                return false;
            });

            $(document).on('click', '.select-menu-link', function () {
                $('#link').val($(this).attr('data-content'));
                $('#ajax-modal-menu-detail').modal('hide');
            });

            $(document).on('click', '.pagination a', function () {
                var page = $(this).attr('data-content');
                $('#ajax-modal-menu-detail .modal-content').load(page, function () {
                    $('#ajax-modal-menu-detail h4.modal-title').html(modalTitle);
                });
            });

            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });

            $('#ajax-modal-menu-detail').on('loaded.bs.modal', function () {
                $('#ajax-modal-menu-detail h4.modal-title').html(modalTitle);
            });

            $('body').on('click', '#search-menu', function () {
                var modalTitleAjax = $('#ajax-modal-menu-detail h4.modal-title').text();
                var url = encodeURI($('#search-form').attr('action') + '/1/' + $('#filter_search').val());
                var contailter_class = "#ajax-modal-menu-detail .modal-content";
                var container_modal = getModalContainer(contailter_class);
                $.ajax({
                    url: url,
                    type: "POST",
                    success: function (html) {
                        $(contailter_class).empty();
                        $(contailter_class).append(html);
                        $('#ajax-modal-menu-detail h4.modal-title').html(modalTitleAjax);
                        $(contailter_class).unblock();
                    },
                    fail: function (jqXHR, textStatus) {
                        alert("Request failed: " + textStatus);
                    }
                });
                return false;
            });
        });

        function getModalContainer(container) {
            var el = $(container);
            el.block({
                overlayCSS: {
                    backgroundColor: '#fff'
                },
                message: '<img src="{{ _baseUri }}/images/tmp/loading.gif" /> {{ __('gb_loading') }}...',
                css: {
                    border: 'none',
                    color: '#333',
                    background: 'none'
                }
            });
            return el;
        }

    </script>
{% endblock %}
