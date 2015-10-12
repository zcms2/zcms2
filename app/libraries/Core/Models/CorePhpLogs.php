<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;

/**
 * Class BugTracking
 *
 * @package ZCMS\Core\Models
 */
class CorePhpLogs extends Model
{
    /**
     * @var int
     */
    public $log_id;

    /**
     *
     * @var string
     */
    public $log_key;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $file;

    /**
     *
     * @var string
     */
    public $line;

    /**
     *
     * @var int
     */
    public $status;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    /**
     * Execute before create
     */
    public function beforeCreate()
    {
        if (property_exists($this, 'created_at')) {
            $this->created_at = date("Y-m-d H:i:s");
        }

        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date("Y-m-d H:i:s");
        }
    }

    /**
     * Execute before update
     */
    public function beforeUpdate()
    {
        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date("Y-m-d H:i:s");
        }
    }
}