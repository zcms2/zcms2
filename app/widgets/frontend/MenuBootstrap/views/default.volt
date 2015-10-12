<nav class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false"><span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button>
            <a href="{{ _baseUri }}" class="navbar-brand">ZCMS</a>
        </div>
        <div id="bs-navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                {% for item in menu_items_left %}
                    {% if item['children']|length %}
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ item['name'] }} <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                {% for childItem in item['children'] %}
                                    {% if childItem['children']|length %}
                                        <li class="dropdown-submenu">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ childItem['name'] }} <span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                {% for childItemLv2 in childItem['children'] %}
                                                    <li><a href="{{ childItemLv2['link'] }}">{{ childItemLv2['name'] }}</a></li>
                                                {% endfor %}
                                            </ul>
                                        </li>
                                    {% else %}
                                        <li><a href="{{ childItem['link'] }}">{{ childItem['name'] }}</a></li>
                                    {% endif %}
                                {% endfor %}
                            </ul>
                        </li>
                    {% else %}
                        <li><a href="{{ item['link'] }}">{{ item['name'] }}</a></li>
                    {% endif %}
                {% endfor %}
            </ul>
            {% if menu_items_right|length %}
                <ul class="nav navbar-nav navbar-right">
                    {% for item in menu_items_right %}
                        {% if item['children']|length %}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ item['name'] }} <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    {% for childItem in item['children'] %}
                                        {% if childItem['children']|length %}
                                            <li class="dropdown-submenu">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ childItem['name'] }} <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    {% for childItemLv2 in childItem['children'] %}
                                                        <li><a href="{{ childItemLv2['link'] }}">{{ childItemLv2['name'] }}</a></li>
                                                    {% endfor %}
                                                </ul>
                                            </li>
                                        {% else %}
                                            <li><a href="{{ childItem['link'] }}">{{ childItem['name'] }}</a></li>
                                        {% endif %}
                                    {% endfor %}
                                </ul>
                            </li>
                        {% else %}
                            <li><a href="{{ item['link'] }}">{{ item['name'] }}</a></li>
                        {% endif %}
                    {% endfor %}
                </ul>
            {% endif %}
        </div>
    </div>
</nav>