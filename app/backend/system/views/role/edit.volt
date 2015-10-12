{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">
            <div class="box">
                <div class="box-body table-responsive">
                    <div class="table-responsive">
                        {{ flashSession.output() }}
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td width="20%"><label>{{ __('m_system_role_form_name') }}</label></td>
                                <td>{{ text_field('name', 'class':'form-control', 'value':edit_data.name, 'autocomplete':'off', 'required':'required') }}</td>
                            </tr>
                            <tr>
                                <td width="20%"><label for="name">{{ __('Is default') }}</label></td>
                                <td>{{ select('is_default',['0' : __('gb_no'), '1' : __('gb_yes')],'class' : 'form-control', 'value' : edit_data.is_default) }}</td>
                            </tr>
                            <tr>
                                <td width="20%"><label>Location</label></td>
                                <td>{{ select_static('location', ['0' : 'Frontend', '1' : 'Backend'], 'class' : 'form-control', 'value' : edit_data.location ) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6"><h4>{{ __('m_admin_system_help_text_resource') }}</h4></div>
                        <div class="col-md-6 text-right">
                            <button type="button"
                                    class="btn btn-success btn-sm role_collapse_all">{{ __('m_admin_system_help_text_collapse_all') }}</button>
                            <button type="button"
                                    class="btn btn-warning btn-sm role_expand_all">{{ __('m_admin_system_help_text_expand_all') }}</button>
                        </div>

                        <div id="tree_view" class="col-md-12"></div>
                        {{ hidden_field("admin_rules") }}
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr />
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        var UITreeview = function () {
            //function to initiate jquery.dynatree
            var runTreeView = function () {

                // Get data from controller
                var treeData = {{ roles }};

                // Init tree view
                jQuery("#tree_view").dynatree({
                    checkbox: true,
                    selectMode: 3,
                    children: treeData,
                    debugLevel: 0,
                    onCreate: function (node) {
                        var edit_rules = [{{ edit_rules_id }}];
                        var current_rules = parseInt(node.data.key);
                        if (jQuery.inArray(current_rules, edit_rules) != -1) {
                            node.select(true);
                        }
                    },
                    onSelect: function (select, node) {
                        // Save selection
                        var selectedNodes = node.tree.getSelectedNodes();
                        var selectedKeys = jQuery.map(selectedNodes, function (node) {
                            return node.data.key;
                        });
                        var actionKeys = "";
                        for (var i = 0; i < selectedKeys.length; i++) {
                            if (selectedKeys[i] > 0) {
                                actionKeys += selectedKeys[i] + ",";
                            }
                        }
                        // Delete last comma
                        actionKeys = actionKeys.slice(0, -1);

                        jQuery("#admin_rules").val(actionKeys);
                    },
                    onClick: function (node, event) {
                        // We should not toggle, if target was "checkbox", because this
                        // would result in double-toggle (i.e. no toggle)
                        if (node.getEventTargetType(event) == "title")
                            node.toggleSelect();
                    },
                    onKeydown: function (node, event) {
                        if (event.which == 32) {
                            node.toggleSelect();
                            return false;
                        }
                    }
                });
            };
            return {
                //main function to initiate template pages
                init: function () {
                    runTreeView();
                }
            };
        }();

        $(function () {
            // Init tree view
            UITreeview.init();
            $("#tree_view").dynatree("getRoot").visit(function (node) {
                node.expand(false);
            });
            $(".role_collapse_all").click(function () {
                $("#tree_view").dynatree("getRoot").visit(function (node) {
                    node.expand(false);
                });
            });
            $(".role_expand_all").click(function () {
                $("#tree_view").dynatree("getRoot").visit(function (node) {
                    node.expand(true);
                });
            });
        });
    </script>
{% endblock %}


