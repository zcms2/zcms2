<?php

namespace ZCMS\Frontend\Auth\Controllers;

use ZCMS\Core\Social\ZSocialHelper;
use ZCMS\Core\ZFrontController;

/**
 * Class ActivateController
 *
 * @package ZCMS\Frontend\Auth\Controllers
 */
class ActivateController extends ZFrontController
{
   public function indexAction(){
       $token = $this->request->get('token','string','');
       $status = ZSocialHelper::processActivateWithToken($token);
       if($status){
           $this->flashSession->success('_ZT_Active account successfully!');
           $this->response->redirect('/');
       }else{
           $this->flashSession->error('_ZT_Active account failed!');
           $this->response->redirect('/user/login/');
       }
       return;
   }
}