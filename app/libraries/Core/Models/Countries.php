<?php

namespace ZCMS\Core\Models;

use  Phalcon\Mvc\Model;

/**
 * Class Countries
 *
 * @package ZCMS\Core\Models
 */
class Countries extends Model
{

    /**
     *
     * @var integer
     */
    public $country_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $ordering;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

        $this->hasMany('country_id', 'Country_states', 'country_id', ['alias' => 'Country_states']);
    }

}
