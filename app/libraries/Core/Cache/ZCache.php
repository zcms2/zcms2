<?php

namespace ZCMS\Core\Cache;

use Phalcon\Di;
use Phalcon\Cache\Backend\Memcache as MemcacheCache;
use Phalcon\Cache\Backend\Apc as ApcCache;
use Phalcon\Cache\Backend\Redis as RedisCache;
use Phalcon\Cache\Backend\File as FileCache;
use Phalcon\Cache\Frontend\Data as DataFrontend;

/**
 * Class ZCache
 *
 * @package ZCMS\Core\Cache
 */
class ZCache
{

    const memCache = 'Memcache';

    const APC_CACHE = 'ApcCache';

    const REDIS_CACHE = 'RedisCache';

    const FILE_CACHE = 'FileCache';

    /**
     * @var ZCache
     */
    public static $instance;

    /**
     * @var ApcCache|MemcacheCache|RedisCache|FileCache
     */
    public $cache;

    /**
     * @var string
     */
    public $cacheName;

    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var string
     */
    public $prefix;

    /**
     * @var int
     */
    public $cache_status = 0;

    /**
     * @var int
     */
    public $lifeTime = null;

    /**
     * Get instance object
     *
     * @param string $cacheName string A-Z
     * @param string $cacheType = Memcache | ApcCache | FileCache | RedisCache
     * @param int $lifeTime
     * @return ZCache
     */
    public static function getInstance($cacheName = 'GLOBAL', $cacheType = 'FileCache', $lifeTime = null)
    {
        if (!is_array(self::$instance) || !isset(self::$instance[$cacheName])) {
            self::$instance[$cacheName] = new ZCache($cacheName, $cacheType, $lifeTime);
        }
        return self::$instance[$cacheName];
    }

    /**
     * Get global cache
     *
     * @param string $key
     * @param integer $lifetime
     * @return mixed
     */
    public static function getCache($key, $lifetime = null)
    {
        $cache = self::getInstance();
        return $cache->get($key, $lifetime);
    }

    /**
     * Set global cache
     *
     * @param string $keyName
     * @param mixed $content
     * @param integer $lifetime
     * @param $stopBuffer
     */
    public function setCache($keyName = null, $content = null, $lifetime = null, $stopBuffer = null)
    {
        $cache = self::getInstance();
        $cache->save($keyName, $content, $lifetime, $stopBuffer);
    }

    /**
     * Get core cache
     *
     * @param string $key
     * @param integer $lifetime
     * @return mixed
     */
    public static function getCoreCache($key, $lifetime = null)
    {
        $cache = self::getInstance(ZCMS_CACHE_CORE_SYSTEM);
        return $cache->get($key, $lifetime);
    }

    /**
     * Get core
     *
     * @return ZCache
     */
    public static function getCore()
    {
        return self::getInstance(ZCMS_CACHE_CORE_SYSTEM);
    }

    /**
     * Set core cache
     *
     * @param string $keyName
     * @param mixed $content
     * @param integer $lifetime
     * @param $stopBuffer
     */
    public function setCoreCache($keyName = null, $content = null, $lifetime = null, $stopBuffer = null)
    {
        $cache = self::getInstance(ZCMS_CACHE_CORE_SYSTEM);
        $cache->save($keyName, $content, $lifetime, $stopBuffer);
    }

    /**
     * Starts a cache. The keyName allows to identify the created fragment
     *
     * @param int|string $keyName
     * @param integer $lifetime
     * @return mixed
     */
    public function start($keyName, $lifetime = null)
    {
        return $this->cache->start($keyName, $lifetime);
    }

    /**
     * Checks whether the cache has starting buffering or not
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->cache->isStarted();
    }

    /**
     * Checks whether the last cache is fresh or cached
     *
     * @return boolean
     */
    public function isFresh()
    {
        return $this->cache->isFresh();
    }

