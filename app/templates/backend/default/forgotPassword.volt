<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ _systemName }} | {{ __('Log in') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Bootstrap 3.3.4 -->
    <link href="/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="/templates/backend/default/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>

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
        <p class="login-box-msg">{{ __('m_user_help_text_forgot_password') }}</p>
        <div class="row">
            {% include _flashSession %}
        </div>
        <form action="" method="post">
            <div class="form-group has-feedback">
                <input type="email" name="email" class="form-control" placeholder="{{ __('m_user_form_user_profile_email') }}"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-6">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('m_user_help_text_get_new_password') }}</button>
                </div>
            </div>
        </form>
        <p class="text-center">
            <br/>
            <a href="/" class="text-center">&larr; {{ __('m_user_help_text_back_to_frontend') }}</a> | <a
                    href="/admin/user/login/">{{ __('m_user_help_text_login_my_account') }}</a>
        </p>
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
<!-- iCheck -->
<script src="/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
</body>
</html>