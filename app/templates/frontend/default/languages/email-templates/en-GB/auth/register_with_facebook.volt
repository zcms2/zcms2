app/templates/frontend/default/languages/email-templates/en-GB/auth/register_with_facebook.volt
<br />
You are logged in with facebook!
<br />
Hi, {{ data['first_name'] }} {{ data['last_name'] }}
<br />
Click here to active account <a href="{{ _baseUri }}/auth/active/?token={{ data['active_account_token'] }}">{{ _baseUri }}/auth/active/?token={{ data['active_account_token'] }}</a>