<?php

namespace ZCMS\Core;

use Phalcon\DI;
use Phalcon\Config\Adapter\Php as PhalconConfig;

/**
 * Class ZFactory
 */
class ZFactory
{
    /**
     * Get default config
     *
     * @return PhalconConfig
     */
    public static function config()
    {
        return new PhalconConfig(ROOT_PATH . '/app/config/config.php');
    }

    /**
     * Get default config
     *
     * @return mixed|PhalconConfig
     */
    public static function getConfig()
    {
        return DI::getDefault()->get('config');
    }
}