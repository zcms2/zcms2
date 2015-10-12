<?php

namespace ZCMS\Core;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\Utilities\ZToolbarHelper;
use Phalcon\Session\Bag as SessionBag;

/**
 * Base admin controller. Do not use this controller in frontend module
 *
 * @package   ZCMS\Core
 * @author    ZCMS Team
 * @copyright ZCMS Team
 * @since     0.2.0
 *
 * @property mixed config
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql db
 * @property \ZCMS\Core\Assets\ZAssets assets
 * @property \ZCMS\Core\ZSession session
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Flash\Session flashSession
 * @property \ZCMS\Core\Plugins\ZAcl acl
 * @property \Phalcon\Mvc\Model\Manager modelsManager
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Security security
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\Di di
 * @property \ZCMS\Core\Cache\ZCache cache
 */
class ZAdminController extends Controller
{
    /**
     * User session info
     *
     * @var mixed
     */
    protected $_user;

    /**
     * Current Module
     *
     * @var string $_module
     */
    public $_module;

    /**
     * Current controller
     *
     * @var string
     */
    public $_controller;

    /**
     * Current action
     *
     * @var string
     */
    public $_action = '';

    /**
     * Filter options in standard table
     *
     * @var array
     */
    public $_filterOptions = [];

    /**
     * Filter options values
     *
     * @var array
     */
    public $_filter = [];

    /**
     * Toolbar helper
     *
     * @var ZToolbarHelper
     */
    protected $_toolbar;

    /**
     * PHQL Model
     * Example: \ZCMS\Models\Users
     *
     * @var string PHQL Model
     */
    public $_model;

    /**
     * Database table name
     * Example: users, core_languages
     *
     * @var string Model name in database
     */
    public $_modelBaseName;

    /**
     * Primary key for this model
     *
     * @var string Model primary key
     */
    public $_modelPrimaryKey = '';

    /**
     * Default backend template
     *
     * @var string
     */
    public $_defaultTemplate;

    /**
     * Use system message for publishAction, unPublishAction, moveUp, moveDown...
     *
     * @var bool
     */
    public $_useSystemMessage = false;

    /**
     * Auto translate toolbar with Controller and Action Name
     *
     * @var bool
     */
    public $_autoTranslateToolbar = true;

    /**
     * @overwrite function initialize
     */
    public function initialize()
    {
        //Get module name
        if (method_exists($this->dispatcher, 'getModuleName')) {
            $this->_module = $this->dispatcher->getModuleName();
        }

        //Get controller name
        $this->_controller = $this->dispatcher->getControllerName();

        //Get action name
        $this->_action = $this->dispatcher->getActionName();

        //Get template default
        $this->_defaultTemplate = $this->config->backendTemplate->defaultTemplate;

        //Get logged user
        $this->_user = $this->session->get('auth');

        //Add toolbar helper
        $this->_toolbar = ZToolbarHelper::getInstance($this->_module, $this->_controller);

        if ($this->_autoTranslateToolbar) {
            if ($this->_action === 'index') {
                $this->_toolbar->addHeaderPrimary('m_admin_' . $this->_module . '_' . $this->_controller);
                $this->_toolbar->addHeaderSecond('m_admin_' . $this->_module);
            } else {
                $this->_toolbar->addHeaderPrimary('m_admin_' . $this->_module . '_' . $this->_controller . '_' . $this->_action);
                $this->_toolbar->addHeaderSecond('m_admin_' . $this->_module . '_' . $this->_controller);
            }
            $this->_toolbar->addBreadcrumb('m_admin_' . $this->_module);
            $this->_toolbar->addBreadcrumb('m_admin_' . $this->_module . '_' . $this->_controller);
        }

        //Add basic variables
        $this->_initBasicViewVariables();

        $this->assets->collection('js_footer');
        $this->assets->collection('css_header');
        $this->_debugModel();
    }

