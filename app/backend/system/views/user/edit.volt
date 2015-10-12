{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm"
                              novalidate="novalidate">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">
                                            First Name <span class="symbol required"></span>
                                        </label>
                                        {{ form.render("first_name") }}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">
                                            Last Name <span class="symbol required"></span>
                                        </label>
                                        {{ form.render("last_name") }}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">
                                            Email <span class="symbol required"></span>
                                        </label>
                                        {{ form.render("email") }}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">
                                            Active Account
                                        </label>
                                        {{ form.render("is_active") }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Password
                                        </label>
                                        {{ form.render("password") }}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">
                                            Confirmation Password
                                        </label>
                                        {{ form.render("password_confirmation") }}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">
                                            Role <span class="symbol required"></span>
                                        </label>
                                        {{ form.render("role_id") }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <span class="symbol required"></span>Required Fields
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}