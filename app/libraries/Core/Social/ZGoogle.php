<?php

namespace ZCMS\Core\Social;

use Phalcon\Di;
use Google_Client;
use ZCMS\Core\ZFactory;
use Google_Service_Plus;

require_once ROOT_PATH . '/app/libraries/GoogleAPI/src/Google/autoload.php';

/**
 * Class ZGoogle
 *
 * @package ZCMS\Core\Social
 */
class ZGoogle
{
    const SOCIAL_GOOGLE_ACCESS_TOKEN = '_SOCIAL_GOOGLE_ACCESS_TOKEN';

    /**
     * @var array Array construct $config = ['clientID' => '', 'clientSecret' => '', 'scope' => '']
     */
    private $config;

    /**
     * @var Google_Client
     */
    protected $client;

    /**
     * @var
     */
    public $isReady = false;

    /**
     * @var ZGoogle
     */
    protected static $instance;

    /**
     * Instance ZGoogle
     *
     * @param array $config
     * @return ZGoogle
     */
    public static function getInstance(array $config = [])
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZGoogle($config);
        }
        return self::$instance;
    }

    /**
     * Get Client
     *
     * @return Google_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Instantiates a new ZGoogle
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!count($config)) {
            $sysConfig = ZFactory::getConfig();
            $config = [
                'clientID' => $sysConfig->social->google->clientID,
                'clientSecret' => $sysConfig->social->google->clientSecret,
                'scope' => $sysConfig->social->google->scope->toArray()
            ];
        }
        $this->config = $config;
        $this->_initGoogleClient();
    }

    /**
     * Init Google Client
     */
    private function _initGoogleClient()
    {
        $this->client = new Google_Client();
        $this->client->setClientId($this->config['clientID']);
        $this->client->setClientSecret($this->config['clientSecret']);
        $this->client->setScopes($this->config['scope']);
        $this->client->setRedirectUri(BASE_URI . '/auth/google/login-callback/');
//        $session = Di::getDefault()->get('session');
//        $token = $session->get(self::SOCIAL_GOOGLE_ACCESS_TOKEN);
//        if ($token && $this->client->verifyIdToken($token)) {
//            $this->client->setAccessToken($token);
//            $this->isReady = true;
//        }
    }

    /**
     * Get url oauth
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Check redirect code from url
     *
     * @param $code
     * @return bool
     */
    public function checkRedirectCode($code)
    {
        if ($code) {
            $this->client->authenticate($code);
            $session = Di::getDefault()->get('session');
            $token = $this->client->getAccessToken();
            $session->set(self::SOCIAL_GOOGLE_ACCESS_TOKEN, $token);
            $this->client->setAccessToken($token);
            return true;
        }
        return false;
    }

    /**
     * Set the OAuth 2.0 access token using the string that resulted from calling createAuthUrl()
     *
     * @param string $token String JSON
     */
    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    /**
     * Get info
     *
     * @return array|bool Return Array if success or false if error
     */
    public function payload()
    {
        try {
            $info = $this->client->verifyIdToken()->getAttributes();
            if (is_array($info)) {
                $info = $info['payload'];
            } else {
                $info = false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $info;
    }

    public function getUserInfoToCreateAccount()
    {
        $me = $this->getMe();
        $payload = $this->payload();
        if(isset($payload['email'])){
            return [
                'email' => $payload['email'],
                'first_name' => $me->getName()->givenName,
                'last_name' => $me->getName()->familyName,
                'display_name' => $me->getDisplayName(),
                'google_token' => $this->client->getAccessToken()
            ];
        }else{
            return false;
        }
    }

    /**
     * @return \Google_Service_Plus_Person
     */
    public function getMe()
    {
        $plus = new Google_Service_Plus($this->client);
        return $plus->people->get('me');
    }
}