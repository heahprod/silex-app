<?php

use Silex\Provider;

// Register default providers
$app
    ->register(new Provider\DoctrineServiceProvider(), array(
        'db.options' => $app['db_config'],
    ))
    ->register(new Provider\FormServiceProvider())
    ->register(new Provider\HttpFragmentServiceProvider())
    ->register(new Provider\ServiceControllerServiceProvider())
    ->register(new Provider\SessionServiceProvider(), array(
        'session.test' => 'test' === $app['env'],
    ))
    ->register(new Provider\TranslationServiceProvider())
    ->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views',
        'twig.options' => array(
            'debug' => $app['debug'],
            'cache' => __DIR__.'/../var/cache/twig',
        ),
    ))
    ->register(new Provider\UrlGeneratorServiceProvider())
    ->register(new Provider\ValidatorServiceProvider())
;

$app->register(new \App\Provider\SecurityProvider($app['security_config']));

if ($app['debug']) {
    $app->register(new \App\Provider\WebProfilerProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../var/profiler/'.$app['env'].'/',
    ));
}


/* Custom Raw Services

// 1. Parameter
$app['test.param'] = 'parameter';

// 2. Constructed service
$app['a_service'] = new SomeService();

// 3. Dynamically constructed (once) and shared service
$app['test.class'] = $app->share(function ($app, array $parameters = [])
{
    if ($app['test.config']) {
        return new TestClass($parameters);
    }

    return new OtherTestClass();
});

// 4. Callable service
$app['service'] = $app->protect(function ($parameter) use ($app)
{
    // do something
});

// 5. Factory
$app['a_service.factory'] = $app->factory(function ()
{
    return new SomeService();
});
*/
