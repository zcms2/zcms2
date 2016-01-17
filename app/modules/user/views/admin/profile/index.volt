{% extends '../../../index.volt' %}
{% block content %}
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm"
                              novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ form.label('first_name') }}
                                        {{ form.render('first_name') }}
                                    </div>
                                    <div class="form-group">
                                        {{ form.label('last_name') }}
                                        {{ form.render('last_name') }}
                                    </div>
                                    <div class="form-group">
                                        {{ form.label('email') }}
                                        {{ form.render('email') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ form.label('current_password') }}
                                        {{ form.render('current_password') }}
                                    </div>
                                    <div class="form-group">
                                        {{ form.label('password') }}
                                        {{ form.render('password') }}
                                    </div>
                                    <div class="form-group">
                                        {{ form.label('password_confirmation') }}
                                        {{ form.render('password_confirmation') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="control-label">
                                        {{ __('m_user_help_text_click_image_upload_new_avatar_avatar_must_square') }}
                                    </label>

                                    <div class="" style="cursor: pointer" onclick="openFileBrowser(this)"><img
                                                src="{{ _baseUri }}{% if avatar is defined and avatar|length > 0 %}{{ avatar }}{% else %}{{ _baseUri }}/images/tmp/select-image.png{% endif %}"
                                                style="width: 128px; height: 128px; border: 1px solid #c0c0c0; padding: 2px; border-radius: 3px">
                                    </div>
                                    <input type="file" name="avatar" style="display: none" onchange="readURL(this)">
                                </div>
                                <div class="col-md-6">{{ __('m_user_help_text_if_you_want_change_password_please_insert_old_and_new_password') }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        function openFileBrowser(div) {
            $(div).next('input[type="file"]').trigger('click');
        }
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(input).prev().children('img').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
{% endblock %}