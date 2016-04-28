<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AccessManager
{
    /**
     * @param GetResponseEvent $event
     *
     * @return UserToken|null
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->hasSession() && $serializedToken = $request->getSession()->get('auth_token', false)) {

            return $this->handleAuthToken($serializedToken);
        }

        return new UserToken('Anonymous', array('roles' => ['ROLE_ANONYMOUS']));
    }

    private function handleAuthToken($authToken)
    {
        $token = new UserToken();
        $token->unserialize($authToken);
        $token->setAuthenticated(0 < count(array_intersect(
                array('ROLE_USER', 'ROLE_ADMIN'),
                $token->getUser()->getRoles()
            )));

        return $token;
    }
}
