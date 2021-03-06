<?php

use DevNet\System\Runtime\launcher;

$projectFile = simplexml_load_file(__DIR__ . "/../project.phproj");
$namespace   = $projectFile->properties->namespace;
$entrypoint  = $projectFile->properties->entrypoint;
$packages    = $projectFile->dependencies->package ?? [];

if (PHP_OS_FAMILY == 'Windows') {
    $path  = getenv('path');
    $paths = explode(';', $path);
} else {
    $path  = getenv('PATH');
    $paths = explode(':', $path);
}

foreach ($paths as $path) {
    if (file_exists($path . '/../autoload.php')) {
        require $path . '/../autoload.php';
        break;
    }
}

foreach ($packages as $package) {
    $include = (string)$package->attributes()->include;
    if (file_exists(__DIR__ . '/../' . $include)) {
        require __DIR__ . '/../' . $include;
    }
}

$launcher = launcher::getLauncher();
$launcher->workspace(dirname(__DIR__));
$launcher->namespace((string)$namespace);
$launcher->entryPoint((string)$entrypoint);
$launcher->launch();
