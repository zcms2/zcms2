{% if _pageLayout is defined and _filter is defined %}
    {% if _filterColumn is defined %}
        <div class="row cms-toolbar-helper">
            <div class="col-md-8">
                {% if _toolbarHelpers is defined %}
                    {{ _toolbarHelpers.renderHtmlFilter() }}
                {% endif %}
            </div>
            <div class="col-md-4">
                <div class="dataTables_filter">
                    <label>
                        <button type="reset" onclick="return ZCMS.resetFilter();" class="btn btn-warning btn-sm"><i
                                    class="fa fa-undo"></i> {{ __('gb_reset') }}</button>
                    </label>
                    <label>
                        <button type="submit" class="btn btn-success btn-sm"><i
                                    class="fa fa-filter"></i> {{ __('gb_search') }}</button>
                    </label>
                </div>
            </div>
        </div>
    {% endif %}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover dataTable">
    <thead>
    <tr>
        {% for item in _pageLayout %}
            {% set class = '' %}
            {% set styleInline = '' %}
            {% set title = '' %}
            {% set sortColumnDefault = '' %}
            {% if item['sort_column'] is defined %}
                {% set sortColumnDefault = item['sort_column'] %}
            {% elseif item['column'] is defined %}
                {% set sortColumnDefault = item['column'] %}
            {% endif %}
            {% if item['class'] is defined %}{% set class = item['class'] %}{% endif %}
            {% if item['css'] is defined %}{% set styleInline = item['css'] %}{% endif %}
            {% if item['class_hidden'] is defined %}{% set class = class ~ ' ' ~ item['class_hidden'] %}{% endif %}
            {% if item['title'] is defined %}{% set title = __(item['title']) %}{% endif %}
            {% if item['type'] is defined %}
                {% if item['type'] == 'check_all' %}
                    <th style="{{ styleInline }}" class="text-center col-check-all">{{ check_field('item_check_all', 'class': 'check_all' ) }}</th>
                {% elseif item['type'] == 'index' %}
                    <th style="{{ styleInline }}" class="text-center col-stt">{{ __('gb_stt') }}</th>
                {% elseif item['type'] == 'actions' %}
                    {% if item['column'] is defined %}
                        {% if _filter['filter_order'] == sortColumnDefault %}
                            <th style="{{ styleInline }}" class="text-center col-active {{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                                   href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length > 0 %}
                            <th style="{{ styleInline }}" class="text-center col-active {{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                                   href="#"{% endif %}>
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length == 0 %}
                            <th style="{{ styleInline }}" class="text-center col-active {{ class }}">
                                <a href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% endif %}
                    {% else %}
                        <th style="{{ styleInline }}" class="text-center col-stt">{{ title }}</th>
                    {% endif %}
                {% elseif item['type'] == 'image' %}
                    <th style="{{ styleInline }}" class="{{ class }}">{{ title }}</th>
                {% elseif item['type'] == 'active' %}
                    {% if _filter['filter_order'] == sortColumnDefault %}
                        <th style="{{ styleInline }}" class="text-center col-active {{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                               href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length > 0 %}
                        <th style="{{ styleInline }}" class="text-center col-active {{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                               href="#"{% endif %}>
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length == 0 %}
                        <th style="{{ styleInline }}" class="text-center col-active {{ class }}">
                            <a href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% endif %}
                {% elseif item['type'] == 'date' %}
                    {% if _filter['filter_order'] == sortColumnDefault %}
                        <th style="{{ styleInline }}" class="text-center col-date {{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                               href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length > 0 %}
                        <th style="{{ styleInline }}" class="text-center col-date {{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                               href="#"{% endif %}>
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length == 0 %}
                        <th style="{{ styleInline }}" class="text-center col-date {{ class }}">
                            <a href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% endif %}
                {% elseif item['type'] == 'id' %}
                    {% if _filter['filter_order'] == sortColumnDefault %}
                        <th style="{{ styleInline }}" class="text-center col-id {{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                               href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length > 0 %}
                        <th style="{{ styleInline }}" class="text-center col-id {{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                               href="#"{% endif %}>
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length == 0 %}
                        <th style="{{ styleInline }}" class="text-center col-id {{ class }}">
                            <a href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% endif %}
                {% elseif item['type'] == 'published' or item['type'] == 'published_is_core' %}
                    {% if _filter['filter_order'] == sortColumnDefault %}
                        <th style="{{ styleInline }}" class="text-center col-published {{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                               href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length > 0 %}
                        <th style="{{ styleInline }}" class="text-center col-published {{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                            <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                               href="#"{% endif %}>
                                {{ title }}
                            </a>
                        </th>
                    {% elseif item['column']|length == 0 %}
                        <th style="{{ styleInline }}" class="text-center col-published {{ class }}">
                            <a href="#">
                                {{ title }}
                            </a>
                        </th>
                    {% endif %}
                {% elseif item['type'] == 'text' %}
                    {% if item['column'] is defined %}
                        {% if _filter['filter_order'] == sortColumnDefault %}
                            <th style="{{ styleInline }}" class="{{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                                   href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length > 0 %}
                            <th style="{{ styleInline }}" class="{{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                                   href="#"{% endif %}>
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length == 0 %}
                            <th style="{{ styleInline }}" class="{{ class }}">
                                <a href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% endif %}
                    {% endif %}
                {% else %}
                    {% if item['column'] is defined %}
                        <!-- If not type -->
                        {% if _filter['filter_order'] == sortColumnDefault %}
                            <th style="{{ styleInline }}" class="{{ class }} {% if item['sort'] is not defined or item['sort'] == true %}sorting sorting_{% if _filter['filter_order_dir']|upper == 'ASC' %}asc{% else %}desc{% endif %}{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','{% if _filter['filter_order_dir']|upper == 'ASC' %}DESC{% else %}ASC{% endif %}')"{% endif %}
                                   href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length > 0 %}
                            <th style="{{ styleInline }}" class="{{ class }}{% if item['sort'] is not defined or item['sort'] == true %} sorting{% endif %}">
                                <a {% if item['sort'] is not defined or item['sort'] == true %}onclick="ZCMS.columnOrdering('{{ sortColumnDefault }}','desc')"
                                   href="#"{% endif %}>
                                    {{ title }}
                                </a>
                            </th>
                        {% elseif item['column']|length == 0 %}
                            <th style="{{ styleInline }}" class="{{ class }}">
                                <a href="#">
                                    {{ title }}
                                </a>
                            </th>
                        {% endif %}
                    {% endif %}
                {% endif %}
            {% endif %}
        {% endfor %}
    </tr>

    {% if _filterColumn is defined %}
        <tr class="tr-filter">
            {% for item in _pageLayout %}
                {% if item['filter'] is defined %}
                    {% set type = item['filter']['type']|upper %}
                    {% if type == 'DATERANGE' %}
                        <th class="th-filter th-filter-date-range">
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_from') }}
                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                            </div>
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_to') }}
                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                            </div>
                        </th>
                    {% elseif type == 'DATE' %}
                        <th class="th-filter th-filter-date-range">
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name']) }}
                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                            </div>
                        </th>
                    {% elseif type == 'SELECT' or type == 'MULTIPLESELECT' %}
                        <th>
                            {{ _filterColumn.render(item['filter']['name']) }}
                        </th>
                    {% elseif type == 'PRICERANGE' %}
                        <th class="th-filter th-filter-price-range">
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_from') }}
                                <span class="input-group-addon"> $ </span>
                            </div>
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_to') }}
                                <span class="input-group-addon"> $ </span>
                            </div>
                        </th>
                    {% elseif type == 'NUMBERRANGE' %}
                        <th class="th-filter th-filter-number-range">
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_from') }}
                            </div>
                            <div class="input-group">
                                {{ _filterColumn.render(item['filter']['name'] ~ '_to') }}
                            </div>
                        </th>
                    {% elseif type == 'TEXT' %}
                        <th class="th-filter">{{ _filterColumn.render(item['filter']['name']) }}</th>
                    {% endif %}
                {% else %}
                    <th class="th-filter"><span class="col-no-filter">&nbsp;</span></th>
                {% endif %}
            {% endfor %}
        </tr>
    {% endif %}
    </thead>
    <tbody>

    {% set idPrefix = 'id' %}
    {% if _pageLayout[0]['type'] is defined and _pageLayout[0]['type'] == 'check_all' and _pageLayout[0]['column'] is defined %}
        {% set idPrefix = _pageLayout[0]['column'] %}
    {% endif %}

    {% for item in _page.items %}
        <?php if(isset($item->{$idPrefix})) $itemPrefixValue = $item->{$idPrefix}?>
        <tr>
        {% set index = loop.index + (_page.current -1 ) * _limit %}
        {% for column in _pageLayout %}
            {% if column['column'] is defined %}
                {% set currentCol = column['column'] %}
                <?php $itemValue = $item->$currentCol ?>
                {% if itemValue|trim == '' and column['default'] is defined %}
                    {% set itemValue = __(column['default']) %}
                {% endif %}
                <?php $itemColumnColumn = $item->{$column['column']} ?>
                {% set lblBefore = '' %}
                {% set lblAfter = '' %}
                {% set lblText = '' %}
                {% if column['label'] is defined %}
                    {% for case in column['label'] %}
                        {% if case['condition'] == '==' %}
                            {% if itemColumnColumn == case['condition_value'] %}
                                {% set lblBefore = '<span class="' ~ case['class'] ~ '">' %}
                                {% set lblAfter = '</span>' %}

                                {% if case['text'] is defined %}
                                    {% set lblText = case['text'] %}
                                    {% set itemValue = lblText %}
                                {% endif %}
                            {% endif %}
                        {% elseif case['condition'] == '!=' %}
                            {% if itemColumnColumn != case['condition_value'] %}
                                {% set lblBefore = '<span class="' ~ case['class'] ~ '">' %}
                                {% set lblAfter = '</span>' %}

                                {% if case['text'] is defined %}
                                    {% set lblText = case['text'] %}
                                    {% set itemValue = lblText %}
                                {% endif %}
                            {% endif %}
                        {% endif %}
                    {% endfor %}
                {% endif %}

                {% if column['translation'] is defined %}
                    {% if column['prefix'] is defined %}
                        {% if lblText|length %}
                            {% set itemValue = lblBefore ~ __(column['prefix'] ~ lblText) ~ lblAfter %}
                        {% else %}
                            {% set itemValue = lblBefore ~ __(column['prefix'] ~ itemValue) ~ lblAfter %}
                        {% endif %}
                    {% else %}
                        {% if lblText|length %}
                            {% set itemValue = lblBefore ~ __(lblText) ~ lblAfter %}
                        {% else %}
                            {% set itemValue = lblBefore ~ __(itemValue) ~ lblAfter %}
                        {% endif %}
                    {% endif %}
                {% else %}
                    {% if column['prefix'] is defined %}
                        {% if lblText|length %}
                            {% set itemValue = lblBefore ~ column['prefix'] ~ lblText ~ lblAfter %}
                        {% endif %}
                    {% else %}
                        {% set itemValue = lblBefore ~ itemValue ~ lblAfter %}
                    {% endif %}
                {% endif %}

                {% if column['pad_type'] is defined and column['pad_column'] is defined %}
                    {% if column['pad_string'] is defined %}
                        {% set pad_string = column['pad_string'] %}
                    {% else %}
                        {% set pad_string = '- ' %}
                    {% endif %}
                    <?php $pad = str_pad('',($item->{$column['pad_column']} -1) * strlen($pad_string), $pad_string, $column['pad_type']); ?>
                    {% set itemValue = pad ~ itemValue %}
                {% endif %}
            {% endif %}

            {% set class = '' %}
            {% if column['class'] is defined %}{% set class = column['class'] %}{% endif %}
            {% if column['class_hidden'] is defined %}{% set class = class ~ ' ' ~ column['class_hidden'] %}{% endif %}

            {% if column['type'] is defined %}
                {% if column['type'] == 'check_all' %}
                    {% if column['column'] is defined %}
                        <td class="text-center {{ class }}">{{ check_field('ids[]', 'class': 'check_element', 'value': itemPrefixValue ) }}</td>
                    {% else %}
                        <td class="text-center {{ class }}">{{ check_field('ids[]', 'class': 'check_element', 'value': itemPrefixValue ) }}</td>
                    {% endif %}
                {% elseif column['type'] == 'index' %}
                    <td class="text-center {{ class }}">{{ index }}</td>
                {% elseif column['type'] == 'link' %}
                    {% if column['array_values'] is defined and column['array_values'][itemValue] %}
                        {% if column['translation'] is defined and column['translation'] %}
                            {% set itemValue = __(column['array_values'][itemValue]) %}
                        {% else %}
                            {% set itemValue = column['array_values'][itemValue] %}
                        {% endif %}
                    {% endif %}
                    {% if(column['access'] == true) %}
                        <td class="{{ class }}">
                            {% if column['link_href'] is defined %}
                                <a {% if column["target"] is defined %} target="{{ column["target"] }}" {% endif %} {% if column['link_title'] is defined %}title="{{ __(column['link_title']) }}"{% endif %}
                                   href="<?php echo $item->{$column['link_href']} ?>">
                                    {{ column['link_text'] }}</a>
                            {% else %}
                                <a {% if column["target"] is defined %} target="{{ column["target"] }}" {% endif %} {% if column['link_title'] is defined %}title="{{ __(column['link_title']) }}"{% endif %}
                                   href="{{ _baseUri }}{{ column['link'] }}{% if column['link_prefix'] is defined %}<?php echo $item->{$column['link_prefix']} ?>{% else %}{{ itemPrefixValue }}{% endif %}/">
                                    {{ itemValue }}</a>
                            {% endif %}
                        </td>
                    {% else %}
                        <td class="{{ class }}">{{ itemValue }}</td>
                    {% endif %}
                {% elseif column['type'] == 'price' %}
                    <td class="{{ class }}">
                        <?php $itemValue = (float)$itemValue; ?>
                        ${{ number_format(itemValue) }}
                    </td>
                {% elseif column['type'] == 'text' %}
                    <td class="{{ class }}">
                        {% if column['array_values'] is defined %}
                            {% if column['translation'] is defined and column['translation'] %}
                                {{ __(column['array_values'][itemValue])  }}
                            {% else %}
                                {{ column['array_values'][itemValue] }}
                            {% endif %}
                        {% else %}
                            {% if column['translation'] is defined and column['translation'] %}
                                {{ __(itemValue) }}
                            {% else %}
                                {{ itemValue }}
                            {% endif %}
                        {% endif %}
                    </td>
                {% elseif column['type'] == 'image' %}
                    {% if column['width'] is not defined %}{% set width = 50 %}{% else %}{% set width = column['width'] %}{% endif %}
                    {% if column['height'] is not defined %}{% set height = 50 %}{% else %}{% set height = column['height'] %}{% endif %}
                    <td class="{{ class }}">
                        <img width="{{ width }}"
                             {% if column['style'] is defined %}style="{{ column['style'] }}"{% endif %}
                             height="{{ height }}" alt="{{ __('gb_image_thumb') }}"
                                {% if itemValue!='' %}
                             src="{% if column['uri_prefix'] is defined %}{{ column['uri_prefix'] }}{% else %}{{ _baseUri }}{% endif %}{{ itemValue }}">
                        {% else %}
                            src="{{ _baseUri }}{{ column["default_thumbnail"] }}">
                        {% endif %}
                    </td>
                {% elseif column['type'] == 'date' %}
                    <td class="{{ class }} text-center">
                        {% if itemValue and itemValue != "0000-00-00 00:00:00" %}
                            {{ date(__('gb_date_format'), strtotime(itemValue)) }}
                        {% else %}
                            {{ __('gb_na') }}
                        {% endif %}
                    </td>
                {% elseif column['type'] == 'published' %}
                    {% if(column['access'] == true) %}
                        <td class="{{ class }} text-center">
                            {% if itemValue %}
                                <a title="{{ __('gb_unpublished_this_item') }}"
                                   href="{{ _baseUri }}{{ column['link'] ~ "unPublish/" ~ itemPrefixValue }}/"><i
                                            class="fa fa-check-circle green"></i></a>
                            {% else %}
                                <a title="{{ __('gb_published_this_item') }}"
                                   href="{{ _baseUri }}{{ column['link'] ~ "publish/" ~ itemPrefixValue }}/"><i
                                            class="fa fa-check-circle red"></i></a>
                            {% endif %}
                        </td>
                    {% else %}
                        <td class="{{ class }} text-center"><i class="fa fa-check-circle"></i></td>
                    {% endif %}
                {% elseif column['type'] == 'active' %}
                    {% if(column['access'] == true) %}
                        <td class="{{ class }} text-center col-active">
                            {% if itemValue == 1 %}
                                <a title="{{ __('gb_deactivate_this_item') }}"
                                   href="{{ _baseUri }}{{ column['link'] ~ "deactivate/" ~ itemPrefixValue }}/"><i
                                            class="fa fa-check-circle green"></i></a>
                            {% else %}
                                <a title="{{ __('gb_activate_this_item') }}"
                                   href="{{ _baseUri }}{{ column['link'] ~ "active/" ~ itemPrefixValue }}/"><i
                                            class="fa fa-check-circle red"></i></a>
                            {% endif %}
                        </td>
                    {% else %}
                        <td class="{{ class }} text-center grey"><i class="fa fa-check-circle"></i></td>
                    {% endif %}
                {% elseif column['type'] == 'published_is_core' %}
                    {% if item.is_core %}
                        <td class="{{ class }} text-center grey"><i class="fa fa-lock"></i></td>
                    {% else %}
                        {% if(column['access'] == true) %}
                            <td class="{{ class }} text-center">
                                {% if item.published %}
                                    <a title="{{ __('gb_unpublished_this_item') }}"
                                       href="{{ _baseUri }}{{ column['link'] ~ "unPublish/" ~ itemPrefixValue }}/"><i
                                                class="fa fa-check-circle green"></i></a>
                                {% else %}
                                    <a title="{{ __('gb_published_this_item') }}"
                                       href="{{ _baseUri }}{{ column['link'] ~ "publish/" ~ itemPrefixValue }}/"><i
                                                class="fa fa-check-circle red"></i></a>
                                {% endif %}
                            </td>
                        {% else %}
                            <td class="{{ class }} text-center grey"><i class="fa fa-check-circle"></i></td>
                        {% endif %}
                    {% endif %}
                {% elseif column['type'] == 'array_values' %}
                    <td class="{{ class }}">
                        {% if column['array_values'] is defined and column['array_values'][itemValue] %}
                            {% if column['translation'] is defined and column['translation'] %}
                                {{ __(column['array_values'][itemValue]) }}
                            {% else %}
                                {{ column['array_values'][itemValue] }}
                            {% endif %}
                        {% else %}
                            Please check 'array_values' => ['key1' => 'value1', 'key2' => 'value2',...] or {{ itemValue }} not defined
                        {% endif %}
                    </td>
                {% elseif column['type'] == 'action' %}
                    {% set html = '' %}
                    {% for case in column['action'] %}
                        {% if case['condition'] == '==' %}
                            {% if itemValue == case['condition_value'] %}
                                {% if case['access'] is defined and case['access'] %}
                                    {% if column['link_prefix'] %}
                                        <?php $itemColumnLinkPrefix = $item->{$column['link_prefix']} ?>
                                        {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="' ~ _baseUri ~ case['link'] ~ itemColumnLinkPrefix ~ '" >' %}
                                    {% else %}
                                        <?php $itemCaseLinkPrefix = $item->{$case['link_prefix']} ?>
                                        {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="' ~ _baseUri ~ case['link'] ~ itemCaseLinkPrefix ~ '" >' %}
                                    {% endif %}
                                {% else %}
                                    {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="#" >' %}
                                {% endif %}

                                {% if case['icon_class'] is defined %}
                                    {% set html = html ~ '<i class="' ~ case['icon_class'] ~ '"></i>' %}
                                {% endif %}

                                {% if case['text'] is defined %}
                                    {% if case['translation'] is defined and case['translation'] == false %}
                                        {% set html = html ~ case['text'] %}
                                    {% else %}
                                        {% set html = html ~ __(case['text']) %}
                                    {% endif %}
                                {% endif %}
                                {% set html = html ~ '</a>' %}
                            {% endif %}
                        {% elseif case['condition'] == '!=' %}
                            {% if itemValue != case['condition_value'] %}
                                {% if case['access'] is defined and case['access'] %}
                                    {% if column['link_prefix'] %}
                                        <?php $itemColumnLinkPrefix = $item->{$column['link_prefix']} ?>
                                        {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="' ~ _baseUri ~ case['link'] ~ itemColumnLinkPrefix ~ '" >' %}
                                    {% else %}
                                        <?php $itemCaseLinkPrefix = $item->{$case['link_prefix']} ?>
                                        {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="' ~ _baseUri ~ case['link'] ~ itemCaseLinkPrefix ~ '" >' %}
                                    {% endif %}
                                {% else %}
                                    {% set html = '<a title="' ~ __(case['link_title']) ~ '" href="#" >' %}
                                {% endif %}

                                {% if case['icon_class'] is defined %}
                                    {% set html = html ~ '<i class="' ~ case['icon_class'] ~ '"></i>' %}
                                {% endif %}

                                {% if case['text'] is defined %}
                                    {% if case['translation'] is defined and case['translation'] == false %}
                                        {% set html = html ~ case['text'] %}
                                    {% else %}
                                        {% set html = html ~ __(case['text']) %}
                                    {% endif %}
                                {% endif %}
                                {% set html = html ~ '</a>' %}
                            {% endif %}
                        {% endif %}
                    {% endfor %}
                    <td class="{{ class }}">{{ html }}</td>
                {% elseif column['type'] == 'id' %}
                    <td class="{{ class }} text-center">{{ itemValue }}</td>
                {% elseif column['type'] == 'inline' %}
                    <td class="{{ class }} text-center">
                        {% if column['access'] is defined and column['access'] %}
                            <a href="#" id="{{ column["column"] }}"
                               data-pk="{{ itemPrefixValue }}"
                               class="inline"
                                    {% if column["data_placement"] is defined %}
                                        data-placement="{{ column["data_placement"] }}"
                                    {% else %}
                                        data-placement="top"
                                    {% endif %}
                               data-value="<?php echo $item->{$column['column']};?>"
                               data-type="{{ column["in-type"] }}"
                               data-url="{{ column["url"] }}"
                               data-original-title="{{ __(column["title"]) }}">{{ itemValue }}</a>
                        {% else %}
                            {{ itemValue }}
                        {% endif %}
                    </td>
                {% elseif column['type'] == 'custom_link' %}
                    <td class="{{ class }} text-center">
                        {% if column["target"] is defined %}
                            <a href="<?php echo $item->{$column['sub_column']} ?>"
                               id="{{ column["column"] }}" target="{{ column["target"] }}">{{ itemValue }}</a>
                        {% else %}
                            <a href="<?php echo $item->{$column['sub_column']} ?>"
                               id="{{ column["column"] }}">{{ itemValue }}</a>
                        {% endif %}

                    </td>
                {% elseif column['type'] == 'actions' and column['actions'] is defined %}
                    <td class="{{ class }}">
                        {% for action in column['actions'] %}
                            {% set access = 1 %}
                            {% set html = '' %}
                            {% if action['access'] is defined %}
                                {% set access = action['access'] %}
                            {% endif %}
                            {% if access %}
                                {% if column['link_prefix'] is defined %}
                                    <?php $itemColumnLinkPrefix = $item->{$column['link_prefix']} ?>
                                    {% set html = '<a title="' ~ __(action['link_title']) ~ '" href="' ~ _baseUri ~ action['link'] ~ itemColumnLinkPrefix ~ '/" >' %}
                                {% else %}
                                    <?php $itemCaseLinkPrefix = $item->{$action['link_prefix']} ?>
                                    {% set html = '<a title="' ~ __(action['link_title']) ~ '" href="' ~ _baseUri ~ action['link'] ~ itemCaseLinkPrefix ~ '/" >' %}
                                {% endif %}
                            {% else %}
                                {% set html = '<a title="' ~ __(action['link_title']) ~ '" href="#" >' %}
                            {% endif %}

                            {% if action['icon_class'] is defined %}
                                {% set html = html ~ '<i class="' ~ action['icon_class'] ~ '"></i>' %}
                            {% endif %}

                            {% if action['text'] is defined %}
                                {% if action['translation'] is defined and action['translation'] == false %}
                                    {% set html = html ~ action['text'] %}
                                {% else %}
                                    {% set html = html ~ __(action['text']) %}
                                {% endif %}
                            {% endif %}
                            {% set html = html ~ '</a>' %}
                            {{ html }}{#echo html#}
                        {% endfor %}
                        {% if column['display_value'] is defined %}
                            {{ itemValue }}
                        {% endif %}
                    </td>
                {% else %}
                    <td class="{{ class }}">{{ itemValue }}</td>
                {% endif %}
            {% else %}
                <td class="{{ class }}">{{ itemValue }}</td>
            {% endif %}
        {% endfor %}
        </tr>
    {% endfor %}
    </tbody>
    </table>
    </div>
{% endif %}
{% include _pagination %}