<?php
/**
 * ZCMS2 - Power by Phalcon Framework and AdminLTE
 */
try {
    error_reporting(E_ALL);
    ini_set('xdebug.var_display_max_depth', -1);
    ini_set('xdebug.var_display_max_children', -1);
    ini_set('xdebug.var_display_max_data', -1);

    (new Phalcon\Debug())->listen();

    /**
     * Define useful constants
     */
    define('ROOT_PATH', dirname(__DIR__));
    define('APP_DIR', ROOT_PATH . '/app');
    require_once ROOT_PATH . '/app/config/define.php';

    if (is_dir(ROOT_PATH . '/app/install/') && file_exists(ROOT_PATH . '/public/install.php')) {
        if ($_SERVER['SERVER_PORT'] != '443') {
            $baseUri = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
        } else {
            $baseUri = 'https://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
        }
        header("Location:  {$baseUri}/install.php?step=1");
        die();
    }
    /**
     * Require ZCMS Core
     */
    require_once ROOT_PATH . '/app/libraries/Core/ZFactory.php';
    require_once ROOT_PATH . '/app/libraries/Core/ZApplicationInit.php';
    require_once ROOT_PATH . '/app/libraries/Core/ZApplication.php';

    /**
     * Create Application
     */
    $application = new ZCMS\Core\ZApplication();

    /**
     * Run ZCMS Application
     */
    echo $application->run()->getContent();
} catch (Exception $e) {
    echo $e->getMessage();
}