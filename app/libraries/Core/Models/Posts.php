<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;

/**
 * Class Posts
 *
 * @package ZCMS\Core\Models
 */
class Posts extends ZModel
{
    /**
     * @var integer
     */
    public $post_id;

    /**
     * @var integer
     */
    public $post_parent;

    /**
     * @var string
     */
    public $post_type;

    /**
     * @var integer
     */
    public $category_id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $alias;

    /**
     * @var string
     */
    public $images;

    /**
     * @var integer
     */
    public $hits;

    /**
     * @var string
     */
    public $tags;

    /**
     * @var integer
     */
    public $version;

    /**
     * @var integer
     */
    public $published;

    /**
     * @var string
     */
    public $published_at;

    /**
     * @var string
     */
    public $intro_text;


    /**
     * @var string
     */
    public $full_text;

    /**
     * @var string
     */
    public $meta_desc;

    /**
     * @var string
     */
    public $meta_keywords;

    /**
     * @var string
     */
    public $metadata;

    /**
     * @var string
     */
    public $options;

    /**
     * @var integer
     */
    public $comment_count;

    /**
     * @var integer
     */
    public $comment_status;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    public function beforeSave()
    {
        $this->title = strip_tags($this->title);
        if($this->alias == ''){
            $this->alias = generateAlias($this->title);
        }
    }
}