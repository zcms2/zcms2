<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ _systemName }} | {{ __('Log in') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Bootstrap 3.3.4 -->
    <link href="{{ _baseUri }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{ _baseUri }}/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{ _baseUri }}/templates/backend/default/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="{{ _baseUri }}/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>
    <!-- ZCMS custom -->
    <link href="{{ _baseUri }}/templates/backend/default/zcms/css/zcms.css" rel="stylesheet" type="text/css"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="/">{{ _systemName }}</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">{{ __('m_user_help_text_sign_into_start_your_session') }}</p>
        <div class="row">
            {% include _flashSession %}
        </div>
        <form action="" method="post">
            <div class="form-group has-feedback">
                <input type="email" name="email" class="form-control" placeholder="{{ __('m_user_form_user_profile_email') }}"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="{{ __('m_user_form_user_login_password') }}"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-8">
                    <button type="submit" class="btn btn-success btn-block btn-flat">{{ __('m_user_help_text_sign_in') }}</button>
                </div>
            </div>
        </form>
        <p class="text-center">
            <br/>
            <a href="{{ _baseUri }}" class="text-center">&larr; {{ __('m_user_help_text_back_to_frontend') }}</a> | <a
                    href="/admin/user/forgot-password/">{{ __('m_user_help_text_i_forgot_my_password') }}</a>
        </p>
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
<!-- Pace -->
<script src="{{ _baseUri }}/plugins/pace/pace.min.js" type="text/javascript"></script>
</body>
</html>