<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        ROOT_PATH . '/app/install/controllers/'
    )
)->register();

//Register default namespaces
$loader->registerNamespaces([
    'ZCMS\Core' => ROOT_PATH . '/app/libraries/Core/',
])->register();
