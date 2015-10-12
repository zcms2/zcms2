<?php

namespace ZCMS\Backend\Content;

use ZCMS\Core\ZModule;

/**
 * Class Module
 *
 * @package ZCMS\Backend\Content
 */
class Module extends ZModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'content';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
