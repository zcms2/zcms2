<?php

namespace ZCMS\Backend\Admin;

use ZCMS\Core\ZModule;

/**
 * Class Module Index
 * Default module when user logged in
 *
 * @package ZCMS\Backend\Admin
 */
class Module extends ZModule
{

    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'admin';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
