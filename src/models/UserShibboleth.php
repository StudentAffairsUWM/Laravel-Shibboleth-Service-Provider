<?php namespace Saitswebuwm\Shibboleth;

use Illuminate\Database\Eloquent\Model;

class UserShibboleth extends User{

	protected $fillable = array('email', 'first_name', 'last_name', 'password', 'type');

}