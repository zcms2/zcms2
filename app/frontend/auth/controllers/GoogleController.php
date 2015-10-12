<?php

namespace ZCMS\Frontend\Auth\Controllers;

use ZCMS\Core\Social\ZGoogle;
use ZCMS\Core\ZFrontController;
use ZCMS\Core\Social\ZSocialHelper;

/**
 * Class IndexController
 *
 * @package ZCMS\Frontend\Auth\GoogleController.php
 */
class GoogleController extends ZFrontController
{
    /**
     * Login callback
     */
    public function loginAction()
    {
        $google = ZGoogle::getInstance();
        if ($google->isReady) {
            $this->_process($google);
        } else {
            $code = $this->request->get('code', 'string', '');
            if ($code) {
                $google->checkRedirectCode($code);
                $status = $this->_process($google);
                if ($status['success'] && $status['message'] == null) {
                    $this->response->redirect('/');
                } elseif ($status['success'] && $status['message'] != null) {
                    $this->flashSession->success($status['message']);
                    $this->response->redirect('/user/login/');
                } elseif (!$status['success']) {
                    $this->flashSession->notice($status['message']);
                    $this->response->redirect('/user/login/');
                }
            } else {
                $this->response->redirect('/');
            }
        }
    }

    /**
     * Process login with Google
     *
     * @param ZGoogle $google
     * @return array
     */
    private function _process($google)
    {
        $userInfo = $google->getUserInfoToCreateAccount();
        return (new ZSocialHelper($userInfo, 'google'))->process();
    }
}