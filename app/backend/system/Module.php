<?php

namespace ZCMS\Backend\System;

use ZCMS\Core\ZModule;

/**
 * Class Module
 *
 * @package ZCMS\Backend\System
 */
class Module extends ZModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'system';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}