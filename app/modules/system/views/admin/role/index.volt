{% extends '../../../index.volt' %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row cms-toolbar-helper"></div>
                    {% include _standardTable %}
                </div>
            </div>
        </form>
    </div>
{% endblock %}
