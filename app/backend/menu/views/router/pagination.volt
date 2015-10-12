<div class="row cms-paginate" style="padding: 0 10px">
    <div class="col-md-6">
        <div class="dataTables_info">{{ __("gb_you_are_in_current_page_per_page", ["1" : page.current, "2" : page.total_pages]) }}</div>
    </div>
    <div class="col-md-6">
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
            <div class="dataTables_paginate paging_bootstrap" style="height: 35px">
                <ul class="pagination">
                    {% if page.current > 1 %}
                        <li><a data-content="{{ request_link }}{{ page.current }}{{ request_title }}">First</a></li>
                        <li>
                            <a data-content="{{ request_link }}{{ page.before }}{{ request_title }}"><i class="icon-double-angle-left"></i></a>
                        </li>
                    {% endif %}

                    {% for pageIndex in startIndex..page.total_pages %}
                        {% if pageIndex is startIndex+6 %}
                            {% break %}
                        {% endif %}

                        <li {% if pageIndex is page.current %} class="active" {% endif %} >
                            <a data-content="{{ request_link }}{{ pageIndex }}{{ request_title }}">{{ pageIndex }}</a>
                        </li>
                    {% endfor %}
                    {% if page.current < page.total_pages %}
                        <li>
                            <a data-content="{{ request_link }}{{ page.next }}{{ request_title }}"><i class="icon-double-angle-right"></i></a>
                        </li>
                        <li>
                            <a data-content="{{ request_link }}{{ page.next }}{{ page.last }}{{ request_title }}">{{ __('gb_last') }}</a>
                        </li>
                    {% endif %}
                </ul>
            </div>
        {% endif %}
    </div>
</div>