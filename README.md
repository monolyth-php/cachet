# monolyth/cachet
Cachebuster command

When serving static assets (mainly JavaScript and CSS, but perhaps also
images...) sometimes you'll want to "bust" the browser cache to make sure
visitors receive the correct version. This is what `cachet` aims to help in.

## Installation

Composer (recommended):

```sh
$ composer require monolyth/cachet
```

## Usage
`cachet` depends on a global "versions file" where all current versions are
stored. This should contain a JSON hash of all files/hashes (relative to your
projet's root directory). Initially you can fill it with empty strings as the
hashes; on first run, the actual hashes will be replaced:

```json
{"some/file/somewhere.js":""}
```

Whenever you run the `cachet` CLI command, the current hashes will be inserted
or updated (based on the files' contents):

```sh
$ vendor/bin/cliff cachet path/to/versions.json public/folder
```

The command also creates symlinks with the correct hash inserted into the public
folder, e.g. `test.js` might become `test.abcdabcd.js`. You should refer to
these linked files in your frontend code, as the hash will change when the file
contents have changed.

Note that both arguments are relative to the current working directory.

## Injecting version numbers in templates
Monolyth projects usually use Twig, so we've included a `TwigFunction` `cachet`
to easily inject the correct version numbers.

Somewhere in a central place in your application, inject it using your
`Twig\Enviroment` of choice:

```php
<?php

// For example:
$twig = new Twig\Environment(new Twig\Loader\FilesystemLoader(__DIR__));

//...your other Twig stuff...

Monolyth\Cachet\Twig::inject('path/to/version/file.json', $twig);

```

Note that when injecting the Twig environment, you need to pass the _full_ path
to the versions file since `getcwd()` has an undefined meaning here.

In your Twig template, pass the filename to be cache-busted through the `cachet`
function:

```twig
<script src="/{{ cachet('js/test.js') }}"></script>

```

An optional second parameter to the cachet function may be set to `false` to
disable all cache-busting; this is handy during development. You'll probably
already have a way to determine whether or not the code is running in
production. A fake example:

```twig
<script src="/{{ cachet('js/test.js', isProd()) }}"></script>

```

Note, by the way, the leading slash in the `src` attribute.

## Fault tolerance
The Twig `cachet` function returns the original filename if no such "bustable"
file was defined. This is the same behaviour as when the second argument is
`false`.

If `E_NOTICE` level errors are enabled though (a good idea during development
and testing) an error of level `E_USER_NOTICE` will be triggered.

