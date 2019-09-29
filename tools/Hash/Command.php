<?php

namespace Tools\Hash;

use Monolyth\Cliff;

/**
 * Helper command to generate a hash.
 */
class Command extends Cliff\Command
{
    /**
     * Generate the hash for the given file.
     *
     * @param string $file Relative to current working directory.
     */
    public function __invoke(string $file) : void
    {
        $hash = sha1(file_get_contents(getcwd()."/$file"));
        echo "\n\nHash: ".substr($hash, 0, 8)."\n\n";
    }
}

