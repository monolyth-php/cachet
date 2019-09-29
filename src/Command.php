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
     *
     * @param string $versionFile
     * @param string $public
     * @return void
     */
    public function __invoke(string $versionFile, string $public) : void
    {
        $versions = json_decode(file_get_contents(getcwd()."/$versionFile"), true);
        foreach ($versions as $file => &$hash) {
            $newHash = substr(sha1(file_get_contents(getcwd()."/$public/$file")), 0, 8);
            $subdir = substr($file, 0, strrpos($file, '/'));
            $new = preg_replace("@$subdir/(.*?)\.([a-z]{1,})$@", "\\1.$newHash.\\2", $file);
            $glob = preg_replace("@\.([a-z]{1,})$@", '.*.\\1', $file);
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

