{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="" method="post" id="adminForm" novalidate="novalidate"
              enctype="multipart/form-data">
            <div class="row" style="padding: 0 10px 0 10px;">

                <ul class="nav nav-tabs tab-blue tab_attribute_groups">
                    <li class="active">
                        <a href="#tab_website" data-content="/admin/system/config/"
                           data-toggle="tab">{{ __('m_system_config_tab_website') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>

                    <li>
                        <a href="#tab_cache" data-content="/admin/system/config/cache/"
                           data-toggle="tab">{{ __('m_system_config_tab_cache') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>

                    <li>
                        <a href="#tab_email" data-content="/admin/system/config/"
                           data-toggle="tab">{{ __('m_system_config_tab_email') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab_log" data-content="/admin/system/config/"
                           data-toggle="tab">{{ __('m_system_config_tab_log') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab_session" data-content="/admin/system/config/"
                           data-toggle="tab">{{ __('m_system_config_tab_session') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab_template" data-content="/admin/system/config/"
                           data-toggle="tab">{{ __('m_system_config_tab_template') }}
                            <span class="badge badge-danger tab_general"></span>
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane" id="tab_pagination">
                        <div class="col-md-6">

                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane active" id="tab_website">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_sitename') }}</label>
                                {{ configForm.render('sitename') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_baseUri') }}</label>
                                {{ configForm.render('baseUri') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_timezone') }}</label>
                                {{ configForm.render('timezone') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_metadesc') }}</label>
                                {{ configForm.render('metadesc') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_metakey') }}</label>
                                {{ configForm.render('metakey') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_language') }}</label>
                                {{ configForm.render('language') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_direction') }}</label>
                                {{ configForm.render('direction') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_limit') }}</label>
                                {{ configForm.render('limit') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_media_limit') }}</label>
                                {{ configForm.render('media_limit') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_feed_limit') }}</label>
                                {{ configForm.render('feed_limit') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_debug') }}</label>
                                {{ configForm.render('debug') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="tab_cache">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_memcache_status') }}</label>
                                {{ configForm.render('mem_cache_status') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_memcache_prefix') }}</label>
                                {{ configForm.render('mem_cache_prefix') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_memcache_host') }}</label>
                                {{ configForm.render('mem_cache_host') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_memcache_port') }}</label>
                                {{ configForm.render('mem_cache_port') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_mem_cache_lifetime') }}</label>
                                {{ configForm.render('mem_cache_lifetime') }}
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_apc_status') }}</label>
                                {{ configForm.render('apc_status') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_apc_prefix') }}</label>
                                {{ configForm.render('apc_prefix') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_apc_lifetime') }}</label>
                                {{ configForm.render('apc_lifetime') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="tab_session">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_auth_lifetime') }}</label>
                                {{ configForm.render('auth_lifetime') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="tab_template">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_backend_default_template') }}</label>
                                {{ configForm.render('defaultTemplate') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_backend_compile_template') }}</label>
                                {{ configForm.render('compileTemplate') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_frontend_default_template') }}</label>
                                {{ configForm.render('defaultTemplate') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_frontend_compile_template') }}</label>
                                {{ configForm.render('compileTemplate') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="tab_log">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_log') }}</label>
                                {{ configForm.render('log') }}
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_log_type') }}</label>
                                {{ configForm.render('log_type') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="tab_email">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_mail_type') }}</label>
                                {{ configForm.render('mail_type') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_mail_from') }}</label>
                                {{ configForm.render('mail_from') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_from_name') }}</label>
                                {{ configForm.render('from_name') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_send_mail') }}</label>
                                {{ configForm.render('send_mail') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_user') }}</label>
                                {{ configForm.render('smtp_user') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_pass') }}</label>
                                {{ configForm.render('smtp_pass') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_host') }}</label>
                                {{ configForm.render('smtp_host') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_secure') }}</label>
                                {{ configForm.render('smtp_secure') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_port') }}</label>
                                {{ configForm.render('smtp_port') }}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ __('m_system_config_label_smtp_auth') }}</label>
                                {{ configForm.render('smtp_auth') }}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>


                </div>
            </div>
        </form>
    </div>
{% endblock %}