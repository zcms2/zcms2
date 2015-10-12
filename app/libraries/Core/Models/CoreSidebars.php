<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;

/**
 * Class CoreSidebar
 *
 * @package ZCMS\Core\Models
 */
class CoreSidebars extends Model
{

    /**
     *
     * @var string
     */
    public $sidebar_base_name;

    /**
     *
     * @var string
     */
    public $theme_name;

    /**
     *
     * @var string
     */
    public $sidebar_name;

    /**
     *
     * @var integer
     */
    public $ordering;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     *
     * @var string
     */
    public $location;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}
