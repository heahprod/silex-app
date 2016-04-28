<?php

require_once '../vendor/autoload.php';

$config = require_once 'config/env.php';

$app = new App\App($config);

include_once 'services.php';
include_once 'listeners.php';

require_once 'controllers.php';

$app->extendsTwig();

$app->boot(); // Cannot change $app further in the code

return $app;
