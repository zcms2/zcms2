<?php

namespace ZCMS\Modules\System\Controllers\Admin;

use ZCMS\Core\Models\CoreModules;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZTranslate;

/**
 * Class ModuleController
 * @package ZCMS\Modules\System\Controllers
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
        ZTranslate::getInstance('admin')->addModulesLang('frontend');
        $this->updateAction(false);

        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addUpdateButton();
        $this->_toolbar->addDeleteButton();
        $this->_toolbar->addCustomButton('deleteCache', 'gb_delete_cache', null, 'fa fa-eraser', 'btn btn-danger delete');

        //Add filter
        $this->addFilter('filter_order', 'ordering', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');

        //Get all filter
        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);
        $conditions = [];

        //Get all template
        $items = CoreModules::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        $currentPage = $this->request->getQuery('page', 'int', 1);

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $this->config->pagination->limit, $currentPage));

        //Set view layout
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'module_id',
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
        //Remove all cache
        $this->cache->flush();
        //Get All Module
        $modules = get_child_folder(ROOT_PATH . '/app/modules/');

        //Remove module delete with hand in folder
        $this->deleteModuleNotExists($modules);
        $messages = $this->updateModuleInfo($modules);
        if ($redirect) {
            if (isset($messages['success'])) {
                $this->flashSession->success(__('m_system_module_message_items_module_update_information_successfully', ['1' => count($messages['success'])]));
            }
            if (isset($messages['error'])) {
                $message = [];
                foreach ($messages['error'] as $error) {
                    $message[] = '<strong>' . $error[0] . '</strong>';
                }
                $this->flashSession->error(__("m_system_module_message_resource_for_module_error_json", ['1' => implode(',', $message)]));
            }
        }

        //Update role menu
        UserRoles::updateModuleMenu();

        if ($redirect) {
            $this->response->redirect('/admin/system/module/');
        }
    }

    /**
     * Update module information
     *
     * @param array $arrayModule
     * @return array
     */
    protected function updateModuleInfo($arrayModule = [])
    {
        $arrayModuleReturn = [];
        foreach ($arrayModule as $module) {
            $filePath = ROOT_PATH . '/app/modules/' . $module . '/Resource.php';
            //Get new Module name
            $resource = check_resource($filePath, $module);
            if (file_exists($filePath) && $resource) {
                /**
                 * CoreModule $coreModule
                 */
                $coreModule = CoreModules::findFirst([
                    'conditions' => 'base_name = ?0',
                    'bind' => [$module]
                ]);

                if (!$coreModule) {
                    $coreModule = new CoreModules();
                    $coreModule->base_name = $module;
                    $coreModule->name = $module;
                    $coreModule->is_core = 0;
                    $coreModule->published = 0;
                    $coreModule->ordering = (int)CoreModules::maximum(['column' => 'ordering']) + 1;
                }
                $coreModule->namespace = $resource['namespace'];
                $coreModule->name = $resource['name'];
                $coreModule->menu = null;

                $coreModule->description = $resource['description'];
                $coreModule->author = $resource['author'];
                $coreModule->authorUri = $resource['authorUri'];
                $coreModule->version = $resource['version'];
                $coreModule->uri = $resource['uri'];

                $coreModule->menu = $this->getModuleMenu($module);

//                if($module == 'dashboard'){
//                    echo '<pre>'; var_dump($coreModule->menu);echo '</pre>'; die();
//                }

                if (!$coreModule->save()) {
                    //Do some thing
                    echo '<pre>';
                    var_dump($coreModule->getMessages());
                    echo '</pre>';
                    die();
                } else {
                    $arrayModuleReturn['success'][] = [$coreModule->name];
                }
            } else {
                $arrayModuleReturn['error'][] = [$module];
            }
        }
        return $arrayModuleReturn;
    }

    /**
     * Get module router
     * @param string $module
     * @return null|string
     */
    protected function getModuleRouter($module)
    {
        $path = ROOT_PATH . '/app/frontend/' . $module . '/Router.php';
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
        $path = ROOT_PATH . '/app/modules/' . $module . '/Menu.php';
        $menu = check_menu($path);
        if (is_array($menu)) {
            return json_encode($menu);
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