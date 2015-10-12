<div id="fight-quiz-container">
    <div class="ws_images">
        <ul>{% for item in slideItems %}
                <li><a href="{{ item['link'] }}" target="{{ item['target'] }}" title="{{ item['title'] }}"><img src="{{ _baseUri }}{{ item['image'] }}"></a></li>{% endfor %}</ul>
    </div>
    <div class="ws_shadow"></div>
</div>