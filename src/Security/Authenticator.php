<?php

namespace App\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class Authenticator
{
    private $encoder;
    private $userProvider;

    public function __construct(UserProvider $userProvider, MessageDigestPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
        $this->userProvider = $userProvider;
    }

    /**
     * @param $username
     * @param $plainPassword
     *
     * @return UserToken
     *
     * @throws AuthenticationException
     */
    public function authenticate($username, $plainPassword)
    {
        /** @var User $user */
        $user = $this->userProvider->loadUserByUsername($username);
        $password = $this->encoder->encodePassword($plainPassword, $user->getSalt());

        $authenticated = $password === $user->getPassword();

        if ($authenticated) {
            return new UserToken($username, array(
                'password' => $password,
                'roles' => $user->getRoles()
            ));
        }

        throw new AuthenticationException('Bad credentials');
    }
}
