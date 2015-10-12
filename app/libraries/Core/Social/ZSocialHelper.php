<?php

namespace ZCMS\Core\Social;

use ZCMS\Core\ZEmail;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Models\CoreOptions;

/**
 * Class ZSocialHelper
 *
 * @package ZCMS\Core\Social
 */
class ZSocialHelper
{
    /**
     * @var array
     */
    public $userInfo;

    /**
     * @var string
     */
    public $socialName;

    /**
     * @var array
     */
    public static $socialSupport = [
        'facebook',
        'google',
        'twitter'
    ];

    /**
     * @var Users
     */
    public $user;

    /**
     * @var integer
     */
    public $defaultCustomerRoleID;

    /**
     * Init social helper
     *
     * @param array $userInfo
     * @param string $socialName Value = facebook, google, twitter
     */
    public function __construct(array $userInfo, $socialName = '')
    {
        $this->userInfo = $userInfo;
        $this->socialName = strtolower($socialName);
        $this->defaultCustomerRoleID = UserRoles::getDefaultCustomerRoleID();
    }

    /**
     * Process login with social
     *
     * @return array
     */
    public function process()
    {
        if ($this->userInfo['first_name'] && $this->userInfo['last_name'] && $this->userInfo['email'] && in_array($this->socialName, self::$socialSupport)) {
            $messageActive = 'Please Activate Account from Email';
            $messageFailed = 'System is busy, please try again later!';
            $autoLoginIfAccountExists = CoreOptions::getOptions('auto_login_if_account_exists_with_' . $this->socialName, 'zcms', 0);
            $sendEmailActivateAccount = CoreOptions::getOptions('verify_register_or_exist_account_with_' . $this->socialName, 'zcms', 1);
            $this->user = Users::findFirst([
                'conditions' => 'email = ?0',
                'bind' => [$this->userInfo['email']]
            ]);
            $propertyName = 'is_active_' . $this->socialName;
            if ($this->user) {
                if (isset($this->userInfo['avatar'])) {
                    $this->user->avatar = $this->userInfo['avatar'];
                }
                if (isset($this->userInfo['facebook_token'])) {
                    $this->user->facebook_token = $this->userInfo['facebook_token'];
                }
                if (isset($this->userInfo['google_token'])) {
                    $this->user->google_token = $this->userInfo['google_token'];
                }
                $this->user->save();
                if ($this->user->$propertyName == 1) {
                    $this->user->loginCurrentUSer();
                    return [
                        'success' => true,
                        'message' => ''
                    ];
                }
                if ($autoLoginIfAccountExists) {
                    $this->_updateLoginWithSocial();
                    $this->user->loginCurrentUSer();
                    $this->_generateWelcomeWithSocial();
                    return [
                        'success' => true,
                        'message' => null
                    ];
                } else {
                    $ok = $this->_generateActiveAccountWithSocial();
                    if ($ok) {
                        return [
                            'success' => true,
                            'message' => $messageActive
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => $messageFailed
                        ];
                    }
                }
            } else {
                $this->user = new Users();
                $password = randomString(8);
                $this->user->generatePassword($password);
                if (!$this->user->role_id) {
                    $this->user->role_id = $this->defaultCustomerRoleID;
                }
                $this->user->first_name = $this->userInfo['first_name'];
                $this->user->last_name = $this->userInfo['last_name'];
                $this->user->email = $this->userInfo['email'];
                if (isset($this->userInfo['avatar'])) {
                    $this->user->avatar = $this->userInfo['avatar'];
                }
                if (isset($this->userInfo['facebook_token'])) {
                    $this->user->facebook_token = $this->userInfo['facebook_token'];
                }
                if (isset($this->userInfo['google_token'])) {
                    $this->user->google_token = $this->userInfo['google_token'];
                }
                if ($sendEmailActivateAccount) {
                    $ok = $this->_generateActiveAccountWithSocial($password);
                    $this->user->save();
                    if ($ok) {
                        return [
                            'success' => true,
                            'message' => $messageActive
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => $messageFailed
                        ];
                    }
                } else {
                    $this->user->is_active = 1;
                    if ($this->socialName == 'facebook') {
                        $this->user->is_active_facebook = 1;
                    } else {
                        $this->user->is_active_facebook = 0;
                    }
                    if ($this->socialName == 'google') {
                        $this->user->is_active_google = 1;
                    } else {
                        $this->user->is_active_google = 0;
                    }
                    $this->user->role_id = $this->defaultCustomerRoleID;
                    $this->_generateWelcomeWithSocial($password);
                    if ($this->user->save()) {
                        $this->user->loginCurrentUSer();
                        return [
                            'success' => true,
                            'message' => null
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => $messageFailed
                        ];
                    }
                }
            }
        }
        return [
            'success' => false,
            'message' => 'Your info invalid, please contact to admin!'
        ];
    }

