<?php

namespace ZCMS\Modules\Template;

use ZCMS\Core\ZModule;

/**
 * Class Module Template
 *
 * @package ZCMS\Modules\Template
 */
class Module extends ZModule
{

    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'template';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
