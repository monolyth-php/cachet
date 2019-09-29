<?php

use Gentry\Gentry\Wrapper;

/** Testsuite for Twig function */
return function () : Generator {
    $loader = new Twig\Loader\FilesystemLoader(__DIR__.'/files/templates');
    $twig = new Twig\Environment($loader, ['cache' => false, 'auto_reload' => true, 'debug' => true]);
    Monolyth\Cachet\Twig::inject(__DIR__.'/files/Version.json', $twig);
    /** By default, we can cache-bust assets where extension equals subdirectory */
    yield function () use ($twig) {
        $html = $twig->render('test.html.twig');
        assert(trim($html) === '/js/test.9ed29936.js');
    };

};

