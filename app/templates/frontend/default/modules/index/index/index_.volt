{% include "../../header.volt" %}
{% block content %}
    <h1>Welcome to ZCMS - Overwrite by Default template!</h1>
    <h2>{{ 'gb_published' | t }}</h2>
    {{ get_sidebar("sidebar_left") }}
{% endblock %}
{% include "../../footer.volt" %}