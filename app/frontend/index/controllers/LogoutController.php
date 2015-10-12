<?php

namespace ZCMS\Frontend\Index\Controllers;

use ZCMS\Core\ZFrontController;

/**
 * Class LogoutController
 *
 * @package ZCMS\Frontend\User\Controllers
 */
class LogoutController extends ZFrontController
{
    /**
     * Logout Action
     */
    public function indexAction()
    {
        unset($_SESSION);
        $this->session->destroy();
        $this->response->redirect('/user/login/');
        $this->flashSession->success('You are logged out');
        return;
    }
}