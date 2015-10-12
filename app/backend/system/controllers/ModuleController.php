<?php

namespace ZCMS\Backend\System\Controllers;

use ZCMS\Core\Models\CoreModules;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZTranslate;

/**
 * Class ModuleController
 * @package ZCMS\Backend\System\Controllers
 */
class ModuleController extends ZAdminController
{

    /**
     * Phalcon PHQL Model
     *
     * @var string
     */
    public $_model = 'ZCMS\Core\Models\CoreModules';

    /**
     * Model base name
     *
     * @var string
     */
    public $_modelBaseName = 'core_modules';

    /**
     * View Index
     */
    public function indexAction()
    {
        //Add translation frontend
        ZTranslate::getInstance()->addModuleLang(get_child_folder(APP_DIR . '/frontend/'), 'frontend');
        $this->updateAction(false);

        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addUpdateButton();
        $this->_toolbar->addDeleteButton();
        $this->_toolbar->addCustomButton('deleteCache', 'gb_delete_cache', null, 'fa fa-eraser', 'btn btn-danger delete');

        //Add filter
        $this->addFilter('filter_order', 'ordering', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_location', '', 'string');

        //Get all filter
        $filter = $this->getFilter();

        $conditions = [];

        if ($filter['filter_location']) {
            $conditions[] = "location = '" . $filter['filter_location'] . "'";
        }

        //Get all template
        $items = CoreModules::find([
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
                'column' => 'module_id',
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'text',
                'title' => 'gb_module_name',
                'column' => 'name',
                'translation' => true,
                'sort' => false
            ],
            [
                'type' => 'text',
                'title' => 'gb_version',
                'class' => 'text-center col-version',
                'column' => 'version',
            ],
            [
                'type' => 'text',
                'title' => 'gb_author',
                'class' => 'text-center col-author',
                'column' => 'author',
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
                'type' => 'published_is_core',
                'title' => 'gb_published',
                'column' => 'published',
                'link' => '/admin/system/module/',
                'access' => $this->acl->isAllowed('module|index|index')
            ],
            [
                'type' => 'actions',
                'title' => 'gb_ordering',
                'link_prefix' => 'module_id',
                'class' => 'text-center',
                'column' => 'ordering',
                'display_value' => true,
                'actions' => [
                    [
                        'link_title' => 'gb_move_up',
                        'link' => '/admin/system/module/moveUp/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-up',
                        'access' => $this->acl->isAllowed('update'),
                    ],
                    [
                        'link_title' => 'gb_move_down',
                        'link' => '/admin/system/module/moveDown/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-down',
                        'access' => $this->acl->isAllowed('update'),
                    ]
                ]
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'module_id',
            ]
        ]);
    }

    /**
     * Update All Module
     *
     * @param bool $redirect
     */
    public function updateAction($redirect = true)
    {
        //Get All Module
        $allModulesBackEnd = get_child_folder(APP_DIR . '/backend/');
        $allModulesFrontEnd = get_child_folder(APP_DIR . '/frontend/');
        $allModules = array_merge($allModulesBackEnd, $allModulesFrontEnd);
        $allModulesUnique = array_unique($allModules);
        //Module in frontend and backend is duplicate!
        if (count($allModulesUnique) != count($allModules)) {
            if ($redirect) {
                $this->flashSession->notice('m_system_message_module_in_frontend_and_backend_is_duplicate');
            }
            $this->response->redirect('/admin/system/user/');
            die(__('m_system_message_module_in_frontend_and_backend_is_duplicate'));
        } else {
            $this->deleteModuleNotExists($allModulesUnique);
            $allModulesBackEndReturn = $this->updateModuleInfo($allModulesBackEnd, 'backend');
            $allModulesFrontEndReturn = $this->updateModuleInfo($allModulesFrontEnd, 'frontend');
            $return = array_merge_recursive($allModulesBackEndReturn, $allModulesFrontEndReturn);
            if ($redirect) {
                if (isset($return['success'])) {

                    $this->flashSession->success(__('m_system_module_message_items_module_update_information_successfully', ['1' => count($return['success'])]));

                }
                if (isset($return['error'])) {
                    $message = [];
                    foreach ($return['error'] as $error) {
                        $message[] = '<strong>' . $error[0] . '</strong> [' . $error[1] . ']';
                    }
                    $this->flashSession->error(__("m_system_module_message_resource_for_module_error_json", ['1' => implode(',', $message)]));
                }
            }
        }
        UserRoles::updateModuleMenu();
        if ($redirect) {
            $this->response->redirect('/admin/system/module/');
        }
    }