    /**
     * Support debug mode for dev
     */
    protected function _debugModel()
    {
        if ($this->config->debug) {
            if ($this->request->get('debug_mode') == 1) {
                if ($this->config->debugType === 'var_dump') {
                    echo '<pre><br />' . __METHOD__;
                    var_dump(ucfirst($this->_module) . '-> ' . ucfirst($this->_controller) . 'Controller:' . ucfirst($this->_action) . 'Action');
                    echo '</pre>';
                    die();
                } else {
                    echo '<pre><br />' . __METHOD__;
                    print_r(ucfirst($this->_module) . '-> ' . ucfirst($this->_controller) . 'Controller:' . ucfirst($this->_action) . 'Action');
                    echo '</pre>';
                    die();
                }
            }

            if ($this->request->get('debug_mode_role') == 1) {
                if ($this->config->debugType === 'var_dump') {
                    echo '<pre><br />' . __METHOD__;
                    var_dump($this->acl->getRules());
                    echo '</pre>';
                    die();
                } else {
                    echo '<pre><br />' . __METHOD__;
                    print_r($this->acl->getRules());
                    echo '</pre>';
                    die();
                }
            }
        }
    }

    /**
     * Init basic view variables
     */
    protected function _initBasicViewVariables()
    {
        //Set template default to view
        $this->view->setVar('_defaultTemplate', $this->_defaultTemplate);

        //Set module to view
        $this->view->setVar('_module', $this->_module);

        //Set base url to view
        $this->view->setVar('_baseUri', BASE_URI);

        //Set site name
        $this->view->setVar('_siteName', $this->config->website->siteName);

        //Set system name
        $this->view->setVar('_systemName', $this->config->website->systemName);

        //Set administrator name
        $this->view->setVar('_user', $this->session->get('auth'));

        $this->view->setVar('_pagination', "../../../templates/backend/{$this->_defaultTemplate}/pagination");
        $this->view->setVar('_flashSession', "../../../templates/backend/{$this->_defaultTemplate}/flashSession");
        $this->view->setVar('_standardTable', "../../../templates/backend/{$this->_defaultTemplate}/standardTable");
        $this->view->setVar('_toolbarHelper', "../../../templates/backend/{$this->_defaultTemplate}/toolbarHelper");
        $this->view->setVar('_userMenu', "../../../templates/backend/{$this->_defaultTemplate}/userMenu");
        $this->view->setVar('_breadcrumb', "../../../templates/backend/{$this->_defaultTemplate}/breadcrumb");
        $this->view->setVar('_version', $this->config->version);

        $this->view->setVar('_menu', $this->_getMenuAdmin($this->_user['role']));
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
        if ($this->_model && $this->_modelBaseName && $this->_modelPrimaryKey) {
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
                $query = "UPDATE {$this->_modelBaseName} SET published = 1 " . $extraQuery . " WHERE {$this->_modelPrimaryKey} IN (" . implode(',', $ids) . ")";
                $this->db->execute($query);
                $this->flashSession->success(__($this->_getPrefixMessage() . 'message_items_successfully_published', ["1" => $this->db->affectedRows()]));
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
        if ($this->_model && $this->_modelBaseName && $this->_modelPrimaryKey) {
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
                $query = "UPDATE {$this->_modelBaseName} SET published = 0 " . $extraQuery . " WHERE {$this->_modelPrimaryKey} IN (" . implode(',', $ids) . ")";
                $this->db->execute($query);
                $this->flashSession->success(__($this->_getPrefixMessage() . 'message_items_successfully_unpublished', ["1" => $this->db->affectedRows()]));
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
     * Function add filter
     *
     * @param string $filterName Filter name as 'search'
     * @param string $filterValueDefault Default null
     * @param string $filterType string|array
     * @param string $method POST|GET
     */
    public function addFilter($filterName, $filterValueDefault = null, $filterType = null, $method = 'POST')
    {
        $this->_filterOptions[$filterName]['value'] = $filterValueDefault;
        $method = strtoupper($method);
        if ($method != 'POST') $method = 'GET';
        $this->_filterOptions[$filterName]['method'] = $method;
        $this->_filterOptions[$filterName]['type'] = $filterType;
    }

    /**
     * Get all value filter
     *
     * @return array Array value filter when coder use function addFilter
     */
    public function getFilter()
    {
        $sessionBagKey = $this->_module . '_' . $this->_controller . '_' . $this->_action . '_filter';
        $filterSessionGlobal = new SessionBag('ZCMS_GB_FILTER');
        $filterSession = [];
        if ($filterSessionGlobal->has($sessionBagKey)) {
            $filterSession = $filterSessionGlobal->get($sessionBagKey);
        }

        if (count($this->_filterOptions)) {
            foreach ($this->_filterOptions as $key => $item) {
                if ($item['method'] == 'POST') {
                    $this->_filter[$key] = $this->request->getPost($key, $item['type']);
                    if ($this->_filter[$key] == null && $this->_filter[$key] !== $item['value']) {
                        if (array_key_exists($key, $filterSession) && $filterSession[$key] != null) {
                            $this->_filter[$key] = $filterSession[$key];
                        } else {
                            $this->_filter[$key] = $item['value'];
                        }
                    }
                } else {
                    $this->_filter[$key] = $this->request->getQuery($key, $item['type']);
                    if ($this->_filter[$key] == null && $this->_filter[$key] !== $item['value']) {
                        if (array_key_exists($key, $filterSession) && $filterSession[$key] != null) {
                            $this->_filter[$key] = $filterSession[$key];
                        } else {
                            $this->_filter[$key] = $item['value'];
                        }
                    }
                }
            }
        }
        $filterSessionGlobal->set($sessionBagKey, $this->_filter);
        return $this->_filter;
    }

    /**
     * Move up
     *
     * @param $id
     */
    public function moveUpAction($id)
    {
        $id = (int)$id;
        /**
         * @var ZModel $model
         */
        $model = new $this->_model;

        /**
         * @var ZModel $item
         */
        $item = $model->findFirst($id);
        if ($item) {
            $item->moveUp();
            $this->flashSession->success($this->_getPrefixMessage() . 'message_items_successfully_move_up');
        } else {
            $this->flashSession->error($this->_getPrefixMessage() . 'message_items_move_up_error');
        }
        $this->response->redirect('/admin/system/module/');
        return;
    }

    /**
     * Move down
     *
     * @param $id
     */
    public function moveDownAction($id)
    {
        $id = (int)$id;
        /**
         * @var ZModel $model
         */
        $model = new $this->_model;

        /**
         * @var ZModel $item
         */
        $item = $model->findFirst($id);
        if ($item) {
            $item->moveDown();
            $this->flashSession->success($this->_getPrefixMessage() . 'message_items_successfully_move_down');
        } else {
            $this->flashSession->error($this->_getPrefixMessage() . 'message_items_move_down_error');
        }
        $this->response->redirect('/admin/system/module/');
        return;
    }

    /**
     * Get prefix message
     *
     * @return string
     */
    public function _getPrefixMessage()
    {
        if ($this->_useSystemMessage) {
            return 'm_' . $this->_module . '_' . $this->_controller . '_';
        }
        return 'gb_';
    }

    /**
     * Set flash session message
     *
     * @param \Phalcon\Mvc\Model\MessageInterface[]|\Phalcon\Validation\Message\Group $messages
     * @param string $type Eg: error, message, success, warning
     */
    public function setFlashSession($messages, $type)
    {
        foreach ($messages as $message) {
            $this->flashSession->{$type}($message->getMessage());
        }
    }

    /**
     * Get menu with admin user role
     *
     * @param integer $role
     * @return array|mixed
     */
    private function _getMenuAdmin($role)
    {
        $currentLink = '/admin/' . $this->_module . '/' . $this->_controller;
        $role = UserRoles::findFirst([
            'conditions' => 'role_id = ?0',
            'bind' => [$role]
        ]);

        if ($role) {
            /**
             * @var mixed $role
             */
            $menu = unserialize($role->menu);
            foreach ($menu as $lv1 => $item) {
                if (isset($item['items']) && count($item['items'])) {
                    foreach ($item['items'] as $lv2 => $childItem) {
                        if (isset($childItem['items']) && count($childItem['items'])) {
                            foreach ($childItem['items'] as $lv3 => $cChildItem) {
                                if (strpos($cChildItem['link'], $currentLink) !== false) {
                                    $menu[$lv1]['items'][$lv2]['current'] = 1;
                                }
                            }

                        }
                    }
                }
            }
            return $menu;
        }
        return [];
    }
}