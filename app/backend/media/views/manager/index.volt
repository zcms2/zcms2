{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">

            {{ hidden_field("filter_order", "value" : _filter['filter_order']) }}
            {{ hidden_field("filter_order_dir", "value" : _filter['filter_order_dir']) }}
            <div class="panel panel-default">
                <div class="panel-body">
                    {% include _standardTable %}
                </div>
            </div>
        </form>
    </div>
{% endblock %}