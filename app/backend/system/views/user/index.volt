{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">

            {{ hidden_field("filter_order", "value" : _filter['filter_order']) }}
            {{ hidden_field("filter_order_dir", "value" : _filter['filter_order_dir']) }}

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row zcms-toolbar-helper">
                        <div class="col-md-5">
                            <div class="dataTables_length">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Search</span>
                                    {{ text_field("filter_search", "value": _filter['filter_search'], "class" : "form-control input-sm", "placeholder" : __("m_system_user_form_search_placeholder")) }}
                                    <span class="input-group-btn">
                                        <input type="reset" class="btn btn-danger btn-sm"
                                               value="{{ __('gb_reset_button') }}" onclick="return ZCMS.resetFilter();">
                                        {{ submit_button("go", "value": __('gb_go_button'), "class" : "btn btn-primary btn-sm") }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="dataTables_filter">
                                <label>{{ select_static("filter_role", rolesData, "value" : _filter['filter_role'], "class" : "form-control input-sm", "onchange" : "ZCMS.submitForm()") }}</label>
                            </div>
                        </div>
                    </div>
                    {% include _standardTable %}
                </div>
            </div>
        </form>
    </div>
{% endblock %}