<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myLargeModalLabel">Choose Menu</h4>
</div>
<div class="modal-body">
    <div class="panel-group accordion-custom accordion-teal">
        {% for item in menuModule %}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"
                           href="#collapseOne">
                            <i class="icon-arrow"></i>
                            {{ item['name'] }}
                        </a></h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" style="height: auto;">
                    <div class="panel-body">
                        {% for menu in item['items'] %}
                            {% if menu['type'] == 'default' %}
                            <a class="select-menu-item" data-target="#ajax-modal-menu-detail"
                               href="{{ _baseUri }}{{ menu['link'] }}"
                               data-toggle="modal">{{ menu['title'] }}</a>
                            <br/>
                        {% elseif menu['type'] == 'link' %}
                            <a class="select-menu-item-link-fixed" data-content="{{ menu['link'] }}"
                               href="javascript:void(0)"  >{{ menu['title'] }}</a>
                            <br/>
                        {% endif %}
                        {% endfor %}
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
</div>