    /**
     * Constructor
     *
     * @param string $cacheName
     * @param string $cacheType
     * @param int $lifeTime
     */
    public function __construct($cacheName = 'GLOBAL', $cacheType = 'ApcCache', $lifeTime = null)
    {
        if ($lifeTime) {
            $this->lifeTime = $lifeTime;
        }
        $this->config = DI::getDefault()->get('config');
        $this->cacheName = $this->config->cachePrefix . $cacheName;
        $this->prefix = $this->config->cachePrefix;
        if ($cacheType == self::memCache && $this->config->memCache->status) {
            $this->_initMemCached();
        } elseif ($cacheType == self::APC_CACHE && $this->config->apcCache->status) {
            $this->_initApcCache();
        } elseif ($cacheType == self::REDIS_CACHE && $this->config->redisCache->status) {
            $this->_initRedisCache();
        } else {
            $this->_initFileCache();
        }
    }

    /**
     * Init Redis cache
     */
    private function _initRedisCache()
    {
        if (!$this->lifeTime) {
            $this->lifeTime = $this->config->redisCache->lifetime;
        }
        $this->cache_status = $this->config->redisCache->status;
        $this->cache = new RedisCache(
            new DataFrontend(['lifetime' => $this->lifeTime]),
            [
                'host' => $this->config->redisCache->host,
                'port' => $this->config->redisCache->port,
                'auth' => $this->config->redisCache->auth,
                'persistent' => $this->config->redisCache->persistent
            ]
        );
    }

    /**
     * Init file cache
     */
    private function _initFileCache()
    {
        if (!$this->lifeTime) {
            $this->lifeTime = $this->config->fileCache->lifetime;
        }
        $this->cache_status = $this->config->fileCache->status;
        $this->cache = new FileCache(
            new DataFrontend(['lifetime' => $this->config->fileCache->lifetime]),
            [
                'prefix' => $this->prefix,
                'cacheDir' => ROOT_PATH . $this->config->fileCache->cacheDir
            ]
        );
    }

    /**
     * Init Memcached
     */
    private function _initMemCached()
    {
        if (!$this->lifeTime) {
            $this->lifeTime = $this->config->memCache->lifetime;
        }
        $this->cache_status = $this->config->memCache->status;
        $this->cache = new MemcacheCache(
            new DataFrontend(['lifetime' => $this->lifeTime]),
            [
                'prefix' => $this->prefix,
                'host' => $this->config->memCache->host,
                'port' => $this->config->memCache->port
            ]
        );
    }

    /**
     * Init Apc cache
     */
    private function _initApcCache()
    {
        if (!$this->lifeTime) {
            $this->lifeTime = $this->config->apcCache->lifetime;
        }
        $this->cache_status = $this->config->apcCache->status;
        $this->cache = new ApcCache(
            new DataFrontend(['lifetime' => $this->lifeTime]),
            [
                'prefix' => $this->prefix
            ]
        );
    }

    /**
     * Returns a cached content
     *
     * @param string $keyName
     * @param int $lifetime
     * @return mixed
     */
    public function get($keyName, $lifetime = null)
    {
        if ($this->cache_status) {
            return $this->cache->get($keyName, $lifetime);
        } else {
            return null;
        }
    }

    /**
     * Checks if cache exists and it isn't expired
     *
     * @param string $keyName
     * @param integer $lifetime
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        return $this->cache->exists($keyName, $lifetime);
    }


    /**
     * Stores cached content
     *
     * @param string $keyName
     * @param string $content
     * @param int $lifetime
     * @param boolean $stopBuffer
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = null)
    {
        if ($this->cache_status) {
            $this->cache->save($keyName, $content, $lifetime, $stopBuffer);
        }
    }

    /**
     * Stores cached content
     *
     * @param string $keyName
     * @param string $content
     * @param int $lifetime
     * @param boolean $stopBuffer
     */
    public function set($keyName = null, $content = null, $lifetime = null, $stopBuffer = null)
    {
        if ($this->cache_status) {
            $this->cache->save($keyName, $content, $lifetime, $stopBuffer);
        }
    }

    /**
     * Delete all cache
     */
    public static function flush()
    {
        if (is_array(self::$instance)) {
            foreach (self::$instance as $zCache) {
                $zCache->cache->flush();
            }
        }
    }

    /**
     * Delete cache by name
     *
     * @param string $cacheName
     * @return bool
     */
    public static function flushCacheByName($cacheName = null)
    {
        if ($cacheName) {
            if (is_array(self::$instance) && isset(self::$instance[$cacheName])) {
                self::$instance[$cacheName]->cache->flush();
                return true;
            }
        }
        return false;
    }
}