{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-body buttons-widget">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ form.label('title') }}
                                    {{ form.render('title') }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ form.label('alias') }}
                                    {{ form.render('alias') }}
                                </div>
                            </div>
                            <div class="col-md-6">
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

                            <div class="col-md-12">
                                {{ form.label('description') }}
                                {{ form.render('description') }}
                            </div>
                        </div>
                        {{ form.getSeoFormHTML(true, 'col-md-5') }}
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
