<?php

namespace Monolyth\Cachet;

use Twig\Environment;
use Twig\TwigFunction;

/**
 * Helper class to quickly add the versioning function to your Twig
 * enviroment. Not using Twig? Fine, write your own based on this :)
 */
abstract class Twig
{
    /**
     * @param string $versionFile Location of the versions file (full path).
     * @param Twig\Environment $twig
     */
    public static function inject(string $versionFile, Environment $twig) : void
    {
        /**
         * @param string $file The asset file to cache bust.
         * @param bool $bust Whether or not to cache-bust. Defaults to true, use
         *  false e.g. during development.
         */
        $twig->addFunction(new TwigFunction('cachet', function (string $file, bool $bust = true) use ($versionFile) : string {
            if (!$bust) {
                return $file;
            }
            static $versions;
            if (!isset($versions)) {
                $versions = json_decode(file_get_contents($versionFile), true);
            }
            $file = preg_replace('@^/@', '', $file);
            return preg_replace('@\.(css|js)$@', ".{$versions[$file]}.\\1", "/$file");
        }));
    }
}