    /**
     * Update module information
     *
     * @param array $arrayModule
     * @param $moduleLocale
     * @return array
     */
    protected function updateModuleInfo($arrayModule = [], $moduleLocale)
    {
        if (count($arrayModule) == 0 || $moduleLocale == '' || ($moduleLocale != 'backend' && $moduleLocale != 'frontend')) {
            return false;
        } else {
            $arrayModuleReturn = [];
            foreach ($arrayModule as $module) {
                $filePath = APP_DIR . '/' . $moduleLocale . '/' . $module . '/Resource.php';
                //Get new Module name
                $resource = check_resource($filePath, $module, $moduleLocale);

                if (file_exists($filePath) && $resource) {
                    /**
                     * CoreModule $coreModule
                     */
                    $coreModule = CoreModules::findFirst([
                        'conditions' => 'base_name = ?1',
                        'bind' => ['1' => $module]
                    ]);

                    if (!$coreModule) {
                        $coreModule = new CoreModules();
                        $coreModule->base_name = $module;
                        $coreModule->name = $module;
                        $coreModule->is_core = 0;
                        $coreModule->published = 0;
                        $coreModule->location = $moduleLocale;
                        $coreModule->ordering = (int)CoreModules::maximum(['column' => 'ordering']) + 1;
                    }

                    $coreModule->name = $resource['name'];

                    $coreModule->class_name = $resource['class_name'];

                    $coreModule->path = $resource['path'];

                    $coreModule->menu = '';

                    $coreModule->description = $resource['description'];
                    $coreModule->author = $resource['author'];
                    $coreModule->authorUri = $resource['authorUri'];
                    $coreModule->version = $resource['version'];
                    $coreModule->uri = $resource['uri'];

                    if ($moduleLocale == 'backend') {
                        $coreModule->menu = $this->getModuleMenu($module);
                    }

                    if ($moduleLocale == 'frontend') {
                        $coreModule->router = null; //$this->getModuleRouter($module);
                    }
                    if (!$coreModule->save()) {
                        //Do some thing
                    } else {
                        $arrayModuleReturn['success'][] = [$coreModule->name, $moduleLocale];
                    }
                } else {
                    $arrayModuleReturn['error'][] = [$module, $moduleLocale];
                }
            }
            return $arrayModuleReturn;
        }
    }

    /**
     * Get module router
     * @param string $module
     * @return null|string
     */
    protected function getModuleRouter($module)
    {
        $path = APP_DIR . '/frontend/' . $module . '/Router.php';
        $router = check_router($path);
        if (is_array($router)) {
            return serialize($router);
        }
        return null;
    }

    /**
     * Get module menu
     *
     * @param string $module
     * @return null|string
     */
    protected function getModuleMenu($module)
    {
        $path = APP_DIR . '/backend/' . $module . '/Menu.php';
        $menu = check_menu($path);
        if (is_array($menu)) {
            return serialize($menu);
        }
        return null;
    }

    /**
     * Delete module not exists on folder
     *
     * @param array $modules array object module
     */
    protected function deleteModuleNotExists($modules)
    {
        $tmp = [];
        foreach ($modules as $m) {
            $tmp[] = "'" . $m . "'";
        }
        if (count($tmp) > 0) {
            $moduleNotExit = CoreModules::find([
                'conditions' => 'base_name NOT IN (' . implode(',', $tmp) . ') AND is_core = 0'
            ]);
            foreach ($moduleNotExit as $m) {
                /**
                 * @var CoreModules $m
                 */
                $m->delete();
            }

        }
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
        if (class_exists($this->_model)) {
            $extraQuery = null;
            if ($log) {
                $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
            }
            $ids = [];
            if ($id) {
                $id = intval($id);
                $ids[] = $id;
            } else {
                $ids = $this->request->getPost('ids');
                ZArrayHelper::toInteger($ids);
            }
            if (is_array($ids)) {
                ZArrayHelper::toInteger($ids);
                $query = "UPDATE {$this->_modelBaseName} SET published = 1 " . $extraQuery . " WHERE is_core = 0 AND module_id IN (" . implode(',', $ids) . ")";
                $this->db->execute($query);
                $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_published', ["1" => $this->db->affectedRows()]));
                UserRoles::updateModuleMenu();
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

    public function deleteCacheAction()
    {
        $this->cache->flush();
        $this->flashSession->success('Delete cache successfully');
        $this->response->redirect('/admin/system/module/');
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
        if (class_exists($this->_model) && $this->_modelBaseName) {
            if ($log) {
                $extraQuery = ', updated_by = ' . $this->_user['id'] . ", updated_at = '" . date("Y-m-d H:i:s") . "'";
            } else {
                $extraQuery = null;
            }
            $ids = [];
            if ($id) {
                $id = intval($id);
                $ids[] = $id;
            } else {
                $ids = $this->request->getPost('ids');
                ZArrayHelper::toInteger($ids);
            }
            if (is_array($ids)) {
                ZArrayHelper::toInteger($ids);
                $query = "UPDATE {$this->_modelBaseName} SET published = 0 " . $extraQuery . " WHERE is_core = 0 AND module_id IN (" . implode(',', $ids) . ")";
                $this->db->execute($query);
                $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_unpublished', ["1" => $this->db->affectedRows()]));
                UserRoles::updateModuleMenu();
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
     * Delete module item action
     */
    public function deleteAction()
    {
        if (class_exists($this->_model) && $this->_modelBaseName) {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
            if (is_array($ids)) {
                $idsSrt = implode(',', $ids);
                $query = "DELETE FROM {$this->_modelBaseName} WHERE is_core = 0 AND module_id IN (" . $idsSrt . ")";
                $this->db->execute($query);
                $this->flashSession->success(__('m_' . $this->_module . '_' . $this->_controller . '_message_items_successfully_delete', ['1' => $this->db->affectedRows()]));

                //Check module is core module
                $isCoreModule = CoreModules::find('is_core = 1 AND module_id IN (' . $idsSrt . ')')->toArray();
                if (count($isCoreModule)) {
                    $isCoreModuleName = array_map("__", array_column($isCoreModule, 'name'));
                    $this->flashSession->error(__('m_' . $this->_module . '_' . $this->_controller . '_can_not_delete_module_core', ["1" => implode(', ', $isCoreModuleName)]));
                }
            }

        } else {
            $this->flashSession->error('gb_message_you_are_must_set_model_in_child_controller');
        }
        $this->response->redirect('/admin/' . $this->_module . '/' . $this->_controller . '/');
    }
}