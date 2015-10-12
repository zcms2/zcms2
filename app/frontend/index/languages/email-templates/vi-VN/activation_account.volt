Hello, {{ data['display_name'] }}/{{ data['email'] }}<br/>
<br/>To active account please click on the following link:
<a href="{{ _baseUri }}/user/activate-account/?token={{ data['token'] }}">
    {{ _baseUri }}/user/activate-account/?token={{ data['token'] }}
</a>