{% extends "../../index.volt" %}
{% block content %}
<div class="content">
    <form action="" method="post" id="adminForm" enctype="multipart/form-data">
        <div class="box">
            <div class="box-body table-responsive">
                <label for="template">{{ __("m_template_choose_template_to_install") }}</label>
                {{ file_field("template") }}
            </div>
        </div>
    </form>
</div>
{% endblock %}