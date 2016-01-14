<?php

namespace ZCMS\Modules\Index;

use ZCMS\Core\ZModule;

/**
 * Class Module Profile
 *
 * @package ZCMS\Modules\Index
 */
class Module extends ZModule
{

    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'index';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}