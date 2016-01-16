{% extends '../../../index.volt' %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Post</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: block;">
                            <div class="form-group">
                                {{ form.label('title') }}
                                {{ form.render('title') }}
                            </div>

                            <div class="form-group">
                                {{ form.label('full_text') }}
                                {{ form.render('full_text') }}
                            </div>

                            <div class="form-group">
                                {{ form.label('intro_text') }}
                                {{ form.render('intro_text') }}
                            </div>

                            <div class="form-group">
                                {{ form.label('alias') }}
                                {{ form.render('alias') }}
                            </div>
                        </div>
                    </div>
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">SEO Meta</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            {{ form.getSeoFormHTML(true, '') }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Publish</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: block;">
                            <div class="form-group">
                                {{ form.label('published') }}
                                {{ form.render('published') }}
                            </div>
                            <div class="form-group">
                                {{ form.label('category_id') }}
                                {{ form.render('category_id') }}
                            </div>
                            <div class="form-group">
                                {{ form.label('published_at') }}
                                {{ form.render('published_at') }}
                            </div>
                        </div>
                    </div>
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Featured Image</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body post-featured-image" style="display: block;">
                            {% if post is defined and post.image %}
                                <img onclick="ZCMS.selectMedia('image','#post-featured-image','#post-featured-image-preview')" id="post-featured-image-preview" src="{{ post.image }}" alt="Featured image">
                            {% else %}
                                <img onclick="ZCMS.selectMedia('image','#post-featured-image','#post-featured-image-preview')" id="post-featured-image-preview" src="{{ _baseUri }}/media/default/select-image.png" alt="Featured image">
                            {% endif %}
                            {{ form.render('image') }}
                        </div>
                    </div>
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Info</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        {% if post is defined %}
                            <div class="box-body" style="display: block;">
                                <div class="form-group">
                                    <p>
                                        <b><i class="fa fa-calendar"></i> {{ 'Created'|t }}:</b> <i>{{ post.created_at|view_date }}</i>
                                    </p>

                                    <p>
                                        <b><i class="fa fa-calendar"></i> {{ 'Updated'|t }}:</b> <i>{{ post.updated_at|view_date }}</i>
                                    </p>
                                </div>
                            </div>
                        {% endif %}

                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <link href="{{ _baseUri }}/plugins/summernote/dist/summernote.css" rel="stylesheet">
    <script src="{{ _baseUri }}/plugins/summernote/dist/summernote.min.js"></script>
    <script src="{{ _baseUri }}/plugins/summernote/plugin/summernote-ext-media.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.summernote').summernote({
                height: 355,
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

            $('.note-editable').click(function () {
                _mediaTarget = null;
            })
        });
    </script>
{% endblock %}
