<?php

namespace ZCMS\Modules\User;

use ZCMS\Core\ZModule;

/**
 * Class Module Profile
 *
 * @package ZCMS\Modules\User
 */
class Module extends ZModule
{

    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'user';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}