<?php

namespace ZCMS\Backend\User\Controllers;

use ZCMS\Core\Models\Users;
use ZCMS\Core\ZAdminController;
use ZCMS\Backend\User\Forms\UserProfileForm;

/**
 * Class ProfileController
 *
 * @package ZCMS\Backend\User\Controllers
 */
class ProfileController extends ZAdminController
{
    /**
     * Profile view and edit
     *
     * @return bool
     */
    public function indexAction()
    {
        /**
         * @var $userData Users
         */
        $userData = Users::findFirst('user_id = ' . $this->_user['id']);

        $this->view->setVar('avatar', $userData->avatar);

        //If id not exist
        if (!$userData) {
            $this->flashSession->notice('m_system_user_message_user_not_exist');
            return $this->response->redirect('/admin/user/profile/');
        }

        $this->_toolbar->addSaveButton();

        $oldUserData = clone $userData;
        $userData->password = null;
        $form = new UserProfileForm($userData);
        $this->view->setVar('form', $form);
        if ($this->request->isPost()) {
            if ($form->isValid($_POST, $userData)) {
                $userData->email = $oldUserData->email;
                $newPassword = $this->request->getPost('password', 'string');
                $currentPassword = $this->request->getPost('current_password', 'string');
                $_POST['current_password'] = '';
                $_POST['password'] = '';
                $_POST['password_confirmation'] = '';
                if ($newPassword != '') {
                    if (Users::checkPassword($currentPassword, $userData->salt, $oldUserData->password)) {
                        $userData->generatePassword($newPassword);
                    } else {
                        $this->flashSession->notice('m_user_message_current_password_not_fount');
                        return null;
                    }
                } else {
                    $userData->password = $oldUserData->password;
                }
                if ($userData->save()) {
                    $this->_user['full_name'] = $userData->first_name . ' ' . $userData->last_name;
                    $avatarName = $this->uploadAvatar($userData);
                    if ($avatarName) {
                        $userData->avatar = $avatarName;
                        $userData->save();
                        $this->_user['avatar'] = $userData->avatar;
                    }
                    $this->session->set('auth', $this->_user);
                    $this->flashSession->success('m_user_message_update_user_successfully');
                    $this->response->redirect('/admin/user/profile/');
                    return true;
                } else {
                    $this->setFlashSession($userData->getMessages(), 'error');
                    $_POST['password'] = '';
                    $_POST['password_confirmation'] = '';
                    $this->flashSession->notice('m_system_user_message_update_user_failed');
                }
            } else {
                $this->setFlashSession($form->getMessages(), 'notice');
            }
        }


        return true;
    }

    /**
     * Upload avatar
     *
     * @param $user Users
     * @return string
     */
    private function uploadAvatar($user)
    {
        /**
         * @var \Phalcon\Http\Request\File[] $files
         */
        $files = $this->request->getUploadedFiles();
        if (count($files)) {
            foreach ($files as $file) {
                if ($file->getKey() == 'avatar' && $file->getName() != null) {
                    $file_name = $file->getName();
                    $file_size = $file->getSize();
                    $file_type = $file->getRealType();
                    $extension = '.' . pathinfo($file_name)['extension'];

                    //Check file type
                    if (substr($file_type, 0, 5) != 'image') {
                        $this->flashSession->notice('');
                        return false;
                    }

                    //Check file size
                    if ($file_size > MAX_AVATAR_SIZE_UPLOAD) {
                        $this->flashSession->notice(__('m_user_message_avatar_lager_than_x_mb', [MAX_AVATAR_SIZE_UPLOAD / (1024 * 1024)]));
                        return false;
                    }

                    $newAvatarName = md5($this->security->hash($user->user_id . time())) . $extension;

                    if (!is_dir(ROOT_USER_AVATAR_FOLDER_UPLOAD)) {
                        mkdir(ROOT_USER_AVATAR_FOLDER_UPLOAD, 0755, true);
                    }

                    if ($file->moveTo(ROOT_USER_AVATAR_FOLDER_UPLOAD . DS . $newAvatarName)) {
                        if ($user->avatar != '') {
                            $user->avatar = generateAlias($user->avatar, '-', 1000);
                            if (file_exists($user->avatar)) {
                                unlink(ROOT_USER_AVATAR_FOLDER_UPLOAD . DS . $user->avatar);
                            }
                        }
                        return USER_AVATAR_FOLDER_UPLOAD . DS . $newAvatarName;
                    }
                }
            }
        }
        return false;
    }
}