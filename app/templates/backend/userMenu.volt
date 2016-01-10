<ul class="sidebar-menu">
    {% for item in _menu %}
        <li class="{% if item['items']|length %}treeview{% endif %}{% if _module == item['module'] %} active{% endif %}"><a class="{{ item['link_class'] }}" href="{{ _baseUri }}{{ item['link'] }}" {% if item['link_target'] %}target="{{ item['link_target'] }}"{% endif %}><i class="{{ item['icon_class'] }}"></i> <span>{{ __(item['menu_name']) }}</span>{% if item['items']|length %}<i class="fa fa-angle-left pull-right"></i>{% endif %}</a>
        {% if item['items']|length %}
            <ul class="treeview-menu">
                {% for childItem in item['items'] %}
                <li {% if _baseUri ~ router.getRewriteUri() == _baseUri ~ childItem['link'] %}class="active"{% endif %}><a href="{{ _baseUri }}{{ childItem['link'] }}" {% if item['link_target'] %}target="{{ item['link_target'] }}"{% endif %}><i class="{{ childItem['icon_class'] }}"></i> <span>{{ __(childItem['menu_name']) }}</span></a></li>
                {% endfor %}
            </ul>
        {% endif %}
        </li>
    {% endfor %}
</ul>
