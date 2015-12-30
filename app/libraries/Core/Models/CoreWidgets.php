<?php

namespace ZCMS\Core\Models;

use  Phalcon\Mvc\Model;
use ZCMS\Core\Cache\ZCache;

/**
 * Class CoreWidget
 *
 * @package ZCMS\Core\Models
 */
class CoreWidgets extends Model
{

    /**
     *
     * @var integer
     */
    public $widget_id;

    /**
     *
     * @var string
     */
    public $base_name;

    /**
     *
     * @var string
     */
    public $location;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $uri;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $authorUri;

    /**
     *
     * @var string
     */
    public $version;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     *
     * @var integer
     */
    public $is_core;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

//    protected static function _createKey($parameters)
//    {
//        $uniqueKey = array();
//
//        foreach ($parameters as $key => $value) {
//            if (is_scalar($value)) {
//                $uniqueKey[] = $key . ':' . $value;
//            } else {
//                if (is_array($value)) {
//                    $uniqueKey[] = $key . ':[' . self::_createKey($value) . ']';
//                }
//            }
//        }
//
//        return 'CoreWidgets_' . md5(join(',', $uniqueKey));
//    }
//
    public static function findFirst($parameters = null, $useCache = false)
    {
        if ($useCache) {
            $cache = ZCache::getInstance();
            $key = self::_createKey($parameters);

            $data = $cache->get($key);
            if ($data === null) {
                $data = parent::findFirst($parameters);
                $cache->save($key, $data);
            }
            return $data;
        } else {
            return parent::findFirst($parameters);
        }
    }
}
