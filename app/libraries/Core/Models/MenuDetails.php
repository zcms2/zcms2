<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class MenuDetail
 *
 * @package ZCMS\Core\Models
 */
class MenuDetails extends ZModel
{
    /**
     *
     * @var integer
     */
    public $menu_detail_id;

    /**
     *
     * @var integer
     */
    public $menu_type_id;

    /**
     *
     * @var integer
     */
    public $menu_item_id;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $ordering;

    /**
     *
     * @var string
     */
    public $published;

    /**
     * @var array
     */
    public $children;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    /**
     * Get children
     *
     * @param $menuDetails
     * @return array
     */
    public function getChildren($menuDetails)
    {
        $children = [];
        foreach ($menuDetails as $it) {
            if ($it->parent_id == $this->menu_item_id) {
                $children[] = $it;
            }
        }
        $arr = [];
        /**
         * @var MenuDetails $value
         */
        foreach ($children as $value) {
            if ($value->parent_id > 0) {
                $value->children = $value->getChildren($menuDetails);
            }
            $arr[] = $value;
        }
        return $arr;
    }
} 