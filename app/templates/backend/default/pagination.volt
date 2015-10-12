<div class="row">
    <div class="col-xs-6">
        {% if _page.total_pages %}
            <div class="dataTables_info">{{ __("gb_you_are_in_current_page_per_page", ["1" : _page.current, "2" : _page.total_pages]) }}</div>
        {% else %}
            <div class="dataTables_info">{{ __("gb_you_are_in_current_page_per_page", ["1" : 0, "2" : _page.total_pages]) }}</div>
        {% endif %}
    </div>
    <div class="col-xs-6">
        {% if _page.total_pages > 1 %}
            {% set startIndex = 1 %}

            {% if _page.total_pages > 6 %}
                {% if _page.current > 4 %}
                    {% set startIndex = startIndex + _page.current - 4 %}
                {% endif %}
            {% if _page.total_pages - _page.current < 6 %}
                {% set startIndex = _page.total_pages - 5 %}
            {% endif %}
        {% endif %}
        <div class="dataTables_paginate paging_bootstrap">
            <ul class="pagination">
                {% if _page.current > 1 %}
                    <li><a href="{{ _baseUri }}{{ router.getRewriteUri() }}">First</a></li>
                    <li><a href="{{ _baseUri }}{{ router.getRewriteUri() }}?page={{_page.before}}">{{ __('gb_prev') }}</a></li>
                {% endif %}

                {% for pageIndex in startIndex.._page.total_pages %}
                    {% if pageIndex is startIndex+6 %}
                        {% break %}
                    {% endif %}

                    <li {% if pageIndex is _page.current %} class="active" {% endif %} >
                        <a href="{{ _baseUri }}{{ router.getRewriteUri() }}?page={{pageIndex}}">{{ pageIndex }}</a>
                    </li>
                {% endfor %}

                {% if _page.current < _page.total_pages %}
                    <li><a href="{{ _baseUri }}{{ router.getRewriteUri() }}?page={{_page.next}}">{{ __('gb_next') }}</a></li>
                    <li><a href="{{ _baseUri }}{{ router.getRewriteUri() }}?page={{_page.last}}">{{ __('gb_last') }}</a></li>
                {% endif %}
            </ul>
        </div>

        {% endif %}
    </div>
</div>
