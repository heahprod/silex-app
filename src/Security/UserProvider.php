<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /** @var \App\Security\User[] */
    private $users;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function loadUserByUsername($username)
    {
        if (!isset($this->users[strtolower($username)])) {
            $this->users[$username] = $this->loadUser($username);
        }

        return $this->users[strtolower($username)];
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('User must be an instance of "App\Security\User", "'.get_class($user).'" given.');
        }

        return new User($user->getUsername(), $user->getPassword(), $user->getRoles());
    }

    public function supportsClass($class)
    {
        return $class === 'App\Security\HeahUser';
    }

    private function loadUser($username)
    {
        try {
            // TODO
            return new User($username, '', array('ROLE_USER'));
        } catch (\Exception $e) {
            $e = new UsernameNotFoundException('User with username "'.$username.'" does not exist.', 0, $e);
            $e->setUsername($username);

            throw $e;
        }
    }
}
