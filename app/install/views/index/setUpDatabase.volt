<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Setup Database') }} | {{ _systemName }}</title>
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
        <h4 class="install-box-msg">{{ __('Step 2: Setup your database ...') }}</h4>

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
        {% if db_connect == 0 %}
            <form action="{{ _baseUri }}/install.php?step=2" method="post">
                <div class="form-group">
                    <label for="host">{{ __('Adapter') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-equalizer"></i></span>
                        {{ select_static('adapter',['Mysql' : 'Mysql', 'Postgresql' : 'Postgresql' ], 'class' : 'form-control', 'value' : 'mysql', 'style' : 'border-radius: 0; -webkit-appearance: none;') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="host">{{ __('Host') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-equalizer"></i></span>
                        {{ text_field('host', 'class' : 'form-control', 'value' : 'localhost') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="dbname">{{ __('Database Name') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-tasks"></i></span>
                        {{ text_field('dbname', 'class' : 'form-control', 'value' : 'zcms_db_master') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">{{ __('Username') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        {{ text_field('username', 'class' : 'form-control', 'value' : 'root') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Password') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        {{ text_field('password', 'class' : 'form-control') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="port">{{ __('Port') }} <span class="symbol required"></span>:</label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-plug"></i></span>
                        {{ text_field('port', 'class' : 'form-control', 'value' : '3306') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-3">
                        <button type="submit" class="btn btn-success btn-block btn-flat">{{ __('Check database') }} &rightarrow;</button>
                    </div>
                </div>
            </form>
        {% else %}
            <div class="row">
                <h4 class="text-center">Connect database successfully...</h4>
                <br/>
                <div class="col-xs-6 col-xs-offset-3">
                    <a href="{{ _baseUri }}/install.php?step=3">
                        <button type="button" class="btn btn-success btn-block btn-flat">{{ __('Next') }} &rightarrow;</button>
                    </a>
                </div>
            </div>
        {% endif %}
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
<script type="text/javascript">
    $(function () {
        $('#adapter').change(function () {
           if($(this).val() == 'Mysql'){
               $('#port').val('3306');
               $('#username').val('root');
           }else{
               $('#port').val('5432');
               $('#username').val('postgres');
           }
        });
    });
</script>
</body>
</html>