<?php

namespace ZCMS\Backend\Slide;

use ZCMS\Core\ZModule;

/**
 * Class Module Slide Shows
 *
 * @package ZCMS\Backend\Slide
 */
class Module extends ZModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'slide';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
