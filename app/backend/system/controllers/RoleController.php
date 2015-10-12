<?php

namespace ZCMS\Backend\System\Controllers;

use Phalcon\Db;
use ZCMS\Core\Models\CoreModules;

use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Plugins\ZAcl;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Models\UserRules;
use ZCMS\Core\Models\UserRoleMapping;
use ZCMS\Core\Utilities\ZArrayHelper;

/**
 * Class Role Controller
 *
 * @package ZCMS\Backend\System\Controllers
 */
class RoleController extends ZAdminController
{
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addNewButton();
        $this->_toolbar->addEditButton();
        $this->_toolbar->addDeleteButton();

        $this->updateRules();
        UserRoles::updateModuleMenu();
        $this->updateACLCache();

        //Add filter
        $this->addFilter("filter_order", "role_id", "string");
        $this->addFilter("filter_order_dir", "ASC", "string");

        $conditions = [];

        $conditions[] = 'ar.is_super_admin != 1';

        //Get all filter
        $filter = $this->getFilter();
        $items = $this->modelsManager->createBuilder()
            ->columns('ar.role_id AS id, ar.name AS name, ar.is_default, ar.location, ar.updated_at AS updated_at, ar.updated_by AS updated_by, ar.created_at AS created_at')
            ->addFrom('ZCMS\Core\Models\UserRoles', "ar")
            ->where(implode(' AND ', $conditions))
            ->orderBy($filter['filter_order'] . ' ' . $filter['filter_order_dir']);
        $currentPage = $this->request->getQuery("page", "int", 1);

