<?php

namespace ZCMS\Core\Social;

use Facebook\Facebook;
use ZCMS\Core\ZFactory;

require_once ROOT_PATH . '/app/libraries/FacebookSDK/src/Facebook/autoload.php';

/**
 * Class ZFacebook
 *
 * @package ZCMS\Core\Social
 */
class ZFacebook extends Facebook
{

    /**
     * @var ZFacebook
     */
    protected static $instance;

    /**
     * Instance ZFacebook
     *
     * @param array $config
     * @return ZFacebook
     */
    public static function getInstance(array $config = [])
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZFacebook($config);
        }
        return self::$instance;
    }

    /**
     * Instantiates a new Facebook super-class object
     *
     * @param array $config
     *
     * @throws \Facebook\Exceptions\FacebookSDKException;
     */
    public function __construct(array $config = [])
    {
        if (!count($config)) {
            $sysConfig = ZFactory::getConfig();
            $config = [
                'app_id' => $sysConfig->social->facebook->appID,
                'app_secret' => $sysConfig->social->facebook->appSecret,
                'permissions' => $sysConfig->social->facebook->permissions,
                'default_graph_version' => $sysConfig->social->facebook->defaultGraphVersion ? $sysConfig->social->facebook->defaultGraphVersion : 'v2.2',
            ];
        }
        parent::__construct($config);
    }
}