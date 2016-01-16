{% include '../../../../templates/backend/header.volt' %}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {% if _toolbarHelpers is defined %}<title>{{ _toolbarHelpers.getTitle() }} | {{ _systemName }}</title>
    {% else %}<title>{{ _systemName }}</title>{% endif %}
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap -->
    <link href="{{ _baseUri }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- DatePicker3 -->
    <link href="{{ _baseUri }}/public/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>
    <!-- DateTimePicker -->
    <link href="{{ _baseUri }}/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ _baseUri }}/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <!-- Media Select-->
    <link href="{{ _baseUri }}/templates/backend/default/zcms/css/summernote-ext-media.css" rel="stylesheet" type="text/css"/>
    <!-- Data Tables -->
    <link href="{{ _baseUri }}/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{ _baseUri }}/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{ _baseUri }}/templates/backend/default/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link href="{{ _baseUri }}/templates/backend/default/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css"/>
    <!-- ZCMS custom -->
    <link href="{{ _baseUri }}/templates/backend/default/zcms/css/zcms.css" rel="stylesheet" type="text/css"/>
    <!-- Coder css -->
    {{ assets.outputCss('css_header') }}<!-- End Coder css -->
    <!-- Custom view css -->
    {% block css_header %}{% endblock %}<!-- End Custom view css -->

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        var _baseUri = '{{ _baseUri }}';
        var _mediaTarget = null;
        var _ZCMS = {};
        _ZCMS.dateFormat = JSON.parse('{{ _dateFormat|json_encode }}');
    </script>
</head>
<body class="sidebar-mini skin-green">
<div class="wrapper">
    <header class="main-header">
        <a href="{{ _baseUri }}/" class="logo" target="_blank">
            <span class="logo-mini">{{ _systemName }}</span>
            <span class="logo-lg">{{ _systemName }}</span>
        </a>

        <div class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                        <a data-toggle="tooltip" data-original-title="My Website" target="_blank" data-placement="bottom" href="{{ _baseUri }}"><i class="fa fa-globe fa-lg"></i></a>
                    </li>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ _baseUri }}{{ _user['avatar'] }}" class="user-image" alt="User Avatar"/>
                            <span class="hidden-xs">{{ _user['full_name'] }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="{{ _baseUri }}{{ _user['avatar'] }}" class="img-circle" alt="User Avatar"/>

                                <p>
                                    {{ _user['full_name'] }}
                                    <small>{{ __('Member since') }} {{ _user['created_at'] }}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                {% if this.acl.isAllowed('user|profile|index') %}
                                    <div class="pull-left">
                                        <a href="{{ _baseUri }}/admin/user/profile/" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                {% endif %}
                                <div class="pull-right">
                                    <a href="{{ _baseUri }}/admin/user/logout/" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <aside class="main-sidebar">
        <div class="sidebar">
            {% include _userMenu %}
        </div>
    </aside>

    <div class="content-wrapper">{% include _toolbarHelper %}{% block content %}{% endblock %}
    </div>

    <footer class="main-footer">
        <div class="text-right">
            <strong><a href="http://www.zcms.com">&copy; ZCMS</a> {{ date('Y') }}</strong> - <i>V{{ _version }}</i> . Power by <a href="http://phalconphp.com/">Phalcon Framework</a>
        </div>
    </footer>
</div>
<!-- jQuery -->
<script src="{{ _baseUri }}/plugins/jquery/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="{{ _baseUri }}/plugins/jquery-ui/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
<!-- Bootstrap JS -->
<script src="{{ _baseUri }}/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Moment -->
<script src="{{ _baseUri }}/plugins/moment/moment-with-locales.min.js" type="text/javascript"></script>
<!-- DatePicker -->
<script src="{{ _baseUri }}/public/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<!-- DateTimePicker -->
<script src="{{ _baseUri }}/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ _baseUri }}/templates/backend/default/js/app.min.js" type="text/javascript"></script>
<!-- Pace -->
<script src="{{ _baseUri }}/plugins/pace/pace.min.js" type="text/javascript"></script>
<!-- ZCMS JS -->
<script src="{{ _baseUri }}/templates/backend/default/zcms/js/zcms.js" type="text/javascript"></script>
<!-- Coder JS -->{{ assets.outputJs('js_footer') }}<!-- End Coder JS -->
<!-- Custom View JS -->{% block js_footer %}{% endblock %}
<!-- End Custom View JS -->
<div class="zcms-custom-main-media-dialog modal" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" tabindex="-1">×</button>
                <h4 class="modal-title">Insert Media</h4></div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
</body>
</html>