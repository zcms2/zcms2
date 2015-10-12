{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row cms-toolbar-helper">
                        <div class="col-md-12">
                            <div class="dataTables_filter">
                                <span><i>File Name = DatabaseName_Time(Hour|Minute|Second)_Date(Day|Month|Year)_UserID_[UserID_ClickBackUpDatabase]</i></span>
                            </div>
                        </div>
                    </div>
                    {% include _standardTable %}
                </div>
            </div>
        </form>
    </div>
{% endblock %}