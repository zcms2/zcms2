<?php

namespace ZCMS\Frontend\Auth\Controllers;

use Facebook\FacebookRequest;
use ZCMS\Core\Social\ZFacebook;
use ZCMS\Core\Social\ZSocialHelper;
use ZCMS\Core\ZFrontController;

/**
 * Class IndexController
 *
 * @package ZCMS\Frontend\Auth\FacebookController
 */
class FacebookController extends ZFrontController
{
    /**
     * Login callback
     */
    public function loginAction()
    {
        $error = $this->request->get('error', 'string');
        if ($error) {
            $this->response->redirect('/');
            return;
        }
        $fb = ZFacebook::getInstance();

        $accessToken = null;

        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Exception $e) {
            $this->flashSession->notice('Cannot access');
            $this->response->redirect('/user/login/');
            return;
        }

        if (isset($accessToken)) {
            // Logged in!
            $this->session->set('_SOCIAL_FACEBOOK_ACCESS_TOKEN', (string)$accessToken);
            $fb->setDefaultAccessToken((string)$accessToken);
            try {
                //http://graph.facebook.com/1530517127225987/picture/?width=128&height=128
                $response = $fb->get('/me?fields=email,first_name,last_name');
                $userNode = $response->getGraphUser();
                $response = $fb->get('/me/picture?width=150&height=150');
                $status = $this->_process($userNode, $response->getHeaders()['Location'], (string)$accessToken);
                if ($status['success'] && $status['message'] == null) {
                    $this->response->redirect('/');
                } elseif ($status['success'] && $status['message'] != null) {
                    $this->flashSession->success($status['message']);
                    $this->response->redirect('/user/login/');
                } elseif (!$status['success']) {
                    $this->flashSession->notice($status['message']);
                    $this->response->redirect('/user/login/');
                }
                return;
            } catch (\Exception $e) {
                $this->flashSession->notice('_ZT_Facebook server is busy, please try again later!');
                $this->response->redirect('/user/login/');
                return;
            }
        }
    }

    /**
     * Process login with Facebook
     *
     * @param \Facebook\GraphNodes\GraphUser $userNode
     * @param string $avatar
     * @param string $accessToken
     * @return array
     */
    private function _process($userNode, $avatar = '', $accessToken)
    {
        $userInfo = [];
        $userInfo['email'] = $userNode->getField('email');
        $userInfo['first_name'] = $userNode->getFirstName();
        $userInfo['last_name'] = $userNode->getLastName();
        $userInfo['facebook_id'] = $userNode->getId();
        $userInfo['avatar'] = $avatar;
        $userInfo['facebook_token'] = $accessToken;
        return (new ZSocialHelper($userInfo, 'facebook'))->process();
    }
}