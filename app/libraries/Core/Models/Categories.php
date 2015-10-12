<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\ZModel;
use ZCMS\Core\Models\Behavior\NestedSet;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * Class Categories
 *
 * @package ZCMS\Core\Models
 * @method saveNode()
 * @method appendTo($target, $attributes = null)
 * @method append($target, $attributes = null)
 * @method prepend($target, $attributes = null)
 * @method prependTo($target, $attributes = null)
 * @method prev()
 * @method next()
 * @method insertBefore($target, $attributes = null)
 * @method insertAfter($target, $attributes = null)
 * @method moveBefore($target)
 * @method moveAfter($target)
 * @method moveAsFirst($target)
 * @method moveAsLast($target)
 * @method moveAsRoot()
 * @method parent()
 * @method children()
 */
class Categories extends ZModel
{
    /**
     * @var integer
     */
    public $category_id;

    /**
     * Is root if > 0, leaf if = 0
     *
     * @var integer
     */
    public $root;

    /**
     * @var integer
     */
    public $lft;

    /**
     * @var integer
     */
    public $rgt;

    /**
     * @var integer
     */
    public $level;

    /**
     * Category for module
     *
     * @var string
     */
    public $module;

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
    public $thumb;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $hits;

    /**
     * @var string 1 = published, 0 = pending, -1 trash
     */
    public $published;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $meta_desc;

    /**
     * @var string
     */
    public $meta_keywords;

    /**
     * JSON for extra SEO options
     *
     * @var string
     */
    public $metadata;

    /**
     * JSON for extra information
     *
     * @var string
     */
    public $options;

    /**
     * Validation
     *
     * @return bool
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'alias'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    /**
     * Init default value
     */
    public function beforeValidation()
    {
        $this->title = strip_tags($this->title);
        if (!$this->alias) {
            $this->alias = generateAlias($this->title);
        }
    }

    /**
     * Initialize method for model
     */
    public function initialize()
    {
        $this->addBehavior(new NestedSet(array(
            'hasManyRoots' => true,
            'primaryKey' => 'category_id',
            'leftAttribute' => 'lft',
            'rightAttribute' => 'rgt',
            'levelAttribute' => 'level',
            'rootAttribute' => 'root'
        )));
        $this->skipAttributesOnCreate([
            'root'
        ]);
    }

    /**
     * Get all roots
     *
     * @return Categories[]
     */
    public static function getRoots()
    {
        return self::find('lft = 1');
    }

    /**
     * Get root
     *
     * @param string $moduleName
     * @return Categories
     */
    public static function getRoot($moduleName)
    {
        return self::findFirst([
            'conditions' => 'module = ?0 AND lft = 1',
            'bind' => [$moduleName]
        ]);
    }

    /**
     * Get tree with root ID
     *
     * @param $root_id
     * @return Categories[]
     */
    public static function getTreeWithRootID($root_id)
    {
        return Categories::find([
            'conditions' => 'root = ?0',
            'order' => 'lft',
            'bind' => [$root_id]
        ]);
    }

    /**
     * Get tree with module
     *
     * @param $moduleName
     * @return PostCategory[]
     */
    public static function getTree($moduleName)
    {
        return Categories::find([
            'conditions' => 'module = ?0',
            'order' => 'lft',
            'bind' => [$moduleName]
        ]);
    }

}