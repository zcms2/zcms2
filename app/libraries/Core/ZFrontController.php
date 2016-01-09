<?php

namespace ZCMS\Core;

use Phalcon\Mvc\Controller as PController;
use ZCMS\Core\Models\Users;

/**
 * Class ZFrontController
 *
 * @package ZCMS\Core
 *
 * @property mixed config
 * @property \Phalcon\Di di
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
 * @property \Phalcon\Dispatcher dispatcher
 * @property \ZCMS\Core\Cache\ZCache cache
 */
class ZFrontController extends PController
{
    /**
     * @var Users
     */
    public $_user;

    /**
     * @var string
     */
    public $_defaultTemplate;

    /**
     * Auto check login
     *
     * If value = true controller auto check user login and redirect to login page
     * @var bool
     */
    public $_checkLogin = false;

    /**
     * Overwrite function initialize
     */
    public function initialize()
    {
        //Get user info
        $this->_user = $this->session->get('auth');
        if ($this->_checkLogin == true && $this->isLogin() == false) {
            $this->session->set('_redirect_from',$_SERVER['REQUEST_URI']);
            header('Location: ' . BASE_URI . '/user/login/');
            die;
        }

        //Set user to view
        $this->view->setVar('_user', $this->_user);

        $this->_initBasicViewVariables();

        $this->assets->collection('js_header');
        $this->assets->collection('css_header');

        global $_controller;
        global $_module;
        global $_action;

        $_module = $this->dispatcher->getModuleName();
        if (method_exists($this->dispatcher, 'getControllerName')) {
            $_controller = $this->dispatcher->getControllerName();
        } else {
            die(__FILE__ . 'ZCMS cannot running!');
        }

        $_action = $this->dispatcher->getActionName();

        $routerName = 'Router' . ucfirst($this->dispatcher->getModuleName()) . 'Helper';
        $routerFile = ROOT_PATH . '/app/frontend/' . $this->dispatcher->getModuleName() . '/' . $routerName . '.php';
        if (file_exists($routerFile)) {
            require_once($routerFile);
            /**
             * @var ZRouter $routerName
             */
            $router = $routerName::getInstance();

            /**
             * @var \RouterIndexHelper $router
             */
            $this->view->setVar('_router', $router);
        }

        $this->view->setVar('_basePaginationParam', '');

        if ($this->request->get('debug_mode') == 1) {
            echo '<pre><br />' . __METHOD__;
            var_dump(ucfirst($_module) . '-> ' . ucfirst($_controller) . 'Controller:' . ucfirst($_action) . 'Action');
            echo '</pre>';
            die('');
        }
    }

    /**
     * Init basic view variables
     */
    protected function _initBasicViewVariables()
    {
        //Add base url to view
        $this->view->setVar('_baseUri', BASE_URI);
        $this->view->setVar('_siteName', $this->config->website->siteName);
        $this->view->setVar('_currentUri', $_SERVER['REQUEST_URI']);
        $this->view->setVar('_debug', $this->config->debug);
        $this->view->setVar('_googleAnalytics', $this->config->googleAnalytics);
        //Get template default
        $this->_defaultTemplate = $this->di->get("config")->frontendTemplate->defaultTemplate;

        //Add template default to view
        $this->view->setVar("_defaultTemplate", $this->_defaultTemplate);

        $this->view->setVar('_user', $this->session->get('auth'));
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
     * @return Users
     */
    protected function _getAuth()
    {
        return $this->_user;
    }

    /**
     * Check user login
     *
     * @return bool
     */
    protected function isLogin()
    {
        if ($this->_user == null) {
            return false;
        }
        return true;
    }
}