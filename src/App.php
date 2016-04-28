<?php

namespace App;

use Silex\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * App.
 *
 * @author Jules Pietri <jules@heahprod.com>
 */
class App extends Application
{
    use Application\SecurityTrait;
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;

    public function extendsTwig()
    {
        $this['twig'] = $this->share($this->extend('twig', function($twig) {
            // Globals
            /** @var \Twig_Environment $twig */
            $twig->addGlobal('env', $this['env']);

            // Filters
            $twig->addFilter('yaml_encode', new \Twig_SimpleFilter('yaml_encode', function (array $var) {
                return Yaml::dump($var);
            }));

            // Functions
            $twig->addFunction('yaml_encode', new \Twig_SimpleFunction('yaml_encode', function (array $var) {
                return Yaml::dump($var);
            }));

            $twig->addFunction('yaml_decode', new \Twig_SimpleFunction('yaml_decode', function ($var) {
                $var = is_array($var) ? $var : (array) $var;

                return Yaml::dump($var, 1);
            }));

            return $twig;
        }));
    }
}