    /**
     * Update user in send email activate login with social
     *
     * @param string $password
     * @return bool
     */
    private function _generateActiveAccountWithSocial($password = '')
    {
        if ($this->defaultCustomerRoleID) {
            $this->user->active_account_token = randomString(100) . time() . '_' . base64_encode($this->socialName);
            $this->user->active_account_type = $this->socialName;
            $this->user->is_active = 0;
            if ($this->socialName == 'facebook' && isset($this->userInfo['facebook_id'])) {
                $this->user->facebook_id = $this->userInfo['facebook_id'];
            }
            if (!$this->user->role_id) {
                $this->user->role_id = $this->defaultCustomerRoleID;
            }
            $data = $this->user->toArray();
            $data['password'] = $password;
            $email = ZEmail::getInstance();
            if ($this->user->save()) {
                $email->setSubject('Activate Account')
                    ->addTo($this->user->email, $this->user->first_name . $this->user->last_name)
                    ->setTemplate('auth', 'register_with_' . $this->socialName, $data)->send();
                return true;
            }
        }
        return false;
    }

    /**
     * Welcome email
     *
     * @param string $password
     * @return bool
     */
    private function _generateWelcomeWithSocial($password = '')
    {
        $data = $this->user->toArray();
        $data['password'] = $password;
        $email = ZEmail::getInstance();
        if ($this->user->save()) {
            $email->setSubject('Welcome to website')
                ->addTo($this->user->email, $this->user->first_name . $this->user->last_name)
                ->setTemplate('auth', 'welcome_with_' . $this->socialName, $data)->send();
            return true;
        }
        return false;
    }

    /**
     * Update login with social
     *
     * @return bool
     */
    private function _updateLoginWithSocial()
    {
        if ($this->socialName == 'facebook') {
            $this->user->is_active_facebook = 1;
            if (isset($this->userInfo['facebook_id'])) {
                $this->user->facebook_id = $this->userInfo['facebook_id'];
            }
        } elseif ($this->socialName == 'google') {
            $this->user->is_active_google = 1;
        }
        if (!$this->user->active_account_at) {
            $this->user->active_account_at = date('Y-m-d H:i:s');
        }
        return $this->user->save();
    }

    /**
     * Active login with social
     *
     * @param $token
     * @return bool
     */
    public static function processActivateWithToken($token)
    {
        if (strlen($token) > 100) {
            /**
             * @var Users $user
             */
            $user = Users::findFirst([
                'conditions' => 'active_account_token = ?0',
                'bind' => [$token]
            ]);
            if ($user) {
                if ($user->active_account_type != '') {
                    if ($user->active_account_type == 'facebook') {
                        $user->is_active_facebook = 1;
                    } elseif ($user->active_account_type == 'google') {
                        $user->is_active_google = 1;
                    }
                    if (!$user->active_account_at) {
                        $user->active_account_at = date('Y-m-d H:i:s');
                    }
                    $user->active_account_type = null;
                    $user->active_account_token = null;
                    $user->is_active = 1;
                    if ($user->save()) {
                        $user->loginCurrentUSer();
                        return true;
                    }
                }
            }
        }
        return false;
    }
}