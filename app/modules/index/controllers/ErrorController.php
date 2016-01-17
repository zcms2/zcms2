<?php

namespace ZCMS\Modules\Index\Controllers;

use ZCMS\Core\ZFrontController;

/**
 * Class ErrorController
 *
 * @package ZCMS\Frontend\Index\Controllers
 */
class ErrorController extends ZFrontController
{
    /**
     * 404 Not Found
     */
    public function notFound404Action()
    {
        echo '<pre>';
        var_dump(__METHOD__);
        echo '</pre>';
        die();
    }
}