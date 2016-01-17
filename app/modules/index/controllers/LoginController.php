<?php

namespace ZCMS\Modules\Index\Controllers;

use Phalcon\Validation;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Social\ZFacebook;
use ZCMS\Core\Social\ZGoogle;
use ZCMS\Core\Utilities\ZReCaptcha;
use ZCMS\Core\ZFrontController;
use Phalcon\Validation\Validator\Email;
use ZCMS\Core\ZSEO;

/**
 * Class LoginController
 *
 * @package ZCMS\Frontend\Index\Controllers
 */
class LoginController extends ZFrontController
{
    /**
     * User login
     */
    public function indexAction()
    {
        //User has login yet
        if ($this->_user) {
            $this->session->remove('auth');
            unset($_SESSION);
        }
        ZSEO::getInstance()->setTitle('Đăng nhập tài khoản')
            ->setDescription('Đăng nhập để cùng tham gia so tài với các thành viên trong hệ thống, thi thử TOEIC trực tuyến')
            ->setKeywords('đăng nhập');

        $this->_addSocialLogin();

        //Regular login
        if ($this->request->isPost()) {
            $validation = new Validation();
            $validation->add('email', new Email());

            $messages = $validation->validate($this->request->getPost());
            if (count($messages)) {
                foreach ($messages as $message) {
                    $this->flashSession->error($message);
                }
                $this->response->redirect('/user/login/');
                return;
            }

            $email = strtolower($this->request->getPost('email', 'email'));
            $password = $this->request->getPost('password', 'string');

            $status = Users::login($email, $password);
            if ($status === true) {
                $user = Users::getCurrentUser();
                $this->flashSession->success('Hi, ' . $user['full_name'] . '!');
                if ($this->session->get('_redirect_from')) {
                    $this->response->redirect(dirname(BASE_URI) . $this->session->get('_redirect_from'));
                } else {
                    $this->response->redirect('/');
                }
            } elseif ($status === false) {
                $this->flashSession->error('User or password not match');
                $this->response->redirect('/user/login/');
            } else {
                $this->flashSession->error('Your account is not active yet');
                $this->response->redirect('/user/login/');
            }
        }
    }

    /**
     * Add social login
     */
    private function _addSocialLogin()
    {
        $isSocialLogin = false;
        if ($this->config->social->facebook->appID) {
            $fb = ZFacebook::getInstance();
            $helper = $fb->getRedirectLoginHelper();
            $permissions = $this->config->social->facebook->permissions->toArray();
            $this->view->setVar('facebookLoginUrl', $helper->getLoginUrl(BASE_URI . '/auth/facebook/login-callback/', $permissions));
            $isSocialLogin = true;
        }

        if ($this->config->social->google->clientID) {
            $google = ZGoogle::getInstance();
            $this->view->setVar('googleLoginUrl', $google->getAuthUrl());
            $isSocialLogin = true;
        }

        $this->view->setVar('isSocialLogin', $isSocialLogin);
    }


}