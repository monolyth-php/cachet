<?php

use Gentry\Gentry\Wrapper;

/** Testsuite for cache busting command */
return function () : Generator {
    $object = Wrapper::createObject(Monolyth\Cachet\Command::class);
    /** By default, we can cache-bust assets where extension equals subdirectory */
    yield function () use ($object) {
        $result = $object->__invoke('tests/files/Version.json', 'tests/files/public', 'js');
        assert(true);
    };
    /** We can also override the subdirectory to something of our choice */
    yield function () use ($object) {
        $result = $object->__invoke('tests/files/Version.json', 'tests/files/public', 'js', 'scripts');
        assert(true);
    };

};

