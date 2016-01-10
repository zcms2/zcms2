<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;
use ZCMS\Core\Cache\ZCache;

/**
 * Class CoreOptions
 *
 * @package ZCMS\Core\Models
 */
class CoreOptions extends Model
{
    /**
     * Cache options key
     */
    const ZCMS_CACHE_MODEL_CORE_OPTIONS = 'ZCMS_CACHE_MODEL_CORE_OPTIONS';

    /**
     * @var int
     */
    public $option_id;

    /**
     * @var string
     */
    public $option_scope;

    /**
     * @var string
     */
    public $option_name;

    /**
     * @var string
     */
    public $option_value;

    /**
     * If value equal 1 then option autoload to CACHE
     *
     * @var int Value in [0,1]
     * @return array
     */
    public $autoload;

    /**
     * Init options
     *
     * @param bool|false $reloadCache
     * @return array|mixed
     */
    public static function initOptions($reloadCache = false)
    {
        $cache = ZCache::getCore();

        //Load cache options
        $optionsCache = $cache->get(self::ZCMS_CACHE_MODEL_CORE_OPTIONS);

        //If reload cache Or current cache is null
        if ($reloadCache || $optionsCache === null) {
            $options = self::find([
                'columns' => ['option_scope', 'option_name', 'option_value'],
                'conditions' => 'autoload = 1'
            ]);
            $optionsCache = [];
            foreach ($options as $option) {
                $optionsCache[$option->option_name . '_' . $option->option_scope] = $option->option_value;
            }
            $cache->save(self::ZCMS_CACHE_MODEL_CORE_OPTIONS, $optionsCache);
        }
        return $optionsCache;
    }

    /**
     * Get option
     *
     * @param $name
     * @param string $scope
     * @param mixed $default
     * @return mixed
     */
    public static function getOptions($name, $scope = '', $default = null)
    {
        $cache = ZCache::getCore();
        $optionsCache = $cache->get(self::ZCMS_CACHE_MODEL_CORE_OPTIONS);
        if ($optionsCache === null) {
            $optionsCache = self::initOrUpdateCacheOptions(true);
        }
        if (isset($optionsCache[$name . '_' . $scope])) {
            return $optionsCache[$name . '_' . $scope];
        }
        return $default;
    }

    /**
     * Execute before create
     */
    public function beforeCreate()
    {
        if ($this->autoload) {
            self::initOrUpdateCacheOptions(true);
        }
    }

    /**
     * Execute before update
     */
    public function beforeUpdate()
    {
        if ($this->autoload) {
            self::initOrUpdateCacheOptions(true);
        }
    }
}