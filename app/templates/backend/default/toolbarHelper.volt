<!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>{{ _toolbarHelpers.getHeaderPrimary() }}<small>{{ _toolbarHelpers.getHeaderSecond() }}</small></h1>
            {% include _breadcrumb %}
            <div class="clear"></div>
            <div class="row">
                <div class="col-md-12 text-right zcms-toolbar-helper">{% if _toolbarHelpers.getButtons() | length > 0 %}{{ _toolbarHelpers.getButtons() }}{% endif %}</div>
            </div>
        </section>
        <!-- End Content Header (Page header) -->
{% include _flashSession %}
