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
     * The name of the versions file is _allways_ considered relative to the
     * current working directory. The same goes for the public directory.
     * The file extension is typically `js` or `css`, but in theory any
     * extension is allowed. The subdirectory can be specified (e.g. `styles` or
     * `scripts` but defaults to the file extension.
     */
    public function __invoke(string $versions, string $public, string $extension, string $subdir = null) : void
    {
        $subdir = $subdir ?? $extension;
        $versions = json_decode(file_get_contents(getcwd()."/$versions"), true);
        foreach ($versions as $file => $hash) {
            // The versions file has _all_ versioned files, but we only want to
            // bust those that have been asked for in the CLI arguments.
            if (!preg_match("@^$subdir/@", $file)) {
                continue;
            }

            $new = preg_replace("@$subdir/(.*?)\.(css|js)$@", "\\1.$hash.\\2", $file);
            if (!file_exists(getcwd()."/$public/$subdir/$new")) {
                $glob = preg_replace("@\.($extension)$@", '.*.\\1', $file);
                exec("unlink ".getcwd()."/$public/$subdir/$glob");
                $old = preg_replace("@^$subdir/@", '', $file);
                $olddir = getcwd();
                chdir(getcwd()."/$public/$subdir");
                symlink($old, $new);
                chdir($olddir);
            }
        }
    }
}
