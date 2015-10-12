<?php

use ZCMS\Core\ZTranslate;
use Phalcon\Mvc\Controller;

/**
 * Class InstallController
 *
 * @property mixed config
 * @property \Phalcon\Di di
 * @property \Phalcon\Mvc\View view
 * @property \Phalcon\Security security
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Dispatcher dispatcher
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Flash\Session flashSession
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql db
 * @property \Phalcon\Session\Adapter\Files session
 */
class InstallController extends Controller
{
    /**
     * @var string Path file config
     */
    protected $configPath;

    public function initialize()
    {
        //Set config path
        $this->configPath= ROOT_PATH . '/app/config/config.php';

        //Set base url to view
        $this->view->setVar('_baseUri', $this->config->website->baseUri);

        //Set site name
        $this->view->setVar('_siteName', $this->config->website->siteName);

        //Set system name
        $this->view->setVar('_systemName', $this->config->website->systemName);

        //Setting language for install section
        $i18n = ZTranslate::getInstance();

        //Add en-GB language
        $i18n->addLang(ROOT_PATH . '/app/languages/en-GB/en-GB.php');
        $i18n->addLang(ROOT_PATH . '/app/install/languages/en-GB/en-GB.php');
        $i18n->addLang(ROOT_PATH . '/app/languages/override/en-GB.php');

        //Add current language
        $currentLanguageTranslateFile = ROOT_PATH . '/app/install/languages/' . $this->config->website->language . '/' . $this->config->website->language . '.php';
        $currentOverrideTranslateFile = ROOT_PATH . '/app/languages/override/' . $this->config->website->language . '.php';
        $i18n->addLang($currentLanguageTranslateFile);
        $i18n->addLang($currentOverrideTranslateFile);
    }
}