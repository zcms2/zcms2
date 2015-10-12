<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

class Medias extends ZModel
{

    /**
     *
     * @var integer
     */
    public $media_id;

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
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $mime_type;

    /**
     *
     * @var string
     */
    public $src;

    /**
     *
     * @var string
     */
    public $information;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'medias';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Medias[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Medias
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
