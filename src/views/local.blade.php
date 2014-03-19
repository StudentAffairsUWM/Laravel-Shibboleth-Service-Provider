{{Form::open(array('url' => action('Saitswebuwm\Shibboleth\ShibbolethController@localAuthorize'), 'method' => 'POST'))}}
	{{Form::text('local_email')}}
	{{Form::password('local_password')}}
	{{Form::submit('Login')}}
{{Form::close()}}