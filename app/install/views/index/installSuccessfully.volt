<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Install') }} | {{ _systemName }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Bootstrap 3.3.4 -->
    <link href="/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="/templates/backend/default/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>
    <!-- Install CSS -->
    <link href="/templates/backend/default/zcms/css/zcms_install.css" rel="stylesheet" type="text/css"/>

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
        <h4 class="install-box-msg">{{ __('Step 4: Welcome to ...') }}</h4>

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

        <div class="row">
            <div class="col-xs-6 text-center">
                <a href="/admin/"><button type="button" class="btn btn-success">{{ __('Go to Admin') }}</button></a>
            </div>
            <div class="col-xs-6 text-center">
                <a href="/"><button type="button" class="btn btn-primary">{{ __('Go to Frontend') }}</button></a>
            </div>
        </div>
    </div>

    <div class="row">
        <br/>

        <p class="text-center">ZCMS | Power by on Phalcon Framework 2!</p>
    </div>
</div>

<!-- jQuery 2.1.4 -->
<script src="/plugins/jquery/jquery-1.11.3.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>