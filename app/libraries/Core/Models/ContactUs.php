<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class ContactUs
 *
 * @package ZCMS\Core\Models
 */
class ContactUs extends ZModel
{

    /**
     *
     * @var integer
     */
    public $cid;

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
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var integer
     */
    public $status;

    public function initialize()
    {

    }

}
