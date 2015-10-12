{% extends "../../index.volt" %}
{% block content %}
    <div class="container user-control">
        <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-2">
            {% include _flashSession %}
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ 'Login'|t }}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ _baseUri }}/user/login/" method="post">
                            <div class="form-group">
                                <label for="email">{{ 'Email'|t }}</label>
                                <input type="text" class="form-control" id="email" name="email" value="">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ 'Password'|t }}</label>
                                <input type="password" class="form-control" id="password" name="password" value="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-default" value="{{ 'Log in'|t }}">
                            </div>
                            <div class="form-group">
                                <a href="{{ _baseUri }}/user/register/">{{ 'Register an account'|t }}</a> | <a href="{{ _baseUri }}/user/forgot-password/">{{ 'Forgot your password?'|t }}</a>
                            </div>
                            {% if isSocialLogin %}
                                <hr/>
                                <div class="form-group text-center">
                                    {% if facebookLoginUrl is defined %}
                                        <a href="{{ facebookLoginUrl }}" class="btn btn-block btn-facebook"><i class="fa fa-facebook"></i> {{ 'Log in with Facebook'|t }}</a>
                                    {% endif %}
                                    {% if googleLoginUrl is defined %}
                                        <a href="{{ googleLoginUrl }}" class="btn btn-block btn-google-plus"><i class="fa fa-google-plus"></i> {{ 'Log in with Google +'|t }}</a>
                                    {% endif %}
                                </div>
                            {% endif %}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}