<?php

namespace ZCMS\Core;

use Phalcon\Di;
use ZCMS\Core\Cache\ZCache;
use Phalcon\Translate\Adapter\NativeArray as NativeArray;

/**
 * Class helper add language for application
 *
 * @package   ZCMS\Core
 * @since     0.0.1
 */
class ZTranslate
{

    const ZCMS_CACHE_TRANSLATE = 'ZCMS_CACHE_TRANSLATE_';

    /**
     * @var ZTranslate
     */
    public static $instance;

    /**
     * Language code. Eg en-GB, fr-FR
     *
     * @var string
     */
    public $language;

    /**
     * Array code translation
     *
     * @var array
     */
    public $translation = [];

    /**
     * Get instance ZTranslate
     *
     * @param string $location
     * @return ZTranslate
     */
    public static function getInstance($location = 'frontend')
    {
        if (!is_array(self::$instance) || !isset(self::$instance[$location])) {
            self::$instance[$location] = new ZTranslate($location);
        }
        return self::$instance[$location];
    }

    /**
     * Instance construct
     *
     * @param $location
     */
    public function __construct($location)
    {
        $this->language = Di::getDefault()->get('config')->website->language;
        $cache = ZCache::getCore();
        $translation = $cache->get(self::ZCMS_CACHE_TRANSLATE . $location);
        if ($translation === null) {
            //Add global language
            $this->addLang(ROOT_PATH . '/app/languages/en-GB/en-GB.php');
            $this->addLang(ROOT_PATH . '/app/languages/' . $this->language . '/' . $this->language . '.php');

            //Add modules language
            global $_modules;
            foreach ($_modules as $module) {
                $this->addModuleLang($module['baseName'], $location);
            }
            $cache->save(self::ZCMS_CACHE_TRANSLATE . $location, $this->translation);
        } else {
            $this->setTranslate($translation);
        }
    }

    /**
     * Method add a language file in the translate
     *
     * @param string $filePath
     * @return bool
     */
    public function addLang($filePath = null)
    {
        if ($filePath && file_exists($filePath)) {
            $contentLang = require_once($filePath);
            if ($contentLang === true) {
                return true;
            }
            if (is_array($contentLang)) {
                $this->translation = array_merge($this->translation, $contentLang);
            } else {
                if (DEBUG) {
                    Di::getDefault()->get('flashSession')->error('Error file translation ' . $filePath);
                }
                return false;
            }
            return true;
        }
        if (DEBUG) {
            //Di::getDefault()->get('flashSession')->warning('File translation not found ' . $filePath);
        }
        return false;
    }

    /**
     * Add a module language file in the translate
     *
     * @param string $module
     * @param string $location
     */
    public function addModuleLang($module, $location = 'admin')
    {
        $basePath = ROOT_PATH . '/app/modules/' . $module . '/languages/';
        $this->addLang($basePath . 'en-GB/' . $location . '/en-GB.php');
        if ($this->language != 'en-GB') {
            $this->addLang($basePath . '/' . $this->language . '/' . $location . '/' . $this->language . '.php');
        }
    }

    public function addModulesLang($location)
    {
        global $_modules;
        if ($location == 'admin') {
            foreach ($_modules as $module) {
                $this->addModuleLang($module['baseName'], 'admin');
            }
        } elseif ($location == 'frontend') {
            foreach ($_modules as $module) {
                $this->addModuleLang($module['baseName'], 'frontend');
            }
        }
    }

    /**
     * Add a template language file in the translate
     *
     * @param string|mixed $templateName
     * @param string $location
     */
    public function addTemplateLang($templateName, $location = 'frontend')
    {
        if (is_array($templateName)) {
            foreach ($templateName as $template) {
                $basePath = ROOT_PATH . '/app/templates/' . $location . '/' . $template . '/languages';
                $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
            }
        } elseif (gettype($templateName) == 'string') {
            $basePath = ROOT_PATH . '/app/templates/' . $location . '/' . $templateName . '/languages';
            $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
        }
    }

    /**
     * Add a widget language file in the translate
     *
     * @param string|array $widgetName
     * @param string $location
     */
    public function addWidgetLang($widgetName, $location = 'admin')
    {
        if (is_array($widgetName)) {
            foreach ($widgetName as $widget) {
                $basePath = ROOT_PATH . '/app/widgets/' . $location . '/' . $widget . '/languages';
                $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
            }
        } elseif (gettype($widgetName) == 'string') {
            $basePath = ROOT_PATH . '/app/widgets/' . $location . '/' . $widgetName . '/languages';
            $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
        }
    }

    /**
     * Get Translate
     *
     * @return NativeArray
     */
    public function getTranslate()
    {
        $this->addLang(ROOT_PATH . '/app/languages/override/' . $this->language . '.php');
        return new NativeArray([
            'content' => $this->translation
        ]);
    }

    /**
     * Set Translate
     *
     * @param array $translation
     */
    private function setTranslate($translation)
    {
        $this->translation = $translation;
    }
}