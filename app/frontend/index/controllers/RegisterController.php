<?php

namespace ZCMS\Frontend\Index\Controllers;

use ZCMS\Core\Models\CoreOptions;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Utilities\ZReCaptcha;
use ZCMS\Core\ZEmail;
use ZCMS\Core\ZFrontController;
use ZCMS\Core\ZSEO;

/**
 * Class RegisterController
 *
 * @package ZCMS\Frontend\Index\Controllers
 */
class RegisterController extends ZFrontController
{
    /**
     * Register account
     */
    public function indexAction()
    {
        if ($this->_user) {
            $this->response->redirect('/');
            return;
        }

        ZSEO::getInstance()->setTitle('Đăng ký tài khoản')
            ->setDescription('Nhanh tay đăng ký tài khoản để thi tài với các thành viên trong hệ thông hoặc thi thử TOEIC trực tuyến')
            ->setKeywords('đăng ký tài khoản, đăng ký thành viên');

        /**
         * @var UserRoles $role
         */
        $role = UserRoles::findFirst('location = 0 AND is_default = 1');
//        $role = UserRoles::findFirst("location = 'frontend' AND is_default = 1");

        if (!$role) {
            $this->flashSession->error(__('System error role for user, please contact admin'));
            $this->response->redirect('/user/login/');
            return;
        }

        if ($this->request->isPost()) {
            $reCaptcha = new ZReCaptcha('6LctxwwTAAAAAG4_N5_mKxoLsBv8y7w1RCSUs49L');
            $resp = $reCaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if ($resp->isSuccess()) {
                //Verified captcha!
                $password = $this->request->getPost('password', 'string');
                $confirmPassword = $this->request->getPost('confirm_password', 'string');

                if (strlen($password) < 6) {
                    $this->flashSession->error('Password must than 6 characters');
                    return;
                }

                if (strlen($password) > 32) {
                    $this->flashSession->error('Password must less than 32 characters');
                    return;
                }

                if ($password != $confirmPassword) {
                    $this->flashSession->error('Password confirmation invalid');
                    return;
                } else {
                    $user = new Users();
                    $user->is_active = 0;
                    $user->first_name = $this->request->getPost('first_name', ['string', 'striptags'], '');
                    $user->last_name = $this->request->getPost('last_name', ['string', 'striptags'], '');
                    $user->email = $this->request->getPost('email', ['string', 'striptags'], '');
                    $user->generatePassword($password);
                    $user->role_id = $role->role_id;
                    $sendEmailActiveAccount = CoreOptions::getOptions('send_email_active_account', 'zcms', 1);
                    if ($sendEmailActiveAccount) {
                        $user->active_account_token = randomString(255) . md5($user->email) . time();
                    } else {
                        $user->is_active = 1;
                    }

                    if ($user->save()) {
                        $data = $user->toArray();
                        $data['password'] = '[Mật khẩu bạn đăng ký]';
                        if ($sendEmailActiveAccount) {//Send email active account
                            $this->flashSession->success('Register account successfully, Please check email to active your account. Thank you!');
                            ZEmail::getInstance()
                                ->addTo($user->email, $user->display_name)
                                ->setSubject(__('Activate account'))
                                ->setTemplate('index', 'activation_account', $data)->send();
                        } else {
                            ZEmail::getInstance()
                                ->addTo($user->email, $user->display_name)
                                ->setSubject(__('Register account success'))
                                ->setTemplate('index', 'register_account_success', $data)->send();
                            $this->flashSession->success('Register account successfully. Thank you');
                        }
                        $this->response->redirect('/user/login/');
                        return;
                    } else {
                        $this->setFlashSession($user->getMessages(), 'error');
                    }
                }
            } else {
                //$errors = $resp->getErrorCodes();
                $this->flashSession->warning('Mã xác nhận không chính xác!');
                return;
            }
        }
        $this->view->setVar('siteKey', $this->config->reCaptcha->siteKey);
        return;
    }

    public function activateAccountAction()
    {
        if ($this->isLogin()) {
            $this->response->redirect('/');
            return;
        }

        $token = $this->request->get('token', 'string', '');
        if ($token && strlen($token) > 0) {
            /**
             * @var $user Users
             */
            $user = Users::findFirst([
                'conditions' => "active_account_token = ?0",
                'bind' => [$token]
            ]);
            if ($user) {
                $user->active_account_token = '';
                $user->active_account_at = date('Y-m-d H:i:s');
                $user->is_active = 1;
                $data = $user->toArray();
                if ($user->save()) {
                    ZEmail::getInstance()
                        ->addTo($user->email, $user->display_name)
                        ->setSubject(__('Register account success'))
                        ->setTemplate('index', 'register_account_success', $data)->send();
                    $user->loginCurrentUSer();
                    $this->response->redirect('/');
                    return;
                } else {
                    $this->flashSession->notice('System is busy, please try again later');
                }
            } else {
                $this->flashSession->notice('Token active account error');
            }
        } else {
            $this->flashSession->notice('Token active account error');
        }
        $this->response->redirect('/user/login/');
        return;
    }
}