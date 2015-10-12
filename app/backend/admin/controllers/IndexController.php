<?php

namespace ZCMS\Backend\Admin\Controllers;

use ZCMS\Core\ZAdminController;

/**
 * Class IndexController
 *
 * @author ZCMS Team
 * @package ZCMS\Backend\Admin\Controllers
 */
class IndexController extends ZAdminController
{
    /**
     * @var bool
     */
    public $_autoTranslateToolbar = false;

    /**
     * Default view when user logged in
     */
    public function indexAction()
    {
        //Add information for dashboard
        $this->_toolbar->addHeaderPrimary('m_admin_admin');
        $this->_toolbar->addHeaderSecond($this->config->website->systemName);
        $this->_toolbar->addBreadcrumb('m_admin_admin');
    }
}