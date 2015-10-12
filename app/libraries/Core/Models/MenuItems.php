<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class MenuItem
 *
 * @package ZCMS\Core\Models
 */
class MenuItems extends ZModel
{
    /**
     *
     * @var integer
     */
    public $menu_item_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $link;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $thumbnail;

    /**
     *
     * @var string
     */
    public $full_link;

    /**
     *
     * @var integer
     */
    public $parent;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     *
     * @var integer
     */
    public $require_login;

    /**
     *
     * @var string
     */
    public $class;

    /**
     *
     * @var string
     */
    public $icon;

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
        parent::beforeCreate();
        if (strpos($this->link, ['https://', 'http://']) === false) {
            $this->full_link = BASE_URI . $this->link;
        } else {
            $this->full_link = $this->link;
        }
    }

    /**
     * Execute before update
     */
    public function beforeUpdate()
    {
        parent::beforeUpdate();
        if (strpos($this->link, ['https://', 'http://']) === false) {
            $this->full_link = BASE_URI . $this->link;
        } else {
            $this->full_link = $this->link;
        }
    }


} 