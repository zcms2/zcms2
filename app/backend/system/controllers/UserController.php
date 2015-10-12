<?php

namespace ZCMS\Backend\System\Controllers;

use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Backend\System\Forms\UserForm;

/**
 * Class UserController
 *
 * @package ZCMS\Backend\System\Controllers
 */
class UserController extends ZAdminController
{
    /**
     * @var string PHQL Model
     */
    public $_model = 'ZCMS\Core\Models\Users';

    /**
     * @var string Model name in database
     */
    public $_modelBaseName = 'users';

    /**
     * List view
     */
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addEditButton();
        $this->_toolbar->addNewButton();
        $this->_toolbar->addDeleteButton();

        //Add filter
        $this->addFilter('filter_order', 'first_name', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_search', '', 'string');
        $this->addFilter('filter_role', '', 'string');

        /**
         * @var UserRoles[] $roles
         */
        $roles = UserRoles::find();
        $rolesData = ['' => __('gb_select_role')];
        foreach ($roles as $role) {
            $rolesData[$role->role_id] = $role->name;
        }

        $this->view->setVar('rolesData', $rolesData);

        //Get all filter
        $filter = $this->getFilter();

        //echo '<pre>'; var_dump($filter);echo '</pre>'; die();

        $conditions = [];
        //$conditions[] = 'user_id != ' . Users::getCurrentUser()['id'];

        if (trim($filter['filter_role'])) {
            $conditions[] = "role_id = " . intval($filter['filter_role']);
        }

        $filter['filter_search'] = trim($filter['filter_search']);

        if ($filter['filter_search']) {
            $conditions[] = "CONCAT(first_name, ' ', last_name) ILIKE '%" . $filter['filter_search'] . "%' OR email like '%" . $filter['filter_search'] . "%'";
        }

        //Get all user
        $items = Users::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        $currentPage = $this->request->getQuery('page', 'int');
        $paginationLimit = $this->config->pagination->limit;

        $filter_location = [
            '' => __('gb_select_location'),
            'backend' => __('gb_backend'),
            'frontend' => __('gb_frontend')
        ];

        //Set filter to view
        $this->view->setVar('filter_location', $filter_location);

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);
        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'user_id'
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'link',
                'title' => 'gb_user_first_name',
                'link' => '/admin/system/user/edit/',
                'link_prefix' => 'user_id', //Use id => link = BASE /admin/system/user/edit/$id/
                'access' => $this->acl->isAllowed('system|user|edit'),
                'column' => 'first_name'
            ],
            [
                'type' => 'link',
                'title' => 'gb_user_last_name',
                'column' => 'last_name',
                'link' => '/admin/system/user/edit/',
                'link_prefix' => 'user_id',
                'access' => $this->acl->isAllowed('system|user|edit'),
            ],
            [
                'type' => 'text',
                'title' => 'gb_user_email',
                'column' => 'email'
            ],
            [
                'type' => 'text',
                'title' => 'Role',
                'column' => 'role_id',
                'array_values' => $rolesData
            ],
            [
                'type' => 'active',
                'title' => 'gb_is_active',
                'column' => 'is_active',
                'access' => $this->acl->isAllowed('system|user|active'),
                'link' => '/admin/system/user/'
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'user_id'
            ]
        ]);
    }

    /**
     * Published item action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        if ($this->_model && $this->_modelBaseName) {
            $extraQuery = null;
            if ($log) {
                $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
            }
            if ($id) {
                $id = intval($id);
                $ids[] = $id;
            } else {
                $ids = $this->request->getPost('ids');
                ZArrayHelper::toInteger($ids);
            }
            if (is_array($ids)) {
                $query = "UPDATE {$this->_modelBaseName} SET published = 1 " . $extraQuery . " WHERE id IN (" . implode(',', $ids) . ")  AND is_supper_admin != 1";
                $this->db->execute($query);
                $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_published', ["1" => $this->db->affectedRows()]));
            }
        } else {
            $this->flashSession->error('gb_message_you_must_set_model_in_child_controller');
        }
        if ($redirect) {
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
        }
    }

    /**
     * Unpublished item action
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     */
    public function unPublishAction($id = null, $redirect = null, $log = true)
    {
        if ($this->_model && $this->_modelBaseName) {
            $extraQuery = null;
            if ($log) {
                $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
            }
            if ($id) {
                $id = intval($id);
                $ids[] = $id;
            } else {
                $ids = $this->request->getPost('ids');
                ZArrayHelper::toInteger($ids);
            }
            if (is_array($ids)) {
                $query = "UPDATE {$this->_modelBaseName} SET published = 0 " . $extraQuery . " WHERE id IN (" . implode(',', $ids) . ")  AND is_supper_admin <> 1";
                $this->db->execute($query);
                $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_unpublished', ["1" => $this->db->affectedRows()]));
            }
        } else {
            $this->flashSession->error('gb_message_you_are_must_set_model_in_child_controller');
        }
        if ($redirect) {
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
        }
    }

    /**
     * Add new role
     * @return null|\Phalcon\Http\ResponseInterface
     */
    public function newAction()
    {
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        $this->view->setVar('admin_role', UserRoles::find());
        $form = new UserForm();
        $this->view->setVar('form', $form);

        if ($this->request->isPost()) {
            $user = new Users();
            if ($form->isValid($_POST, $user)) {
                $user->generatePassword($user->password);
                if ($user->save() == false) {
                    $this->flashSession->error('m_system_user_message_new_user_was_created_fail');
                    $this->setFlashSession($user->getMessages(), 'error');
                } else {
                    $this->flashSession->success('m_system_user_message_new_user_was_created_successfully');
                    return $this->response->redirect('/admin/system/user/');
                }
            } else {
                $this->flashSession->error('gb_please_check_required_filed');
            }
        }
        return null;
    }

    /**
     * Edit role
     *
     * @param int $id
     * @return null
     */
    public function editAction($id)
    {
        $id = intval($id);

        //Add toolbar button
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        /**
         * @var Users $currentEditUser
         */
        $currentEditUser = Users::findFirst($id);

        //If id not exist
        if (!$currentEditUser || $currentEditUser->user_id == Users::getCurrentUser()['id']) {
            $this->flashSession->error('m_system_user_message_user_not_exist');
            $this->response->redirect('/admin/system/user/');
            return null;
        }

        $oldUserInfo = clone $currentEditUser;

        $currentEditUser->password = null;
        $form = new UserForm($currentEditUser);

        $this->view->setVar('admin_role', UserRoles::find());

        if ($this->request->isPost()) {
            if ($_POST['password'] == '' && $_POST['password_confirmation'] == '') {
                //Return old password
                $_POST['password'] = $oldUserInfo->password;
                $_POST['password_confirmation'] = $_POST['password'];
            }
            if ($form->isValid($_POST, $currentEditUser)) {
                $currentEditUser->avatar = USER_AVATAR_DEFAULT;
                $currentEditUser->email = $oldUserInfo->email;
                $currentEditUser->password = $this->security->hash($_POST['password']);
                if ($currentEditUser->save()) {
                    $this->flashSession->success('m_system_user_message_update_user_successfully');
                    return $this->response->redirect('/admin/system/user/');
                } else {
                    $this->flashSession->error('m_system_user_message_update_user_failed');
                    $this->setFlashSession($currentEditUser->getMessages(), 'error');
                }
            } else {
                $this->setFlashSession($form->getMessages(), 'error');
            }
        }
        $this->view->setVar('form', $form);
        $_POST['password'] = '';
        $_POST['password_confirmation'] = '';
        return true;
    }

    /**
     * DeActive admin user
     *
     * @param $id
     */
    public function deactivateAction($id)
    {
        $id = intval($id);
        /**
         * @var Users $user
         */
        $user = Users::findFirst($id);
        if ($user) {
            if ($user->role_id == 1) {
                $this->flashSession->error('m_system_user_message_supper_admin_can_not_be_change');
            } else {
                $user->is_active = 0;
                if ($user->save()) {
                    $this->flashSession->success('m_system_user_message_deactivate_user_successfully');
                } else {
                    $this->flashSession->success('m_system_user_message_deactivate_user_failed');
                }
            }
        }
        $this->response->redirect('/admin/system/user/');
    }

    /**
     * Active admin user
     *
     * @param int $id
     */
    public function activeAction($id)
    {
        $id = intval($id);
        /**
         * @var Users $user
         */
        $user = Users::findFirst($id);
        if ($user) {
            if ($user->role_id == 1) {
                $this->flashSession->error('m_system_user_message_supper_admin_can_not_be_change');
            } else {
                $user->is_active = 1;
                if (!$user->active_account_at) {
                    $user->active_account_at = date('Y-m-d H:i:s');
                }
                if ($user->save()) {
                    $this->flashSession->success('m_system_user_message_active_user_successfully');
                } else {
                    $this->flashSession->success('m_system_user_message_active_user_failed');
                }
            }
        }
        $this->response->redirect('/admin/system/user/');
    }

    /**
     * Delete user
     */
    public function deleteAction()
    {
        //return false;
        if ($this->_model && $this->_modelBaseName) {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
            if (is_array($ids)) {
                $query = "DELETE FROM {$this->_modelBaseName} WHERE id IN (" . implode(',', $ids) . ") AND is_supper_admin <> 1";
                $this->db->execute($query);
                if ($this->db->affectedRows() > 0) {
                    $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_delete', ["1" => $this->db->affectedRows()]));
                }
            }
        } else {
            $this->flashSession->error('gb_message_you_are_must_set_model_in_child_controller');
        }
        $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
    }
}