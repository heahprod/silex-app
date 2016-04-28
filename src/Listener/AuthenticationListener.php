<?php

namespace App\Listener;

use App\Security\AccessManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AuthenticationListener implements EventSubscriberInterface
{
    private $tokenStorage;
    private $accessManager;

    public function __construct(TokenStorage $tokenStorage, AccessManager $accessManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->accessManager = $accessManager;
    }

    public function checkAuthentication(GetResponseEvent $event)
    {
        /** @var \App\Security\UserToken $authorizedToken */
        $authorizedToken = $this->accessManager->handle($event);

        if (null !== $authorizedToken && $authorizedToken->isAuthenticated()) {
            $this->tokenStorage->setToken($authorizedToken);
        }
        else if (!preg_match('#/log(in|out)#', $event->getRequest()->getRequestUri())) {
            $event->setResponse(new RedirectResponse('/login'));
        }
    }

    /**
     * {@inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return ['kernel.request' => ['checkAuthentication', 64]];
    }
}
