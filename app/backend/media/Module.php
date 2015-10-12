<?php

namespace ZCMS\Backend\Media;

use ZCMS\Core\ZModule;

/**
 * Class Module
 *
 * @package ZCMS\Backend\Media
 */
class Module extends ZModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'media';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
