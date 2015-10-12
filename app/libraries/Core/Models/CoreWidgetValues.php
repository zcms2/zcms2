<?php

namespace ZCMS\Core\Models;

use ZCMS\Core\Cache\ZCache;
use ZCMS\Core\ZModel;

/**
 * Class CoreWidgetValue
 *
 * @package ZCMS\Core\Models
 */
class CoreWidgetValues extends ZModel
{

    /**
     *
     * @var integer
     */
    public $widget_value_id;

    /**
     *
     * @var string
     */
    public $sidebar_base_name;

    /**
     *
     * @var string
     */
    public $theme_name;

    /**
     *
     * @var string
     */
    public $class_name;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $options;

    /**
     *
     * @var integer
     */
    public $ordering;

    /**
     *
     * @var integer
     */
    public $published;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    protected static function _createKey($parameters)
    {
        $uniqueKey = array();

        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ':' . $value;
            } else {
                if (is_array($value)) {
                    $uniqueKey[] = $key . ':[' . self::_createKey($value) . ']';
                }
            }
        }

        return 'CoreWidgetValues_' . md5(join(',', $uniqueKey));
    }

    public static function findFirst($parameters = null)
    {
        $cache = ZCache::getInstance();
        $key = self::_createKey($parameters);

        $data = $cache->get($key);
        if ($data === null) {
            $data = parent::findFirst($parameters);
            $cache->save($key, $data);
        }
        return $data;
    }
}
