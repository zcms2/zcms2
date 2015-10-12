<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;

define('BASE_URI', $config->website->baseUri);
define('DEBUG', $config->debug);

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->website->baseUri);
    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir(ROOT_PATH . '/app/install/views/');

    $view->registerEngines([
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions([
                'compiledPath' => ROOT_PATH . '/cache/install/',
                'compiledSeparator' => '_',
                'compileAlways' => true,
                'stat' => false
            ]);

            $compiler = $volt->getCompiler();
            $compiler->addFunction('__', '__');

            return $volt;
        }
    ]);

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    $adapter = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    if ($config->database->adapter == 'Mysql') {
        return new $adapter($config->database->toArray());
    } else {
        return new $adapter([
            'host' => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname' => $config->database->dbname
        ]);
    }
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Set up the flash service (custom with bootstrap)
 */
$di->set('flashSession', function () {
    $flashSession = new FlashSession([
        'warning' => 'alert alert-warning',
        'notice' => 'alert alert-info',
        'success' => 'alert alert-success',
        'error' => 'alert alert-danger'
    ]);
    return $flashSession;
});