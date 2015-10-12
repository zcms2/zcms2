{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-body buttons-widget">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ form.label('title')}}
                                    {{ form.render('title') }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ form.label('alias')}}
                                    {{ form.render('alias') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ form.label('published')}}
                                    {{ form.render('published') }}
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12">
                                {{ form.label('description')}}
                                {{ form.render('description') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="control-label"> {{ __('m_slide_form_slide_show_form_image') }}</label>

                                <div class="clearfix    "></div>
                                <div style="float: left">
                                    <div class="image_preview" onclick="openFileBrowser(this)"><img
                                                src="{% if slideShow is defined and slideShow.image|length %}{{ _baseUri }}/{{ slideShow.image }}{% else %}{{ _baseUri }}/media/default/select-image.png{% endif %}"
                                                style="width: 250px; border: 1px solid #c0c0c0; padding: 2px; border-radius: 3px">
                                    </div>
                                    <input type="file" name="image" style="display: none" onchange="readURL(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        $(function () {
            $('#bug_tracking_type_id').change(function () {
                if ($(this).val() != '') {

                    $('.btn.btn-primary.btn-sm').html('<span class="glyphicon glyphicon-floppy-saved"></span> Save ' + $(this).find('option:selected').html());
                }
            });
        });
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

        // Override submit function
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
                        $(this).parent().append('<span class="help-block">{{ __('gb_form_this_field_is_required') }}</span>');
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
    </script>
{% endblock %}
