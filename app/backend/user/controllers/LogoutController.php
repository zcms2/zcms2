<?php

namespace ZCMS\Backend\User\Controllers;

use ZCMS\Core\ZAdminController;

/**
 * Class LogoutController
 *
 * @package ZCMS\Backend\User\Controllers
 */
class LogoutController extends ZAdminController
{
    /**
     * Logout Action
     */
    public function indexAction()
    {
        unset($_SESSION);
        $this->session->destroy();
        $this->response->redirect('/admin/user/login/');
        return;
    }
}