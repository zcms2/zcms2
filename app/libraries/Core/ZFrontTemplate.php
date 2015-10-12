<?php

namespace ZCMS\Core;

use Phalcon\Events\Event as PEvent;
use Phalcon\Mvc\View as PView;

/**
 * Class helper loader overwrite template frontend
 *
 * @package   ZCMS\Core
 * @author    ZCMS Team
 * @copyright ZCMS Team
 * @since     0.0.1
 */
class ZFrontTemplate
{
    /**
     * @var string Module name need overwrite template;
     */
    protected $moduleBaseName = '';

    /**
     * Instance construct
     *
     * @param string $moduleBaseName
     */
    public function __construct($moduleBaseName)
    {
        $this->moduleBaseName = $moduleBaseName;
    }

    /**
     * After render
     *
     * @param PEvent $event
     * @param PView $view
     */
    public function afterRender($event, $view)
    {
        if ($view->_debug == 0) {
            $view->setContent(remove_multi_space(str_replace(["\r\n", "\r", "\n"], '', $view->getContent())));
        }
    }

    /**
     * Before render view
     *
     * @param PEvent $event
     * @param PView $view
     */
    public function beforeRender(PEvent $event, PView $view)
    {
        $defaultTemplate = $view->getDI()->get('config')->frontendTemplate->defaultTemplate;
        $viewDir = ROOT_PATH . '/app/templates/frontend/' . $defaultTemplate . '/modules/' . $this->moduleBaseName . '/';
        $pathView = $viewDir . $view->getControllerName() . '/' . $view->getActionName();
        $view->setVar('_templateDir', ROOT_PATH . '/app/templates/frontend/' . $defaultTemplate);
        if (realpath($pathView . '.volt')) {
            $view->setVar('_flashSession', '../../flashSession');
            $view->setVar('_header', '../../header');
            $view->setVar('_footer', '../../footer');
            $view->setVar('_jsBlock', '../../jsBlock');
            $view->setVar('_sidebar', '../../sidebar');
            $view->setVar('_userFriends', '../../userFriends');
            $view->setViewsDir($viewDir);
        } else {
            if (isset($view->_defaultTemplate)) {
                $view->setVar('_flashSession', '../../../templates/frontend/' . $view->_defaultTemplate . '/flashSession');
                $view->setVar('_header', '../../../templates/frontend/' . $view->_defaultTemplate . '/header');
                $view->setVar('_footer', '../../../templates/frontend/' . $view->_defaultTemplate . '/footer');
                $view->setVar('_jsBlock', '../../../templates/frontend/' . $view->_defaultTemplate . '/jsBlock');
                $view->setVar('_userFriends', '../../../templates/frontend/' . $view->_defaultTemplate . '/userFriends');
            }
        }
    }
}
