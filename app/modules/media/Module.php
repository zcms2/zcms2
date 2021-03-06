<?php

namespace ZCMS\Modules\Media;

use ZCMS\Core\ZModule;

/**
 * Class Module
 *
 * @package ZCMS\Modules\Media
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
