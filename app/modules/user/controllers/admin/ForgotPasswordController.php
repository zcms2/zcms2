<?php

namespace ZCMS\Modules\User\Controllers\Admin;

use ZCMS\Core\ZAdminController;

/**
 * Class ForgotPasswordController
 *
 * @package ZCMS\Modules\User\Controllers
 */
class ForgotPasswordController extends ZAdminController
{
    public function indexAction()
    {
        //User has login yet
        if ($this->_user) {
            $this->session->destroy();
        }
    }
}