        $paginationLimit = $this->config->pagination->limit;

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationQueryBuilder($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);

        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'link',
                'title' => 'm_system_role_form_name',
                'column' => 'name',
                'link' => '/admin/system/role/edit/',
                'access' => $this->acl->isAllowed('system|role|edit')
            ],
            [
                'type' => 'text',
                'title' => 'gb_is_default',
                'column' => 'is_default',
                'class' => 'text-center col-location',
                'label' => [
                    [
                        'condition' => '==',
                        'condition_value' => '1',
                        'class' => 'glyphicon glyphicon-star orange',
                        'text' => ''
                    ],
                    [
                        'condition' => '==',
                        'condition_value' => '0',
                        'class' => 'glyphicon glyphicon-star grey',
                        'text' => ''
                    ]
                ]
            ],
            [
                'type' => 'text',
                'title' => 'gb_location',
                'class' => 'text-center col-location',
                'column' => 'location',
                'label' => [
                    [
                        'condition' => '==',
                        'condition_value' => 'backend',
                        'class' => 'label label-sm label-success',
                        'text' => 'gb_backend'
                    ],
                    [
                        'condition' => '!=',
                        'condition_value' => 'backend',
                        'class' => 'label label-sm label-warning',
                        'text' => 'gb_frontend'
                    ]
                ],
                'translation' => true,
            ],
            [
                'type' => 'date',
                'title' => 'gb_created_at',
                'column' => 'created_at',
            ],
            [
                'type' => 'date',
                'title' => 'gb_updated_at',
                'column' => 'updated_at',
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'id',
            ],
        ]);
    }

    /**
     * New Role
     */
    public function newAction()
    {
        //Add toolbar button
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        $this->_addCSSAndJS();

        //Get rules
        $this->getRules();

        if ($this->request->isPost()) {
            //Begin transaction
            $this->db->begin();

            //Save admin role
            $user_role = new UserRoles();
            $user_role->name = $this->request->getPost('name', 'striptags');
            $user_role->is_super_admin = 0;
            $user_role->location = (int)$this->request->getPost('location');
            $user_role->is_default = (int)$this->request->getPost('is_default');

            if ($user_role->save() == false) {
                $this->db->rollback();
                $this->setFlashSession($user_role->getMessages(), 'notice');
                return $this->flashSession->error('m_system_role_message_cannot_save_role');
            }

            //Save admin role mapping
            $userRulesPost = trim($this->request->getPost("admin_rules"), ' ');
            if ($userRulesPost == '') {
                $this->db->commit();
                $this->flashSession->success('m_system_role_message_new_role_was_created_successfully');
                $this->response->redirect('/admin/system/role/');
                return true;
            }
            $user_rules = explode(',', $userRulesPost);
            foreach ($user_rules as $rule) {
                $user_role_mapping = new UserRoleMapping();
                $user_role_mapping->role_id = $user_role->role_id;
                $user_role_mapping->rule_id = $rule;
                if ($user_role_mapping->save() == false) {
                    $this->setFlashSession($user_role_mapping->getMessages(), 'notice');
                    $this->db->rollback();
                    return $this->flashSession->error('m_system_role_message_cannot_save_ruler_in_role');
                }
            }

            //After all success full, commit transaction
            $this->db->commit();
            $this->flashSession->success('m_system_role_message_new_role_was_created_successfully');
            return $this->response->redirect('/admin/system/role/');
        }

        return null;
    }

    /**
     * Delete role
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $ids = $this->request->getPost('ids', 'int', 'null');
            if (is_array($ids)) {
                ZArrayHelper::toInteger($ids);
                foreach ($ids as $id) {
                    /**
                     * @var UserRoles $userRole
                     */
                    $userRole = UserRoles::findFirst('role_id = ' . $id . ' AND is_super_admin != 1');
                    if ($userRole) {

                        //Check Admin role is being used
                        $user = Users::findFirst([
                            'conditions' => 'role_id = :id:',
                            'bind' => ['id' => $id]
                        ]);

                        if (!$user) {
                            //Begin transaction
                            $this->db->begin();

                            $userRoleMapping = UserRoleMapping::find('role_id = ' . $id);

                            if (method_exists($userRoleMapping, 'delete') && $userRoleMapping->delete() == false) {
                                $this->db->rollback();
                                $this->flashSession->error('m_system_role_message_cannot_delete_role_mapping');
                                return $this->response->redirect('/admin/system/role/');
                            }

                            if ($userRole->delete() == false) {
                                $this->db->rollback();
                                $this->flashSession->error('m_system_role_message_cannot_delete_role');
                                return $this->response->redirect('/admin/system/role/');
                            }

                            //After all successfully, commit transaction
                            $this->db->commit();
                            $this->flashSession->success(__('m_system_role_message_delete_role_successfully', ["1" => $userRole->name]));
                        } else {
                            $this->flashSession->error('m_system_role_message_role_is_being_used_on_some_user');
                        }
                    } else {
                        $this->flashSession->error('m_system_role_message_super_administrator_cannot_delete');
                    }
                }
            }
        }
        return $this->response->redirect('/admin/system/role/');
    }

    private function _addCSSAndJS(){
        //Add css
        $this->assets->collection('css_header')->addCss('/plugins/dynatree/dist/skin-vista/ui.dynatree.css');
        //Add js
        $this->assets->collection('js_footer')->addJs('/plugins/dynatree/dist/jquery.dynatree.min.js');
    }

    /**
     * Edit role
     *
     * @param int $id
     * @return bool
     */
    public function editAction($id)
    {
        $id = intval($id);

        /**
         * @var UserRoles $edit_data
         */
        $edit_data = UserRoles::findFirst([
            'conditions' => 'role_id = ?0',
            'bind' => [$id]
        ]);
        //If id not exist
        if (!$edit_data) {
            $this->flashSession->error("Cant not find that item to edit!");
            return $this->response->redirect('/admin/system/role/');
        } elseif ($edit_data->is_super_admin == 1) {
            $this->flashSession->error("You can't not edit Super Admin!");
            return $this->response->redirect('/admin/system/role/');
        } else {
            $this->view->setVar('edit_data', $edit_data);
        }

        //Add toolbar button
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton("index");

        $this->_addCSSAndJS();

        //Get rules
        $this->getRules();

        //Get edit rules
        /**
         * @var UserRoleMapping[] $edit_user_role_mapping
         */
        $edit_user_role_mapping = UserRoleMapping::find([
            "conditions" => "role_id = ?0",
            "bind" => [0 => $edit_data->role_id]
        ]);

        $edit_rules = [];
        foreach ($edit_user_role_mapping as $arm) {
            $edit_rules[] = $arm->rule_id;
        }

        $this->view->setVar('edit_rules_id', implode(",", $edit_rules));

        if ($this->request->isPost()) {
            //Begin transaction
            $this->db->begin();

            //Get current auth
            $auth = ZAcl::getInstance()->getAuth();
            //Save admin role
            $edit_data->name = $this->request->getPost("name", "striptags");
            $edit_data->updated_at = date("Y-m-d H:i:s");
            $edit_data->updated_by = $auth['id'];
            $edit_data->location = (int)$this->request->getPost('location');
            $edit_data->is_default = (int)$this->request->getPost('is_default');

            if ($edit_data->save() == false) {
                $this->db->rollback();
                return $this->flashSession->error("m_system_role_message_cannot_save_role");
            }

            //Save admin role mapping
            $userRulesPost = trim($this->request->getPost("admin_rules"), ' ');
            if ($userRulesPost == '') {
                $this->db->commit();
                $this->flashSession->success('m_system_role_message_new_role_was_created_successfully');
                $this->response->redirect('/admin/system/role/');
                return true;
            }
            $user_rules = explode(",", $userRulesPost);
            $number_new_rules = count($user_rules);
            $number_old_rules = count($edit_user_role_mapping);
            $sub = $number_new_rules - $number_old_rules;

            if ($sub < 0) {
                foreach ($edit_user_role_mapping as $key => $arm) {
                    if ($user_rules[$key]) {
                        $arm->rule_id = $user_rules[$key];
                        if ($arm->save() == false) {
                            $this->db->rollback();
                            return $this->flashSession->error('m_system_role_message_update_role_failed');
                        }
                    } else {
                        if ($arm->delete() == false) {
                            $this->db->rollback();
                            return $this->flashSession->error('m_system_role_message_update_role_failed');
                        }
                    }
                }
            } elseif ($sub == 0) {
                //echo '<pre>'; var_dump($edit_user_role_mapping->toArray());echo '</pre>'; die();
                foreach ($edit_user_role_mapping as $key => $arm) {
                    $arm->rule_id = $user_rules[$key];
                    if ($arm->save() == false) {
                        $this->db->rollback();
                        return $this->flashSession->error('m_system_role_message_update_role_failed');
                    }
                }
            } else {
                foreach ($edit_user_role_mapping as $key => $arm) {
                    $arm->rule_id = $user_rules[$key];
                    if ($arm->save() == false) {
                        $this->db->rollback();
                        return $this->flashSession->error('m_system_role_message_update_role_failed');
                    }
                }
                for ($i = $number_old_rules; $i < $number_new_rules; $i++) {
                    $new_user_role_mapping = new UserRoleMapping();
                    $new_user_role_mapping->role_id = $edit_data->role_id;
                    $new_user_role_mapping->rule_id = $user_rules[$i];
                    if ($new_user_role_mapping->save() == false) {
                        $this->db->rollback();
                        return $this->flashSession->error('m_system_role_message_update_role_failed');
                    }
                }
            }

            //After all success full, commit transaction
            $this->db->commit();
            $this->flashSession->success(__('m_system_role_message_new_role_was_updated_successfully', ['1' => $edit_data->name]));
            return $this->response->redirect('/admin/system/role/');
        }
        return true;
    }

    private function updateRules()
    {
        //Get active module
        $core_module = CoreModules::find("published = 1 AND location = 'backend'");

        //Read resources
        foreach ($core_module as $cModule) {
            $filePath = APP_DIR . "/backend/" . $cModule->base_name . "/Resource.php";
            $module = str_replace(' ', "", $cModule->base_name);

            //Save rules from resource.php to database
            $resource = check_resource($filePath, $cModule->base_name, 'backend');

            if ($resource) {
                //Save rules
                $rules = $resource["rules"];
                foreach ($rules as $rule) {
                    $this->saveRule($resource, $module, $rule);
                }

                //Delete old rules
                $this->deleteOldRules($module, $rules);
            } else {
                $this->flashSession->error(__('m_system_role_message_resource_for_module_is_error', ["1" => $module]));
            }
        }

        //Get all module backend
        $allModuleBackEnd = get_child_folder(APP_DIR . '/backend/');
        foreach ($allModuleBackEnd as &$aMBE) {
            $aMBE = '"' . $aMBE . '"';
        }

        //Get old module to delete rules
        $allModuleNotExist = UserRules::find("module NOT IN(" . implode(",", $allModuleBackEnd) . ")")->toArray();

        /**
         * @var CoreModules[] $unpublished_module
         */
        $unpublished_module = CoreModules::find("published = 0 AND location='backend'");
        $arrayModuleName = [];
        if (count($unpublished_module) || count($allModuleNotExist)) {

            foreach ($unpublished_module as $u) {
                $arrayModuleName[] = "'" . $u->base_name . "'";
            }

            foreach ($allModuleNotExist as $aMNE) {
                $arrayModuleName[] = "'" . $aMNE['module'] . "'";
            }

            $arrayModuleName = array_unique($arrayModuleName);

            if (count($arrayModuleName)) {
                /**
                 * @var UserRules[] $userRuleNeedDelete
                 */
                $userRuleNeedDelete = UserRules::find("module in (" . implode(',', $arrayModuleName) . ")");
                if (count($userRuleNeedDelete)) {
                    $ids = [];
                    foreach ($userRuleNeedDelete as $aRND) {
                        $ids[] = $aRND->rule_id;
                    }
                    $deleteRuleQuery = "DELETE FROM user_role_mapping WHERE rule_id IN (" . implode(',', $ids) . ")";
                    $this->db->execute($deleteRuleQuery);

                    foreach ($userRuleNeedDelete as $aRND) {
                        $aRND->delete();
                    }
                }

            }
        }
    }

    private function updateACLCache()
    {
        /**
         * @var UserRoles[] $roles
         */
        $roles = UserRoles::find();
        foreach ($roles as $role) {
            $query = 'SELECT module, controller, action, sub_action, mca FROM user_rules AS al
                      INNER JOIN user_role_mapping AS alm ON alm.rule_id = al.rule_id
                      WHERE alm.role_id = ' . $role->role_id;
            $rules = $this->db->fetchAll($query, Db::FETCH_ASSOC);
            $rulesTmp = [];
            $linkAccess = [];

            foreach ($rules as $rule) {
                $rulesTmp[] = strtolower($rule['mca']);
                $linkAccess[] = strtolower('/admin/' . $rule['module'] . '/' . $rule['controller'] . '/' . $rule['action']);
                $rule['sub_action'] = trim($rule['sub_action'], ' ');
                if ($rule['sub_action']) {
                    $subAction = explode(',', $rule['sub_action']);

                    foreach ($subAction as $action) {
                        $action = trim($action, ' ');
                        if ($action != '') {
                            //Add Rule
                            $rulesTmp[] = strtolower($rule['module'] . '|' . $rule['controller'] . '|' . $action);
                            $linkAccess[] = strtolower('/admin/' . $rule['module'] . '/' . $rule['controller'] . '/' . $action);
                        }
                    }
                }
            }
            $role->acl = json_encode(['rules' => $rulesTmp, 'links' => $linkAccess]);
            $role->save();
        }

    }

    /**
     * Save rule
     *
     * @param array $resource
     * @param string $module
     * @param string $rule
     * @return null|string
     */
    private function saveRule($resource, $module, $rule)
    {
        //Trim module, controller, action
        $controller = str_replace(' ', '', $rule['controller']);
        $action = str_replace(' ', '', $rule['action']);
        $sub_action = str_replace(' ', '', $rule['sub_action']);
        $mca = $module . "|" . $controller . "|" . $action;

        $userRule = UserRules::findFirst([
            'conditions' => 'module = ?0 AND controller = ?1 AND action = ?2',
            'bind' => ['0' => $module, '1' => $controller, '2' => $action]
        ]);

        if (!$userRule) {
            //Add new rule
            $userRule = new UserRules();
            $userRule->module = $module;
            $userRule->controller = $controller;
            $userRule->action = $action;
            $userRule->mca = $mca;
            $userRule->module_name = $resource['name'];
            $userRule->controller_name = $rule['controller_name'];
            $userRule->action_name = $rule['action_name'];
            $userRule->sub_action = $sub_action;
        }

        //Add new or update
        $userRule->module_name = $resource['name'];
        $userRule->controller_name = $rule['controller_name'];
        $userRule->action_name = $rule['action_name'];
        $userRule->sub_action = $sub_action;

        if ($userRule->save() == false) {
            return $this->flashSession->error('m_system_role_message_update_role_failed');
        }

        return null;
    }

    /**
     * Delete old rules
     *
     * @param string $module
     * @param array $rules
     */
    private function deleteOldRules($module, $rules)
    {
        $array_rules = [];
        foreach ($rules as $rule) {
            $array_rules[] = "'" . $module . "|" . $rule['controller'] . "|" . $rule['action'] . "'";
        }

        $str = implode(',', $array_rules);

        $phql = "SELECT "
            . "rule_id,mca FROM ZCMS\Core\Models\UserRules "
            . "WHERE module = ?0 AND mca not in (" . $str . ")";
        /**
         * @var mixed $old_rules
         */
        $old_rules = $this->modelsManager->executeQuery($phql, [
            0 => $module
        ]);

        $old_rules_array = $old_rules->toArray();

        if (!empty($old_rules_array)) {
            $ids = implode(',', array_column($old_rules_array, 'rule_id'));
            $delete = "DELETE FROM user_role_mapping WHERE rule_id IN (" . $ids . ")";
            $this->db->execute($delete);
        }

        foreach ($old_rules as $or) {
            $userRule = UserRules::findFirst($or->rule_id);
            if ($userRule) {
                $userRule->delete();
            }
        }
    }

    private function getRules()
    {
        $query = 'SELECT module ,module_name
                  FROM user_rules
                  INNER JOIN core_modules
                    ON core_modules.base_name = user_rules.module
                  GROUP BY module, module_name, ordering
                  ORDER BY core_modules.ordering ASC';
        $modules = $this->db->fetchAll($query, Db::FETCH_OBJ);

        $roles = [];

        foreach ($modules as $i => $module) {
            $mod = new \stdClass();
            $mod->title = __($module->module_name);
            $mod->isFolder = true;
            $mod->expand = true;
            $mod->addClass = 'cb_module';
            $mod->children = [];

            $phql1 = 'SELECT '
                . 'controller, controller_name '
                . 'FROM '
                . 'ZCMS\Core\Models\UserRules '
                . 'WHERE module = ?0 '
                . 'GROUP BY controller, controller_name';
            $controllers = $this->modelsManager->executeQuery($phql1, [
                0 => $module->module
            ]);

            foreach ($controllers as $j => $controller) {
                $con = new \stdClass();
                $con->title = __($controller->controller_name);
                $con->isFolder = true;
                $con->expand = true;
                $con->addClass = 'cb_controller';
                $con->children = [];

                $phql2 = 'SELECT '
                    . 'rule_id, action, action_name '
                    . 'FROM '
                    . 'ZCMS\Core\Models\UserRules '
                    . 'WHERE module = ?0 AND controller = ?1 '
                    . 'ORDER BY action';
                $actions = $this->modelsManager->executeQuery($phql2, [
                    0 => $module->module,
                    1 => $controller->controller
                ]);

                foreach ($actions as $k => $action) {
                    $act = new \stdClass();
                    $act->title = __($action->action_name);
                    $act->isFolder = true;
                    $act->addClass = "cb_action";
                    $act->key = $action->rule_id;
                    $con->children[$k] = $act;
                }
                $mod->children[$j] = $con;
            }
            $roles[$i] = $mod;
        }
        $this->view->setVar('roles', json_encode($roles));
    }
}
