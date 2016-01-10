<?php

namespace ZCMS\Modules\Dashboard;

use ZCMS\Core\ZModule;

/**
 * Class Module Dashboard
 * Default module when user logged in
 *
 * @package ZCMS\Modules\Dashboard\Module
 */
class Module extends ZModule
{

    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'dashboard';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
