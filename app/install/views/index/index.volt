<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Install') }} | {{ _systemName }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Bootstrap 3.3.4 -->
    <link href="{{ _baseUri }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{ _baseUri }}/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{ _baseUri }}/templates/backend/default/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="{{ _baseUri }}/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>
    <!-- Install CSS -->
    <link href="{{ _baseUri }}/templates/backend/default/zcms/css/zcms_install.css" rel="stylesheet" type="text/css"/>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="install-page">
<div class="install-box">
    <div class="install-logo">
        <a href="/">{{ _systemName }}!</a>
    </div>
    <div class="install-box-body">
        <h4 class="install-box-msg">{{ __('Step 1: Check system ...') }}</h4>

        <div class="row">
            {% set _messageFlashSession = flashSession.getMessages() %}
            {% if _messageFlashSession|length >0 %}
                <!-- Flash session -->
                {% set _classExtra = [ "warning" : "alert-warning", "notice" : "alert-warning", "success" : "alert-success", "error" : "alert-danger" ] %}
                {% set _iconExtra = [ "warning" : "fa fa-exclamation-triangle", "notice" : "fa fa-info-circle", "success" : "fa fa-check-circle", "error" : "fa fa-times-circle" ] %}
                <div class="col-md-12 zcms-toolbar-helper">
                    {% for key, item in _messageFlashSession %}
                        {% for childItem in item %}
                            <div class="alert {{ _classExtra[key] }}">
                                <button data-dismiss="alert" class="close">×</button>
                                <i class="{{ _iconExtra[key] }}"></i>
                                <strong>{{ __('gb_flash_session_' ~ key) }}:</strong> {{ __(childItem) }}
                            </div>
                        {% endfor %}
                    {% endfor %}
                </div>
                <!-- End Flash session -->
                <div class="clearfix"></div>
            {% endif %}
        </div>
        <form action="{{ _baseUri }}/install.php?step=2" method="post">
            <table class="table">
                <tr>
                    <td>{{ __('Phalcon') }}</td>
                    <td class="text-center">{% if env_phalcon %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('MB String') }} (For UTF 8)</td>
                    <td class="text-center">{% if env_mbstring %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('PDO') }}</td>
                    <td class="text-center">{% if env_PDO %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('PDO PgSQL') }}</td>
                    <td class="text-center">{% if env_pdo_pgsql %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('APC Cache') }}</td>
                    <td class="text-center">{% if env_apc %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('Memcache') }}</td>
                    <td class="text-center">{% if env_memcache %}<span class="label label-success">Installed</span>{% else %}<span class="label label-warning">N/A</span>{% endif %}</td>
                </tr>
                <tr>
                    <td>{{ __('Redis') }}</td>
                    <td class="text-center"><span class="label label-warning">Uncertainly</span></td>
                </tr>
            </table>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    {% if hiddenButton is not defined %}
                        <button class="btn btn-success btn-block btn-flat">{{ __('Start Install') }}</button>
                    {% endif %}
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <br/>

        <p class="text-center">ZCMS | Power by on Phalcon Framework 2!</p>
    </div>
</div>

<!-- jQuery 2.1.4 -->
<script src="{{ _baseUri }}/plugins/jquery/jquery-1.11.3.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ _baseUri }}/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>