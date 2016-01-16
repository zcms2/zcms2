{% extends '../../../index.volt' %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">
            <div class="panel panel-default">
                <div class="panel-body">
                    {% include _standardTable %}
                </div>
            </div>
        </form>
    </div>
{% endblock %}