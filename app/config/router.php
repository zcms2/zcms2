<?php
/**
 * Registering a router
 */
$router = new \Phalcon\Mvc\Router(false);

//Set default router
$router->setDefaultModule('index');
$router->setDefaultController('index');
$router->setDefaultAction('index');

/**
 * Frontend router
 */
$router->add('/', [
    'module' => 'index',
    'controller' => 'index',
    'action' => 'index'
]);

//Load router frontend
$router = zcms_load_frontend_router($router);

/**
 * Backend router
 */
$router->add('/admin[/]?', [
    'module' => 'admin',
    'controller' => 'index',
    'action' => 'index',
]);

$router->add('/admin/:module/:controller/:action/:params', [
    'module' => 1,
    'controller' => 2,
    'action' => 3,
    'params' => 4,
]);

$router->add('/admin/:module/:controller/:action', [
    'module' => 1,
    'controller' => 2,
    'action' => 3,
]);

$router->add('/admin/:module/:controller[/]?', [
    'module' => 1,
    'controller' => 2,
    'action' => 'index',
]);

$router->add('/admin/:module[/]?', [
    'module' => 1,
    'controller' => 'index',
    'action' => 'index',
]);

//404 not found
$router->notFound([
    'module' => 'index',
    'controller' => 'error',
    'action' => 'notFound404'
]);

return $router;