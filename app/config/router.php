<?php
/**
 * Registering a router
 */
$router = new Phalcon\Mvc\Router(false);

//Set default router
$router->setDefaultModule('index');
$router->setDefaultController('index');
$router->setDefaultAction('index');

/**
 * Frontend router
 */
$router->add('/', [
    'namespace' => 'ZCMS\Modules\Index\Controllers\Admin',
    'module' => 'index',
    'controller' => 'index',
    'action' => 'index'
]);

$router->add('/admin[/]?', [
    'namespace' => 'ZCMS\Modules\Dashboard\Controllers\Admin',
    'module' => 'dashboard',
    'controller' => 'index',
    'action' => 'index',
]);

//Load router
if (ZCMS_APPLICATION_LOCATION === 'admin') {
    $router = zcms_load_admin_router($router);
} elseif (ZCMS_APPLICATION_LOCATION === 'frontend') {
    $router = zcms_load_frontend_router($router);
}

$router->notFound([
    'module' => 'index',
    'controller' => 'error',
    'action' => 'notFound404'
]);

return $router;