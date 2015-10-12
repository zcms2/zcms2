{% set _messageFlashSession = flashSession.getMessages() %}
{% if _messageFlashSession|length >0 %}
    <!-- Flash session -->
    {% set _classExtra = [ "warning" : "alert-warning", "notice" : "alert-warning", "success" : "alert-success", "error" : "alert-danger" ] %}
    {% set _iconExtra = [ "warning" : "fa fa-exclamation-triangle", "notice" : "fa fa-info-circle", "success" : "fa fa-check-circle", "error" : "fa fa-times-circle" ] %}
    <div class="row">
        {% for key, item in _messageFlashSession %}
            {% for childItem in item %}
                <div class="alert {{ _classExtra[key] }}">
                    <button data-dismiss="alert" class="close">×</button>
                    <i class="{{ _iconExtra[key] }}"></i>
                    <strong>{{ __('gb_flash_session_' ~ key) }}:</strong> {{ __(childItem) }}
                </div>
            {% endfor %}
        {% endfor %}
    </div>
    <!-- End Flash session -->
    <div class="clearfix"></div>
{% endif %}