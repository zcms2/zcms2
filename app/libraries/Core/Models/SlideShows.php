<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class SlideShows
 *
 * @package ZCMS\Core\Models
 */
class SlideShows extends ZModel
{
    /**
     *
     * @var integer
     */
    public $slide_show_id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $alias;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var integer
     */
    public $published;

    public function beforeSave()
    {
        if ($this->alias == '') {
            $this->alias = generateAlias($this->title);
        }
    }

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}