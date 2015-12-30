{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-body buttons-widget">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                {{ form.label('title') }}
                                {{ form.render('title') }}
                            </div>
                            <div class="form-group">
                                {{ form.label('alias') }}
                                {{ form.render('alias') }}
                            </div>
                            <div class="col-md-6 row">
                                <div class="form-group">
                                    {{ form.label('category_id') }}
                                    {{ form.render('category_id') }}
                                </div>
                                <div class="form-group">
                                    {{ form.label('published') }}
                                    {{ form.render('published') }}
                                </div>
                            </div>
                            <div class="col-md-6 row">

                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                                {{ form.label('intro_text') }}
                                {{ form.render('intro_text') }}
                            </div>

                            <div class="form-group">
                                {{ form.label('full_text') }}
                                {{ form.render('full_text') }}
                            </div>
                        </div>
                        {{ form.getSeoFormHTML(true, 'col-md-3') }}
                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <link href="{{ _baseUri }}/plugins/summernote/dist/summernote.css" rel="stylesheet">
    <link href="{{ _baseUri }}/plugins/summernote/plugin/summernote-ext-media.css" rel="stylesheet">
    <script src="{{ _baseUri }}/plugins/summernote/dist/summernote.min.js"></script>
    <script src="{{ _baseUri }}/plugins/summernote/plugin/summernote-ext-media.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.summernote').summernote({
                height: 155,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'hr', 'media', 'video']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]
                ]
            });
        });
    </script>
{% endblock %}
