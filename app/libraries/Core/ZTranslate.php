<?php

namespace ZCMS\Core;

use Phalcon\Di as PhalconDI;
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

    const ZCMS_Core_ZTranslate = 'ZCMS_Core_ZTranslate';

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
     * @return ZTranslate
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZTranslate();
        }
        return self::$instance;
    }

    /**
     * Instance construct
     */
    public function __construct()
    {
        $this->language = PhalconDI::getDefault()->get('config')->website->language;
        global $APP_LOCATION;
        if ($APP_LOCATION) {
            $cache = ZCache::getInstance();
            $translation = $cache->get('ZCMS_Core_ZTranslate' . $APP_LOCATION);

            if ($translation === null) {
                $modules = get_child_folder(ROOT_PATH . "/app/{$APP_LOCATION}/");
                $this->addLang(ROOT_PATH . '/app/languages/en-GB/en-GB.php');
                $this->addLang(ROOT_PATH . '/app/languages/' . $this->language . '/' . $this->language . '.php');
                $this->addModuleLang($modules, $APP_LOCATION);
                $cache->save('ZCMS_Core_ZTranslate', $this->translation);
            } else {
                $this->setTranslate($translation);
            }
        }
    }

    /**
     * Method add a language file in the translate
     *
     * @param string $filePath
     * @return bool
     */
    public function addLang($filePath = '')
    {
        if (file_exists($filePath)) {
            $contentLang = require_once($filePath);
            if ($contentLang === true) {
                return true;
            }
            if (is_array($contentLang)) {
                $this->translation = array_merge($this->translation, $contentLang);
            } else {
                if (DEBUG) {
                    PhalconDI::getDefault()->get('flashSession')->error('Error file translation ' . $filePath);
                }
                return false;
            }
            return true;
        }
        if (DEBUG) {
            //PhalconDI::getDefault()->get('flashSession')->warning('File translation not found ' . $filePath);
        }
        return false;
    }

    /**
     * Add a module language file in the translate
     *
     * @param string|mixed $moduleName
     * @param string $location
     */
    public function addModuleLang($moduleName, $location = 'backend')
    {
        if (is_array($moduleName)) {
            foreach ($moduleName as $module_base_name) {
                $basePath = ROOT_PATH . '/app/' . $location . '/' . $module_base_name . '/languages';
                $this->addLang($basePath . '/en-GB/en-GB.php');
                if ($this->language != 'en-GB') {
                    $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
                }
            }
        } elseif (gettype($moduleName) == 'string') {
            $basePath = ROOT_PATH . '/app/' . $location . '/' . $moduleName . '/languages';
            $this->addLang($basePath . '/en-GB/en-GB.php');
            if ($this->language != 'en-GB') {
                $this->addLang($basePath . '/' . $this->language . '/' . $this->language . '.php');
            }
        }
    }

    /**
     * Add a template language file in the translate
     *
     * @param string|mixed $templateName
     * @param string $location
     */
    public function addTemplateLang($templateName, $location = 'backend')
    {
        if (is_array($templateName)) {
            foreach ($templateName as $template_base_name) {
                $basePath = ROOT_PATH . '/app/templates/' . $location . '/' . $template_base_name . '/languages';
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
    public function addWidgetLang($widgetName, $location = 'backend')
    {
        if (is_array($widgetName)) {
            foreach ($widgetName as $widget_base_name) {
                $basePath = ROOT_PATH . '/app/widgets/' . $location . '/' . $widget_base_name . '/languages';
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
    public function setTranslate($translation)
    {
        $this->translation = $translation;
    }
}