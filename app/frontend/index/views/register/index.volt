{% extends "../../index.volt" %}
{% block content %}
    <div class="container user-control">
        <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-2">
            {% include _flashSession %}
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ 'Register Account'|t }}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ _baseUri }}/user/register/" method="post">
                            <div class="form-group">
                                <label for="first_name">{{ 'First name'|t }}</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="">
                            </div>
                            <div class="form-group">
                                <label for="last_name">{{ 'Last name'|t }}</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="">
                            </div>
                            <div class="form-group">
                                <label for="email">{{ 'Email'|t }}</label>
                                <input type="text" class="form-control" id="email" name="email" value="">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ 'Password'|t }}</label>
                                <input type="password" class="form-control" id="password" name="password" value="">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">{{ 'Confirm Password'|t }}</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-default" value="{{ 'Register'|t }}">
                            </div>
                            <div class="form-group">
                                <a href="{{ _baseUri }}/user/login/">{{ 'Log in'|t }}</a> | <a href="{{ _baseUri }}/user/forgot-password/">{{ 'Forgot your password?'|t }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}