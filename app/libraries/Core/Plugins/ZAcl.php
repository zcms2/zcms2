<?php

namespace ZCMS\Core\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use ZCMS\Core\Models\Users;
use Phalcon\Mvc\User\Plugin;
use ZCMS\Core\Models\CoreLogs;
use ZCMS\Core\Models\UserRules;

/**
 * Class ZSecurity
 *
 * @package ZCMS\Core\Plugins
 * @property \ZCMS\Core\ZSession session
 * @property \Phalcon\Flash\Session flashSession
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Di di
 * @property \Phalcon\Mvc\Model\Manager modelsManager
 */
class ZAcl extends Plugin
{
    /**
     * @var object $instance an object Security
     */
    public static $instance;

    /**
     * Url redirect not permission
     *
     * @var string
     */
    protected $urlRedirectNotPermission = '/admin/user/profile/';

    /**
     * Rules admin
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Public rules
     *
     * @var array
     */
    protected $publicRules = [
        'user|login|index',
        'user|forgot-password|index',
        'user|forgot-password|new',
        'user|logout|index'
    ];

    /**
     * Link user access
     *
     * @var array
     */
    protected $linkAccess = [];

    /**
     * Auth admin user info login
     *
     * @var array
     */
    protected $auth = null;

    /**
     * Get Security Instance
     *
     * @return ZAcl
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZAcl();
        }
        return self::$instance;
    }

    /**
     * Instance construct
     */
    public function __construct()
    {
        //Get auth of user login
        $this->auth = $this->session->get('auth');
        $this->rules = $this->auth['rules'];
        $this->linkAccess = $this->auth['linkAccess'];
    }

    /**
     * Get current rule
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return \Phalcon\Http\ResponseInterface
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $config = $this->di->get('config');

        $this->auth = $this->session->get('auth');

        //Get current resource
        $module = $dispatcher->getModuleName();
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $rule = $module . '|' . $controller . '|' . $action;

        if ($this->checkPagePublic($rule)) {
            return true;
        } else {
            if ($this->auth) {
                if (!$this->isAllowed('admin|index|index')) {
                    $this->session->remove('auth');
                    unset($_SESSION);
                }
                if ($this->isAllowed($rule)) {
                    if (time() - $this->auth['last_use_admin'] > $config->auth->lifetime) {
                        //$this->session->remove('auth');
                        $this->flashSession->warning(__('gb_session_login_timeout'));
                        $this->response->redirect('/admin/user/login/');
                        return false;
                    } else {
                        $this->auth['last_use_admin'] = time();
                        $this->session->set('auth', $this->auth);
                        return true;
                    }
                } else {
                    if ($config->debug) {
                        $this->flashSession->warning(__('gb_permission_denied_for_action', [
                            1 => $this->getRuleError($rule) . ' => ' . $module . '<strong style=\'color: red;\'> | </strong>' . $controller . '<strong style=\'color: red;\'> | </strong>' . $action
                        ]));
                    } else {
                        $this->flashSession->warning('gb_permission_denied');
                    }
                    if ($this->isAllowed('user|profile|index')) {
                        $this->response->redirect($this->urlRedirectNotPermission);
                    } else {
                        $this->response->redirect('/admin/');
                    }
                    return false;
                }
            } else {
                if ($config->debug) {
                    $this->flashSession->warning(__('gb_permission_denied_for_action', [
                        1 => $this->getRuleError($rule) . ' => ' . $module . '<strong style=\'color: red;\'> | </strong>' . $controller . '<strong style=\'color: red;\'> | </strong>' . $action
                    ]));
                } else {
                    $this->flashSession->warning('gb_permission_denied');
                }
                $this->response->redirect('/admin/user/login/');
                return false;
            }
        }
    }

    /**
     * Get rule error
     *
     * @param $rule
     * @return string
     */
    protected function getRuleError($rule)
    {
        /**
         * @var UserRules $userRule
         */
        $userRule = UserRules::findFirst([
            'conditions' => 'mca = ?0',
            'bind' => [$rule]
        ]);
        if ($userRule) {
            return __($userRule->action_name);
        } else {
            return '';
        }
    }

    /**
     * Save log
     *
     * @param $permission
     */
    public function saveLog($permission)
    {
        if ($this->auth) {
            $log = new CoreLogs();
            $log->log_module = $permission;
            $log->log_content = json_encode($this->auth);
            $log->save();
        }
    }

    /**
     * Check rule allowed on Security
     *
     * @param string $rule Rule sample construct module|controller|action
     * @return bool
     */
    public final function isAllowed($rule)
    {
        $rule = strtolower($rule);
        if ((int)$this->auth['is_super_admin'] === 1) {
            return true;
        }
        return in_array($rule, $this->rules);
    }

    /**
     * Check link allowed on Security
     *
     * @param string $link Rule sample construct module|controller|action
     * @return bool
     */
    public final function isAllowedLink($link)
    {
        $link = strtolower($link);
        if ((int)$this->auth['is_super_admin'] === 1) {
            return true;
        }
        return in_array($link, $this->linkAccess);
    }

    /**
     * Check this page is Admin Login page or Admin Forgot password page
     *
     * @param $rule
     * @return bool
     */
    protected final function checkPagePublic($rule)
    {
        return in_array($rule, $this->publicRules);
    }

    /**
     * Get current auth
     *
     * @return array Session user login
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Check Token Login
     *
     * @param $auth
     * @return bool
     */
    public function checkTokenLogin($auth)
    {
        if (!$auth) {
            return false;
        }
        $user = Users::findFirst([
            'conditions' => 'id = ?0 AND (token = ?1 OR token = ?2)',
            'bind' => [$auth['id'], $auth['token'], '']
        ]);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }
}