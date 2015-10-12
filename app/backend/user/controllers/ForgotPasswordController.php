<?php

namespace ZCMS\Backend\User\Controllers;

use ZCMS\Core\ZAdminController;

/**
 * Class ForgotPasswordController
 *
 * @package ZCMS\Backend\User\Controllers
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