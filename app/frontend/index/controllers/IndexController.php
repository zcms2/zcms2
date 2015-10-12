<?php

namespace ZCMS\Frontend\Index\Controllers;

use ZCMS\Core\ZFrontController;
use ZCMS\Core\ZSEO;
use ZCMS\Frontend\QuizFight\Helpers\FightHelper;

/**
 * Class IndexController
 *
 * @package ZCMS\Frontend\Index\Controllers
 */
class IndexController extends ZFrontController
{
    public function indexAction()
    {
        if (!$this->_user) {
            $this->view->cache(
                array(
                    "lifetime" => 60
                )
            );
        }
        $fightHelper = new FightHelper();
        $this->view->setVar('topUsers', $fightHelper->getTopUsers(5));
        $this->view->setVar('latestUsers', $fightHelper->getLatestUsers(6));
        $this->view->setVar('timeLines', $fightHelper->getTimeLines(10));
        ZSEO::getInstance()->setTitle('Đấu trường Tiếng Anh | English Fights')
            ->setDescription('Cùng nhau ôn tập kiến thức tiếng anh, kiểm tra trắc nghiệm tiếng anh online. Thi thử đề thi TOEIC online!')
            ->setKeywords('thi tài tiếng anh, toeic online, đấu trường tiếng anh');

    }

    public function privacyPolicyAction()
    {

    }
}