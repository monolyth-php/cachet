<?php

namespace Monolyth\Cachet;

use Monolyth\Cliff;

/**
 * Command to bust the cached versions of static assets (usually, Javascript and
 * stylesheets).
 */
class Command extends Cliff\Command
{
    /**
     * The name of the versions file is _always_ considered relative to the
     * current working directory. The same goes for the public directory.
     * The file extension is typically `js` or `css`, but in theory any
     * extension is allowed. The subdirectory can be specified (e.g. `styles` or
     * `scripts` but defaults to the file extension.
     *
     * @param string $versionFile
     * @param string $public
     * @param string $extension
     * @param string $subdir Optional, defaults to the value of `$extension`.
     * @return void
     */
    public function __invoke(string $versionFile, string $public, string $extension, string $subdir = null) : void
    {
        $subdir = $subdir ?? $extension;
        $versions = json_decode(file_get_contents(getcwd()."/$versionFile"), true);
        foreach ($versions as $file => &$hash) {
            // The versions file has _all_ versioned files, but we only want to
            // bust those that have been asked for in the CLI arguments.
            if (!preg_match("@^$subdir/@", $file)) {
                continue;
            }

            $newHash = substr(sha1(file_get_contents(getcwd()."/$public/$file")), 0, 8);
            $new = preg_replace("@$subdir/(.*?)\.(css|js)$@", "\\1.$newHash.\\2", $file);
            $glob = preg_replace("@\.($extension)$@", '.*.\\1', $file);
            $files = glob(getcwd()."/$public/$glob");
            foreach ($files as $existingFile) {
                unlink($existingFile);
            }
            $old = preg_replace("@^$subdir/@", '', $file);
            $olddir = getcwd();
            chdir(getcwd()."/$public/$subdir");
            symlink($old, $new);
            chdir($olddir);
            $hash = $newHash;
        }
        file_put_contents(getcwd()."/$versionFile", json_encode($versions));
    }
}

