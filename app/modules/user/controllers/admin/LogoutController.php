<?php

namespace ZCMS\Modules\User\Controllers\Admin;

use ZCMS\Core\ZAdminController;

/**
 * Class LogoutController
 *
 * @package ZCMS\Modules\User\Controllers
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