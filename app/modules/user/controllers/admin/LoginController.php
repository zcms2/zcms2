<?php

namespace ZCMS\Modules\User\Controllers\Admin;

use Phalcon\Validation;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\Models\Users;
use Phalcon\Validation\Validator\Email;

/**
 * Class LoginController
 *
 * @package ZCMS\Modules\User\Controllers
 */
class LoginController extends ZAdminController
{
    /**
     * Login Action
     */
    public function indexAction()
    {
        //User has login yet
        if ($this->_user) {
            $this->session->remove('auth');
            unset($_SESSION);
        }

        //Regular login
        if ($this->request->isPost()) {
            $validation = new Validation();
            $validation->add('email', new Email());

            $messages = $validation->validate($this->request->getPost());
            if (count($messages)) {
                foreach ($messages as $message) {
                    $this->flashSession->error($message);
                }
                return $this->response->redirect('/admin/user/login/');
            }

            $email = $this->request->getPost('email', 'email');
            $password = $this->request->getPost('password', 'string');

            if (Users::login($email, $password)) {
                $this->response->redirect('/admin/');
            } else {
                $this->flashSession->error('m_user_message_login__user_or_password_do_not_match');
                return $this->response->redirect('/admin/user/login/');
            }
        }
        return null;
    }
}
