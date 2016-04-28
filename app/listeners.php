<?php

/* Raw Listeners

// 1. As a callable
$app->on('event.name', function (EventClass $event) use ($app)
{
    // do something
}, 0);

// 2. As a protected callable service
$app['listener'] = $app->protect(function (EventClass $event) use ($app)
{
    // do something
});

$app->on('event.name', $app['listener'], 0);

// 3. As a EventSubscriberInterface
$app['subscriber.class'] = function ($app, array $params = [])
{
    if ($app['config']) {
        return new SpecificSubscriberClass($params);
    }

    return new SubscriberClass();
};

$app['dispatcher']->addSubscriber('event.name', $app['listener'], 0);
*/
