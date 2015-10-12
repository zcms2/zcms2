<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class CoreMedia
 *
 * @package ZCMS\Core\Models
 */
class CoreMedia extends ZModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $alt_text;

    /**
     *
     * @var string
     */
    public $caption;

    /**
     *
     * @var integer
     */
    public $description;

    /**
     *
     * @var string
     */
    public $mime_type;

    /**
     *
     * @var integer
     */
    public $size;

    /**
     *
     * @var string
     */
    public $alias;

    /**
     *
     * @var string
     */
    public $information;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}
