<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        &times;
    </button>
    <h4 class="modal-title"></h4>
</div>
<div class="modal-body" style="padding-top: 10px">
    <div class="row">
        {% if error is defined %}
            {{ error }}
        {% else %}
            <div class="col-md-8 col-md-offset-4" style="margin-bottom: 10px; padding-right: 10px">
                <form action="{{ request_link }}" method="post" id="search-form">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">Search</span>
                        {{ text_field('filter_search', 'class' : 'form-control input-sm', 'vale' : request_title) }}
                        <span class="input-group-btn">
                            {{ submit_button('search', 'id': 'search-menu', 'value' : __('gb_search'), 'class' : 'btn btn-primary btn-sm') }}
                        </span>
                    </div>
                </form>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
                <table class="table table-bordered table-striped dataTable">
                    <thead>
                    <tr>
                        <th class="text-center col-stt">#</th>
                        <th>{{ __('gb_title') }}</th>
                        <th class="text-center col-id">{{ __('gb_ID') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for item in page.items %}
                        <tr>
                            <td class="text-center">{{ loop.index }}</td>
                            <td><a class="select-menu-link" data-content="{{ item['link'] }}"
                                   href="#">{{ item['title'] }}</a></td>
                            <td class="text-center">{{ item['id'] }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        {% endif %}
        {% include "router/pagination.volt" %}
    </div>
</div>
