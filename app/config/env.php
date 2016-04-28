<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\Yaml\Yaml;

// Customize to your needs
if (getenv('TEST')) {
    $env = 'test';
} else {
    $env = '127.0.0.1' === $_SERVER['REMOTE_ADDR'] ? 'dev' : 'prod';
}

if ('dev' === $env) {
    Debug::enable();
}

$config = file_exists(__DIR__.'/'.$env.'.yml') ? Yaml::parse(file_get_contents(__DIR__.'/'.$env.'.yml')) : array();
$parameters = file_exists(__DIR__.'/parameters.yml') ? Yaml::parse(file_get_contents(__DIR__.'/parameters.yml')) : array();

return array_merge($parameters, $config, array('env' => $env));
