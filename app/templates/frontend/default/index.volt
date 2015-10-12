{% include _header %}
<!-- Main menu and slide Show -->
{% block sidebar_top %}
    <div class="container">
        {{ get_sidebar('sidebar_top') }}
    </div>
{% endblock %}
<!-- END Main menu and slide show -->

<!-- END Flash Session -->
{% block content %}
{% endblock %}
{% include _footer %}