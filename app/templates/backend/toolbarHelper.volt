<!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 class="pull-left">{{ _toolbarHelpers.getHeaderPrimary() }}<small>{{ _toolbarHelpers.getHeaderSecond() }}</small></h1>
            <div class="pull-right zcms-toolbar-helper">{% if _toolbarHelpers.getButtons() | length > 0 %}{{ _toolbarHelpers.getButtons() }}{% endif %}</div>
        </section>
        <div class="clearfix"></div>
        <!-- End Content Header (Page header) -->
{% include _flashSession %}
