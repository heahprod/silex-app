<?php

namespace App\Provider;

use App\App;
use App\Listener\AuthenticationListener;
use App\Security\AccessManager;
use App\Security\Authenticator;
use App\Security\UserProvider;
use App\Security\UserToken;
use Silex\ServiceProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SecurityProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app A container instance
     */
    public function register(Application $app)
    {
        $app['security.token_storage'] = $app->share(function ()
        {
            return new TokenStorage();
        });

        $app['security.encoder.digest'] = $app->share(function ()
        {
            return new MessageDigestPasswordEncoder('sha1', false, 1);
        });

        $app['security.user_provider'] = $app->share(function ($app)
        {
            return new UserProvider($app['security_config']);
        });

        // Authentication
        $app['security.authenticator'] = $app->share(function ($app)
        {
            return new Authenticator($app['security.user_provider'], $app['security.encoder.digest']);
        });

        //Authorization
        $app['security.access_manager'] = $app->share(function ()
        {
            return new AccessManager();
        });

        // Firewall
        $app['security.authentication_listener'] = $app->share(function ($app)
        {
            return new AuthenticationListener($app['security.token_storage'], $app['security.access_manager']);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        $app->mount('/', $this->connect($app));

        $app['dispatcher']->addSubscriber($app['security.authentication_listener']);
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var \Silex\ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->match('/login', function (App $app, Request $request)
        {
            /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
            $session = $app['session'];

            if ($session->get('logout', false)) {
                $session->remove('logout');
            }
            else if ($username = $request->request->get('_username', false)) {

                try {
                    /** @var UserToken $token */
                    $token = $app['security.authenticator']->authenticate($username, $request->request->get('_password'));
                    $session->set('auth_token', $token->serialize());

                    return $app->redirect('/');
                }
                catch (AuthenticationException $e) {
                    // Todo
                }
            }

            return $app->render('/main/login.html.twig');
        })
            ->bind('login');

        $controllers->get('/logout', function (Application $app)
        {
            $app['session']->set('logout', true);
            $app['session']->remove('auth_token');

            return $app->redirect('/login');
        })
            ->bind('logout');

        return $controllers;
    }
}
