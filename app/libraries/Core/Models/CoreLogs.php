<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class CoreLogs
 *
 * @package ZCMS\Core\Models
 */
class CoreLogs extends ZModel
{
    /**
     * @var int
     */
    public $log_id;

    /**
     * @var string
     */
    public $log_module;

    /**
     * @var string
     */
    public $log_content;

    /**
     * @var string error|success|notice
     */
    public $status;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}