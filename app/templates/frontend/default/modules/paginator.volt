{% if page.total_pages > 1 %}
    {% set startIndex = 1 %}

    {% if page.total_pages > 6 %}
        {% if page.current > 4 %}
            {% set startIndex = startIndex + page.current - 4 %}
        {% endif %}
        {% if page.total_pages - page.current < 6 %}
            {% set startIndex = page.total_pages - 5 %}
        {% endif %}
    {% endif %}
    <div class="span9">
        <div class="paging">
            {% if page.current > 1 %}
                <a href="{{ _baseUri ~ router.getRewriteUri() ~ _getVariable ~ _connectVariable }}">First</a>
                <a href="{{ _baseUri ~ router.getRewriteUri() ~ _getVariable ~ _connectVariable }}page={{page.before}}"><<</a>
            {% endif %}

            {% for pageIndex in startIndex..page.total_pages %}
                {% if pageIndex is startIndex+6 %}
                    {% break %}
                {% endif %}

                {% if pageIndex is page.current %}
                    <span class="current">{{ pageIndex }}</span>
                {% else %}
                    <a href="{{ _baseUri ~ router.getRewriteUri() ~ _getVariable ~ _connectVariable }}page={{pageIndex}}">{{ pageIndex }}</a>
                {%  endif %}
            {% endfor %}

            {% if page.current < page.total_pages %}
                <a href="{{ _baseUri ~ router.getRewriteUri() ~ _getVariable ~ _connectVariable }}page={{page.next}}"> >> </a>
                <a href="{{ _baseUri ~ router.getRewriteUri() ~ _getVariable ~ _connectVariable }}page={{page.last}}">Last</a>
            {% endif %}
        </div>
    </div>
{% endif %}