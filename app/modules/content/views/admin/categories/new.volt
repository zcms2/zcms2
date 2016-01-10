{% extends '../../../index.volt' %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Category</h3>

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
                                {{ form.label('alias') }}
                                {{ form.render('alias') }}
                            </div>
                            <div class="col-md-6 row">
                                <div class="form-group">
                                    {{ form.label('parent') }}
                                    {{ form.render('parent') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ form.label('published') }}
                                    {{ form.render('published') }}
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                                {{ form.label('description') }}
                                {{ form.render('description') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">SEO Meta</h3>

                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: block;">
                            {{ form.getSeoFormHTML(true, '') }}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <link href="{{ _baseUri }}/plugins/summernote/dist/summernote.css" rel="stylesheet">
    <script src="{{ _baseUri }}/plugins/summernote/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.summernote').summernote({
                height: 155
            });
        });
    </script>
{% endblock %}
