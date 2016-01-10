<?php

if (is_dir(ROOT_PATH . '/app/install/') && file_exists(ROOT_PATH . '/public/install.php')) {
    if ($_SERVER['SERVER_PORT'] != '443') {
        $baseUri = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
    } else {
        $baseUri = 'https://' . $_SERVER['HTTP_HOST'] . str_replace(['/public/index.php', '/index.php'], '', $_SERVER['SCRIPT_NAME']);
    }
    header("Location:  {$baseUri}/install.php?step=1");
    die();
}

if (strpos($_SERVER['QUERY_STRING'], '_url=/admin/') == false) {
    define('ZCMS_APPLICATION_LOCATION', 'admin');
} else {
    define('ZCMS_APPLICATION_LOCATION', 'frontend');
}