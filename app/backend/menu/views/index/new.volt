{% extends "../../index.volt" %}
{% block content %}
    {%- macro draw_children(dic_menus,children) %}
        <ol class="dd-list">
            {% for child in children %}
                <li class="dd-item" data-id="{{ child.menu_item_id }}">
                    <div class="dd-handle">

                        {{ dic_menus[child.menu_item_id] }}

                    </div>
                    {% if child.children is defined %}
                        {{ draw_children(dic_menus, child.children) }}
                    {% endif %}
                </li>
            {% endfor %}
        </ol>
    {%- endmacro %}
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm"
                              novalidate="novalidate" class="form-horizontal">
                            <div class="form-group">
                                {{ form.label('name', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("name") }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ form.label('description', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("description") }}
                                </div>

                            </div>
                            <div class="form-group">
                                {{ form.label('published', ['class' : 'col-sm-2']) }}
                                <div class="col-sm-9">
                                    {{ form.render("published") }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">
                                </label>

                                <div class="col-sm-12 no-padding-lr">
                                    <div class="col-sm-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-reorder"></i>
                                                {{ __('m_admin_menu_menuitem_index') }}
                                            </div>
                                            <div class="panel-body">
                                                <div class="dd" id="menu-items">
                                                    {% if menu_items|length %}
                                                        <ol class="dd-list" id="selectMenu">

                                                            {% for child in menu_items %}
                                                                <li class="dd-item" data-id="{{ child.menu_item_id }}">
                                                                    <div class="dd-handle">
                                                                        {{ child.name }}
                                                                    </div>
                                                                </li>
                                                            {% endfor %}
                                                        </ol>
                                                    {% else %}
                                                        <div class="dd-empty"></div>
                                                    {% endif %}
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-reorder"></i>
                                                {{ __('m_menu_form_menu_type_selected_menu') }}
                                            </div>
                                            <div class="panel-body">
                                                <div class="dd" id="nestable">
                                                    {% if menu_details is defined and menu_details|length %}
                                                        <ol class="dd-list" id="rootMenu">
                                                            {% for child in menu_details %}
                                                                <li class="dd-item"
                                                                    data-id="{{ child.menu_item_id }}">
                                                                    <div class="dd-handle">
                                                                        {{ dic_menus[child.menu_item_id] }}
                                                                    </div>
                                                                    {% if child.children is defined %}
                                                                        {{ draw_children(dic_menus, child.children) }}
                                                                    {% endif %}
                                                                </li>

                                                            {% endfor %}

                                                        </ol>
                                                    {% else %}
                                                        <div class="dd-empty"></div>
                                                    {% endif %}
                                                </div>
                                            </div>
                                            {{ hidden_field("nestable-output") }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>


{% endblock %}
{% block js_footer %}
    <script>
        jQuery(document).ready(function () {
            UINestable.init();
        });

        function SeletedMenuItem() {
            var checkboxes = document.getElementsByName("ids[]");
            var checkboxesChecked = [];
            // loop over them all
            for (var i = 0; i < checkboxes.length; i++) {
                // And stick the checked ones onto an array...
                if (checkboxes[i].checked) {
                    checkboxesChecked.push(checkboxes[i].defaultValue);
                    checkboxes[i].checked = false;
                }
            }
            $(".dd-item").each(function (index) {
                for (var i = 0; i < checkboxesChecked.length; i++) {
                    if (checkboxesChecked[i] == $(this).attr("data-id")) {
                        checkboxesChecked[i] = -1;
                    }
                }
            });
            var checkAll = $(".check_element");
            checkAll.prop("checked", false);
            checkAll.each(function () {
                jQuery(this).parent().removeClass("checked").attr("aria-checked", "false");
            });
            if (checkboxesChecked.length == 0) return;
            $.ajax({
                url: "{{ _baseUri }}/admin/menu/index/getMenuItem",
                type: "POST",
                data: {ids: checkboxesChecked.join()},
                success: function (re) {
                    console.log(re);
                    for (var i = 0; i < re.length; i++) {
                        $("#rootMenu").append('<li class="dd-item" data-id="' + re[i].id + '"><div class="dd-handle">' + re[i].name + '</div></li>');
                    }
                    var dataJson = $('.dd').nestable('serialize');
                    $("#nestable-output").val(JSON.stringify(dataJson));
                }
            });
        }
        function removeMenu(e) {
            if (confirm("Do you want delete to this menu?")) {
                $(e).parent().parent().remove();
            }
        }
    </script>
{% endblock %}