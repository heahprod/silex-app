<?php

namespace App\Provider;

use Silex\Application;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

class WebProfilerProvider extends WebProfilerServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        if (isset($app['security.token_storage']) && class_exists('Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector')) {
            $app['data_collectors'] = $app->share($app->extend('data_collectors', function ($collectors) {
                $collectors['security'] = function ($app) { return new SecurityDataCollector($app['security.token_storage']); };

                return $collectors;
            }));

            $app['data_collectors.templates'] = $app->share($app->extend('data_collector.templates', function ($templates) {
                $templates[] = ['security', '@Security/Collector/security.html.twig'];

                return $templates;
            }));

            $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
                if ($app['profiler.templates_path.security']) {
                    $loader->addPath($app['profiler.templates_path.security'], 'Security');
                }

                return $loader;
            }));
        }

        $app['profiler.templates_path.security'] = $app->share(function () {
            $r = new \ReflectionClass('Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector');

            return dirname(dirname($r->getFileName())).'/Resources/views';
        });
    }
}
