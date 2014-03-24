<?php namespace Saitswebuwm\Shibboleth;

use Eloquent;
use Users;

class Group extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'groups';

    public $timestamps = false; // Don't care about time for this.

        
        /**
         * These values are allowed to be used in mass
         * assignments.
         * 
         * @var type 
         */
        protected $fillable = array('name');
        
        /**
         * Rules for editing and adding data to the
         * Users table.
         * 
         * @return array rules
         */
        public static function rules()
        {
            return $rules = array();
        }

        /**
         * Relation to users
         *
         */
        public function users()
        {
            return $this->belongsToMany('User');
        }

}