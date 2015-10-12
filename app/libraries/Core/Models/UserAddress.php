<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;

/**
 * Class UserAddress
 *
 * @package ZCMS\Core\Models
 */
class UserAddress extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $id_user;

    /**
     *
     * @var string
     */
    public $first_name;


    /**
     *
     * @var string
     */
    public $last_name;


    /**
     *
     * @var integer
     */
    public $city;

    /**
     *
     * @var integer
     */
    public $country;

    /**
     *
     * @var integer
     */
    public $state;

    /**
     *
     * @var string
     */
    public $zip_postal_code;

    /**
     *
     * @var string
     */
    public $telephone;

    /**
     *
     * @var string
     */
    public $cellphone;

    /**
     *
     * @var string
     */
    public $fax;
    /**
     *
     * @var string
     */
    public $address1;
    /**
     *
     * @var string
     */
    public $address2;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }
}
