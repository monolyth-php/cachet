<?php

use Gentry\Gentry\Wrapper;

/** Testsuite for cache busting command */
return function () : Generator {
    $command = Wrapper::createObject(Monolyth\Cachet\Command::class);
    /** By default, we can cache-bust assets where extension equals subdirectory */
    yield function () use ($command) {
        $command->__invoke('tests/files/Version.json', 'tests/files/public', 'js');
        assert(file_exists('tests/files/public/js/test.d02bc52d.js'));
        assert(!file_exists('tests/files/public/js/test.dummy.js'));
        exec("cd tests/files/public/js && ln -s test.js test.dummy.js && cd -");
    };
    /** We can also override the subdirectory to something of our choice */
    yield function () use ($command) {
        $command->__invoke('tests/files/Version.json', 'tests/files/public', 'js', 'scripts');
        assert(file_exists('tests/files/public/scripts/test.d02bc52d.js'));
        assert(!file_exists('tests/files/public/scripts/test.dummy.js'));
        exec("cd tests/files/public/scripts && ln -s test.js test.dummy.js && cd -");
    };

};

