{% extends "../../index.volt" %}
{% block content %}
    <table>
            <tbody>
            {% for item in page.items %}
                <tr>
                    {% set index = loop.index %}
                    {% for column in _sortColumn %}
                        {% if column['column'] is defined %}
                            {% set currentCol = column['column'] %}
                        {% endif %}
                        {% if column['translation'] is defined %}
                            {% if column['prefix'] is defined %}
                                <?php $item->$currentCol = __($column['prefix'] . $item->$currentCol)?>
                            {% else %}
                                <?php $item->$currentCol = __($item->$currentCol)?>
                            {% endif %}
                        {% endif %}
                        {% set class = '' %}
                        {% if column['class'] is defined %}{% set class = column['class'] %}{% endif %}
                        {% if column['type'] is defined %}
                            {% if column['type']=='check_all' %}
                                <td class="text-center">{{ check_field('ids[]', 'class': 'check_element', 'value':item.id ) }}</td>
                            {% elseif column['type']=='index' %}
                                <td class="text-center">{{ index }}</td>
                            {% elseif column['type']=='default' %}
                                {% if column['display'] == 'text' %}
                                    <td class="{{ class }}"><?php echo $item->{$column['column']}?></td>
                                {% elseif column['display'] == 'edit' %}
                                    {% if(column['access'] == true) %}
                                        <td class="{{ class }}"><a href="{{ _baseUri }}{{ column['link'] ~ item.id }}/"><?php echo $item->{$column['column']}?></a></td>
                                    {% else %}
                                        <td class="{{ class }}"><?php echo $item->{$column['column']}?></td>
                                    {% endif %}
                                {% elseif column['display'] == 'date' %}
                                    <td class="{{ class }} text-center col-date"><?php if($item->$currentCol && $item->$currentCol != "0000-00-00 00:00:00") { ?>
                                        <?php echo date(__('gb_date_format'), strtotime($item->$currentCol)); ?>
                                        <?php }else{ echo "N/A"; } ?>
                                    </td>
                                {% elseif column['display'] == 'published' %}
                                    {% if(column['access'] == true) %}
                                        <td class="{{ class }} text-center col-published">
                                            {% if item.published %}
                                                <a href="{{ _baseUri }}{{ column['link'] ~ "unPublish/" ~ item.id }}/"><i class="fa fa-check-circle green"></i></a>
                                            {% else %}
                                                <a href="{{ _baseUri }}{{ column['link'] ~ "publish/" ~item.id }}/"><i class="fa fa-check-circle red"></i></a>
                                            {% endif  %}
                                        </td>
                                    {% else %}
                                        <td class="{{ class }} text-center"><i class="fa fa-check-circle"></i></td>
                                    {% endif %}
                                {% elseif column['display'] == 'published_is_core' %}
                                    {% if item.is_core %}
                                        <td class="{{ class }} text-center col-published"><i class="fa fa-lock"></i></td>
                                    {% else %}
                                        {% if(column['access'] == true) %}
                                            <td class="{{ class }} text-center col-published">
                                                {% if item.published %}
                                                    <a href="{{ _baseUri }}{{ column['link'] ~ "unPublish/" ~ item.id }}/"><i class="fa fa-check-circle green"></i></a>
                                                {% else %}
                                                    <a href="{{ _baseUri }}{{ column['link'] ~ "publish/" ~item.id }}/"><i class="fa fa-check-circle red"></i></a>
                                                {% endif  %}
                                            </td>
                                        {% else %}
                                            <td class="{{ class }}"><i class="fa fa-check-circle"></i></td>
                                        {% endif %}
                                    {% endif %}
                                {% elseif column['display'] == 'id' %}
                                    <td class="{{ class }} text-center col-id"><?php echo $item->$currentCol ?></td>
                                {% endif %}
                            {% endif %}
                        {% else %}
                            <td class="{{ class }}"><?php echo $item->$currentCol ?></td>
                        {% endif %}
                    {% endfor %}
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {% include _pagination %}
    {% endif %}
{% endblock %}