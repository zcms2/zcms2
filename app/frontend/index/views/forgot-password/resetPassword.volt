{% extends "../../index.volt" %}
{% block content %}
    <div class="container user-control">
        <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-2">
            {% include _flashSession %}
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ 'Reset Your Password'|t }}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="password">{{ 'Password'|t }}</label>
                                <input type="password" class="form-control" id="password" name="password" value="">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">{{ 'Confirm Password'|t }}</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-default" value="{{ 'Submit'|t }}">
                            </div>
                            <div class="form-group">
                                <a href="{{ _baseUri }}/user/login/">{{ 'Log in'|t }}</a> | {{ 'or'|t }} <a href="{{ _baseUri }}/user/register/">{{ 'Register an account'|t }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}