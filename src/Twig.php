<?php

namespace Monolyth\Cachet;

use Twig\Environment;
use Twig\TwigFunction;
use Monolyth\Envy\Enviroment;

/**
 * Helper class to quickly add the versioning function to your Twig
 * enviroment. Not using Twig? Fine, write your own based on this :)
 */
abstract class Twig
{
    /**
     * @param string $versions Location of the versions file (relative to current
     *  working directory).
     * @param Twig\Environment $twig
     */
    public static function inject(string $versions, Environment $twig) : void
    {
        if (class_exists(Environment::class)) {
            $env = Environment::instance();
        }
        $twig->addFunction(new TwigFunction('version', function ($file) use ($env) {
            if (!isset($env) || !$env->prod) {
                return $file;
            }
            static $versions;
            if (!isset($versions)) {
                $versions = json_decode(file_get_contents(dirname(__DIR__).'/Versions.json'), true);
            }
            $file = preg_replace('@^/@', '', $file);
            return preg_replace('@\.(css|js)$@', ".{$versions[$file]}.\\1", "/$file");
        }));
    }
}

