<?php

namespace ZCMS\Core;

use Phalcon\Di;
use Phalcon\Events\Event as PEvent;
use Phalcon\Mvc\View as PView;
use ZCMS\Core\Forms\ZFormFilter;
use ZCMS\Core\Utilities\ZToolbarHelper;

/**
 * Class helper loader template backend
 *
 * @package   ZCMS\Core
 * @since     0.0.1
 */
class ZAdminTemplate
{
    /**
     * @var string Module name need overwrite template
     */
    protected $moduleBaseName = "";

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
     * After render view
     *
     * @param PEvent $event
     * @param PView $view
     */
    public function afterRender($event, $view)
    {
        //Do something
    }

    /**
     * Before render
     *
     * @param PEvent $event
     * @param PView $view
     * @return PView
     */
    public function beforeRender($event, $view)
    {
        $view->setVar('_limit', $view->getDI()->get('config')->pagination->limit);
        if (isset($view->_pageLayout) && isset($view->_filter)) {
            $filter = array_column($view->_pageLayout, 'filter');
            if (!empty($filter)) {
                $filterForm = new ZFormFilter($filter, $view->_filter);
                $view->setVar('_filterColumn', $filterForm->getForm());
            }
        }
        $view->setVar('_toolbarHelpers', ZToolbarHelper::getInstance($this->moduleBaseName, $view->getControllerName()));
    }

    /**
     * Before render view
     *
     * @param PEvent $event
     * @param PView $view
     * @return PView
     */
    public function beforeRenderView($event, $view)
    {

    }
}
