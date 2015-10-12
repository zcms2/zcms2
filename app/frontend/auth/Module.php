<?php

namespace ZCMS\Frontend\Auth;

use ZCMS\Core\ZFrontModule;

/**
 * Class Module
 *
 * @package ZCMS\Frontend\Auth
 */
class Module extends ZFrontModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'auth';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
