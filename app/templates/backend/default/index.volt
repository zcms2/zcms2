{% include "../../../templates/backend/" ~ _defaultTemplate ~ "/header" %}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {% if _toolbarHelpers is defined %}<title>{{ _toolbarHelpers.getTitle() }} | {{ _systemName }}</title>
{% else %}<title>{{ _systemName }}</title>{% endif %}<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap -->
    <link href="{{ _baseUri }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        var _baseUri = '{{ _baseUri }}';
    </script>
</head>
<body class="sidebar-mini skin-green">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="{{ _baseUri }}/" class="logo" target="_blank">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">{{ _systemName }}</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">{{ _systemName }}</span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <div class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ _baseUri }}{{ _user['avatar'] }}" class="user-image" alt="User Image"/>
                            <span class="hidden-xs">{{ _user['full_name'] }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ _baseUri }}{{ _user['avatar'] }}" class="img-circle" alt="User Image"/>
                                <p>
                                    {{ _user['full_name'] }}
                                    <small>{{ __('Member since') }} {{ _user['created_at'] }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
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
                            <!-- End Menu Footer-->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <div class="sidebar">
            {% include _userMenu %}
        </div>
        <!-- /.sidebar -->
    </aside>

    <div class="content-wrapper">
        {% include _toolbarHelper %}
        {% block content %}
        {% endblock %}
    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b><a href="http://phalconphp.com/">Phalcon 2 Framework</a> &amp; <a href="https://almsaeedstudio.com/">AdminLTE</a></b> 2.0
        </div>
        <strong>Copyright <a href="http://www.zcms.com">&copy; ZCMS Team</a> {{ date('Y') }}</strong> - <i>Version {{ _version }}</i> .All rights reserved.
    </footer>

</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="{{ _baseUri }}/plugins/jquery/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="{{ _baseUri }}/plugins/jquery-ui/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
<!-- Bootstrap JS -->
<script src="{{ _baseUri }}/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ _baseUri }}/templates/backend/default/js/app.min.js" type="text/javascript"></script>
<!-- Pace -->
<script src="{{ _baseUri }}/plugins/pace/pace.min.js" type="text/javascript"></script>
<!-- ZCMS JS -->
<script src="{{ _baseUri }}/templates/backend/default/zcms/js/zcms.js" type="text/javascript"></script>
<!-- Coder JS -->
{{ assets.outputJs('js_footer') }}
<!-- End Coder JS -->

<!-- Custom View JS -->{% block js_footer %}{% endblock %}<!-- End Custom View JS -->

{% include "../../../templates/backend/" ~ _defaultTemplate ~ "/footer" %}
</body>
</html>