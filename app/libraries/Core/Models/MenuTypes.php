<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class MenuType
 *
 * @package ZCMS\Core\Models
 */
class MenuTypes extends ZModel
{
    /**
     *
     * @var integer
     */
    public $menu_type_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
} 