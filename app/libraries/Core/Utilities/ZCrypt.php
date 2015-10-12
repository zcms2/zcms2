<?php

namespace ZCMS\Core\Utilities;

use Phalcon\Crypt;

/**
 * Class ZCrypt
 *
 * @package ZCMS\Core\Utilities
 */
class ZCrypt extends Crypt
{
    const ZCMS_SYSTEM_ENCRYPT_KEY = "BLA BLA";

    /**
     * @var ZCrypt
     */
    public static $instance;

    /**
     * @var string
     */
    protected $encryptKey;

    /**
     * Constructor
     *
     * @param string $encryptKey
     */
    public function __construct($encryptKey = null)
    {
        if ($encryptKey) {
            $this->encryptKey = $encryptKey;
        } else {
            $this->encryptKey = self::ZCMS_SYSTEM_ENCRYPT_KEY;
        }
    }

    /**
     * Get Encrypt Key
     */
    public function getEncryptKey()
    {
        return $this->encryptKey;
    }

    /**
     * Get instance object
     *
     * @param string $encryptKey array or string JSON
     * @return ZCrypt
     */

    /**
     * @param string $encryptKey
     * @return ZCrypt
     */
    public static function getInstance($encryptKey = null)
    {

        if (!is_object(self::$instance)) {
            self::$instance = new ZCrypt($encryptKey);
            self::$instance->setPadding(Crypt::PADDING_ZERO);
        }
        return self::$instance;
    }

    /**
     * Encrypts a text
     *
     *<code>
     *    $encrypted = $crypt->encrypt("Ultra-secret text", "encrypt password");
     *</code>
     *
     * @param string $text
     * @param string $key
     * @return string
     */
    public function encrypt($text, $key = null)
    {
        if ($key != null) {
            return base64_encode(parent::encrypt($text, $key));
        }
        return base64_encode(parent::encrypt($text, $this->encryptKey));
    }

    /**
     * Decrypts an encrypted text
     *
     *<code>
     *    echo $crypt->decrypt($encrypted, "decrypt password");
     *</code>
     *
     * @param string $text
     * @param string $key
     * @return string
     */
    public function decrypt($text, $key = null)
    {
        if ($key != null) {
            return parent::decrypt(base64_decode($text), $key);
        }
        return parent::decrypt(base64_decode($text), $this->encryptKey);
    }
}