{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td width="20%"><label for="name">{{ __('Role Name') }}</label></td>
                                <td>{{ text_field("name", "class":"form-control", "autocomplete":"off", "required":"required") }}</td>
                            </tr>
                            <tr>
                                <td width="20%"><label>Location</label></td>
                                <td>{{ select_static('location', ['0' : 'Frontend', '1' : 'Backend'], 'class' : 'form-control', 'value' : '' ) }}</td>
                            </tr>
                            <tr>
                                <td width="20%"><label for="name">{{ __('Is default') }}</label></td>
                                <td>{{ select('is_default',['0' : __('gb_no'), '1' : __('gb_yes')],'class' : 'form-control') }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <h4>Resource</h4>

                        <div id="tree_view"></div>
                        {{ hidden_field("admin_rules") }}
                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <script type="text/javascript">
        var UITreeView = function () {
            var runTreeView = function () {
                // Get data from controller
                var treeData = {{ roles }};
                // Get data before submit form
                var edit_rules = $("#admin_rules").val().split(",");
                console.log(treeData);
                // Init tree view
                $("#tree_view").dynatree({
                    checkbox: true,
                    selectMode: 3,
                    children: treeData,
                    onRender: function (node) {
                        var current_rules = node.data.key;
                        if ($.inArray(current_rules, edit_rules) != -1) {
                            node.select(true);
                        }
                    },
                    onSelect: function (select, node) {
                        // Save selection
                        var selectedNodes = node.tree.getSelectedNodes();
                        var selectedKeys = $.map(selectedNodes, function (node) {
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
                        console.log(actionKeys);
                        $("#admin_rules").val(actionKeys);
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
            UITreeView.init();
        });
    </script>
{% endblock %}


