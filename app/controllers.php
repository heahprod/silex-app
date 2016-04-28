<?php

use App\App;

// Controllers

/** @var App $app */
$app->get('/hello/{name}', function (App $app, $name)
{
    $hello = 'Hello ' . $app->escape($name) . '!';

    return $app->render('main/hello.html.twig', array('say_hello' => $hello));
})
    ->value('name', 'World')
    ->bind('hello')
;

$app->get('/test', function (App $app)
{
    $dump = [];

    /** @var Symfony\Component\Routing\Route $route */
    foreach ($app['routes']->all() as $name => $route) {
        $dump[$name] = $route->getPath();
    }

    return $app->render('main/test.html.twig', ['dump' => $dump]);
})
    ->bind('test')
;

// Import
foreach (glob(__DIR__ . "/../src/Controller/*.php") as $controllers_provider) {
    require_once $controllers_provider;
}
