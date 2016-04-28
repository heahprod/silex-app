<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Custom User Token
 */
class UserToken extends AbstractToken
{
    public function __construct($username = 'NO_USERNAME', array $attributes = [])
    {
        $password = isset($attributes['password']) ? $attributes['password'] : '';
        $roles    = isset($attributes['roles']) ? $attributes['roles'] : array();

        unset($attributes['password'], $attributes['roles']);

        $this->setAttributes($attributes);

        $user = new User($username, $password, $roles);

        $this->setUser($user);

        parent::__construct($roles);

        // To Remove or Override with custom logic
        parent::setAuthenticated(in_array('ROLE_USER', $roles));
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return $this->getUser()->getPassword();
    }
}
