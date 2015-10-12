<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class CoreModules
 *
 * @package ZCMS\Core\Models
 */
class CoreModules extends ZModel
{
    /**
     *
     * @var integer
     */
    public $module_id;

    /**
     *
     * @var string
     */
    public $base_name;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $location;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $class_name;

    /**
     *
     * @var string
     */
    public $path;

    /**
     *
     * @var string
     */
    public $menu;

    /**
     *
     * @var string
     */
    public $router;

    /**
     *
     * @var string
     */
    public $version;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $authorUri;

    /**
     *
     * @var string
     */
    public $uri;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     *
     * @var integer
     */
    public $is_core;

    /**
     *
     * @var integer
     */
    public $ordering;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var integer
     */
    public $created_by;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $updated_by;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {

    }

}
