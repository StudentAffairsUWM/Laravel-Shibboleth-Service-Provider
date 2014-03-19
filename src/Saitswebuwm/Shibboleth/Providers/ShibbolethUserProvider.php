<?php namespace Saitswebuwm\Shibboleth\Providers;

use Illuminate\Auth\UserInterface;
use Illuminate\Hashing\HasherInterface;
use Illuminate\Auth\UserProviderInterface;

class ShibbolethUserProvider implements UserProviderInterface 
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new Shibboleth user provider.
     *
     * @param  \Illuminate\Hashing\HasherInterface  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherInterface $hasher, $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        $user = $this->retrieveByCredentials(array('id' => $identifier));
        if ($user && $user->getAuthIdentifier() == $identifier) {
            return $user;
        }
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Auth\UserInterface
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (count($credentials) == 0) return null;

        $class = '\\'.ltrim($this->model, '\\');
        $user = new $class;

        $query = $user->newQuery();
        foreach ($credentials as $key => $value)
        {
            if (!str_contains($key, 'password')) $query->where($key, $value);
        }
        
        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        if ($user->type == 'local')
        {
            $plain = $credentials['password'];
            return $this->hasher->check($plain, $user->getAuthPassword());
        }
        else
        {
            return true;
        }
    }
}