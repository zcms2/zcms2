<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class UserRoleMapping
 *
 * @package ZCMS\Core\Models
 */
class UserRoleMapping extends ZModel
{

    /**
     *
     * @var integer
     */
    public $role_mapping_id;

    /**
     *
     * @var integer
     */
    public $role_id;

    /**
     *
     * @var integer
     */
    public $rule_id;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}
