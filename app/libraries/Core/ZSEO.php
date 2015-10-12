<?php

namespace ZCMS\Core;

use Phalcon\Di;
use Phalcon\Escaper;

/**
 * Class ZSEO
 *
 * @package ZCMS\Core
 */
class ZSEO
{

    /**
     * @var \ZCMS\Core\ZSEO
     */
    public static $instance;

    /**
     *
     * <code>
     * <head prefix=
     * "og: http://ogp.me/ns#
     * fb: http://ogp.me/ns/fb#
     * product: http://ogp.me/ns/product#">
     * </code>
     * @var
     */
    public $headerPrefix = 'og: http://ogp.me/ns#';

    /**
     * @var string Title
     */
    public $title = '';

    /**
     * @var bool
     */
    public $empty_title = false;

    /**
     * @var string Description
     */
    public $description = '';

    /**
     * @var string Language
     */
    public $language = 'en-gb';

    /**
     * @var string Html direction setting dir="rtl" | "ltr"
     */
    public $direction = 'ltr';

    /**
     * @var string Html charset
     */
    public $charset = 'utf-8';

    /**
     * @var array Html Meta key
     */
    public $metaTags = [];

    /**
     * @var array Html link rel - href
     */
    public $link = [];

    /**
     * @var array
     */
    public $link_mapping = [];

    /**
     * @var string
     */
    public $icon = '';

    /**
     * @var string
     */
    public $keywords = '';

    /**
     * @var string
     */
    public $redirect_301 = '';

    /**
     * Get instance object
     *
     * @param array|string $params array or string JSON
     * @return ZSEO
     */
    public static function getInstance($params = null)
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZSEO($params);
        }
        return self::$instance;
    }

    /**
     * Construct Method
     *
     * @param array|string $params array or string JSON
     */
    public function __construct($params = null)
    {
        $this->e = new Escaper();
        $this->initialize($params);
    }

    /**
     * @param array|object $params
     * @return $this
     */
    public function initialize($params = null)
    {
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        if ($params != null) {
            if (is_object($params)) {
                $this->setTitle($params->title);
                $this->redirect_301 = $params->redirect_301;
                $this->setMetaName('robots', $params->robots);
                $this->setMetaProperty('og:title', $params->title);
                $this->setDescription($params->description);
                $this->setKeywords($params->keywords);
            } else {
                $this->setTitle($params['title']);
                $this->redirect_301 = $params['redirect_301'];
                $this->setMetaName('robots', $params['robots']);
                $this->setMetaProperty('og:title', $params['title']);
                $this->setDescription($params['description']);
                $this->setKeywords($params['keywords']);
            }
        } else {
            $this->empty_title = true;
        }
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     * @param string $keywords
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * Redirect 301
     *
     * @param $redirect_301
     */
    public function setRedirect301($redirect_301)
    {
        if ($redirect_301 != '') {
            $this->redirect_301 = $redirect_301;
        }
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set link
     *
     * @param string $relType
     * @param string $href
     * @param string $attributeName
     * @param string $attributeValue
     *
     * <code>
     * <meta http-equiv="content-language" content="vi" />
     * </code>
     *
     * @return $this
     */
    public function setLink($relType, $href, $attributeName = '', $attributeValue = '')
    {
        $this->link[] = [
            'rel' => $relType,
            'href' => $href,
            'attributeName' => $attributeName,
            'attributeValue' => $attributeValue,
        ];

        $this->link_mapping[$relType] = count($this->link);
        return $this;
    }

    /**
     * Get link by rel type
     *
     * @param string $relType
     * @param string $defaultValue
     * @return string
     */
    public function getLinkByRelType($relType, $defaultValue = null)
    {
        if (isset($this->link_mapping[$relType])) {
            return $this->link[$this->link_mapping[$relType] - 1];
        }
        return $defaultValue;
    }


    /**
     * Set meta tag global
     *
     * @param string $metaKey
     * @param string $metaContent
     * @param string $metaKeyName
     * @param bool $merger
     *
     * <code>
     * <meta http-equiv="content-language" content="en" />
     * </code>
     *
     * @return $this
     */
    public function setMetaTag($metaKey, $metaContent, $metaKeyName, $merger = false)
    {
        if ($merger) {
            $this->metaTags[$metaKey][$metaKeyName][] = $metaContent;
        } else {
            $this->metaTags[$metaKey][$metaKeyName] = $metaContent;
        }
        return $this;
    }

    /**
     * Set meta name
     *
     * @param string $metaKey
     * @param string $metaContent
     * @param bool $merger
     *
     * <code>
     * <meta name="robots" content="index,follow" />
     * </code>
     *
     * @return $this
     */
    public function setMetaName($metaKey, $metaContent, $merger = false)
    {
        if ($merger) {
            $this->metaTags['name'][$metaKey][] = $metaContent;
        } else {
            $this->metaTags['name'][$metaKey] = $metaContent;
        }
        return $this;
    }

    /**
     * Set meta property
     *
     * @param string $metaContent
     * @param string $metaKey
     * @param bool $merger
     *
     * <code>
     * <meta property="og:title" content="ZCMS" />
     * </code>
     *
     * @return $this
     */
    public function setMetaProperty($metaKey, $metaContent, $merger = false)
    {
        if ($merger) {
            $this->metaTags['property'][$metaKey][] = $metaContent;
        } else {
            $this->metaTags['property'][$metaKey] = $metaContent;
        }
        return $this;
    }

    /**
     * Set header prefix
     * @param string $str
     * @return $this
     */
    public function setHeaderPrefix($str)
    {
        $this->headerPrefix = $str;
        return $this;
    }

    /**
     * Get header prefix
     *
     * @return string
     */
    public function getHeaderPrefix()
    {
        return $this->headerPrefix;
    }

    /**
     * Set charset
     *
     * @param string $charset_name
     * @return $this
     */
    public function setCharset($charset_name = 'utf-8')
    {
        $this->charset = $charset_name;
        return $this;
    }

    /**
     * Get charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set language
     *
     * @param string $language_code
     * @return $this
     */
    public function setLanguage($language_code = "en-gb")
    {
        $this->language = strtolower($language_code);
        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set direction
     *
     * @param string $direction
     * @return $this
     */
    public function setDirection($direction = "ltr")
    {
        $this->direction = strtolower($direction);
        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * To string Object - Magic method
     *
     * @return string
     */
    public function __toString()
    {
        $DI = Di::getDefault();
        $config = $DI->get('config');
        $this->icon = BASE_URI . '/templates/frontend/' . $config->frontendTemplate->defaultTemplate . '/favicon.ico';

        if ($this->getTitle() == '') {
            $this->setTitle($config->website->siteName);
        } else {
            $this->setTitle($this->getTitle());
        }

        /**
         * Todo
         * Change in ZCMS before version. We need change language with User language
         */
        $this->setLanguage($config->website->language);

        if ($this->getDescription() == '') {
            $this->setDescription($config->website->metaDesc);
        }

        if ($this->getDirection() == '') {
            $this->setDirection($config->website->direction);
        }

        $canonical = $this->getLinkByRelType('canonical');
        if (!$canonical['href']) {
            $this->setLink('canonical', BASE_URI);
        }

        if ($this->getKeywords() == '') {
            $this->setKeywords($config->website->metaKey);
        }

        $header[] = "<meta charset=\"{$this->charset}\"/>";
        $header[] = "<title>{$this->title}</title>";
        $header[] = "<meta name=\"description\" content=\"{$this->description}\" />";
        $header[] = "<meta name=\"keywords\" content=\"{$this->keywords}\"/>";
        $header[] = '<link rel="shortcut icon" href="' . $this->icon . '" type="image/x-icon" />';

        //Render meta link
        foreach ($this->link as $link) {
            $header[] = $this->renderLink($link);
        }

        //Render meta tag
        foreach ($this->metaTags as $key => $metaTag) {
            $header[] = $this->renderMetaTag($key, $metaTag);
        }

        return implode("\n", $header);
    }

    /**
     * Render link
     *
     * @param array $link
     * @return string
     */
    private function renderLink($link)
    {
        $htmlTag = '<link href="' . $link['href'] . '" ' . 'rel="' . $link['rel'] . '" ';
        if ($link['attributeName'] != '' && $link['attributeTitle']) {
            $htmlTag .= ' ' . $link['attributeName'] . '="' . $link['attributeTitle'] . '" ';
        }
        $htmlTag .= '/>';
        return $htmlTag;
    }

    /**
     * Render meta tag
     *
     * @param string $key
     * @param string $metaTags
     * @return string
     */
    private function renderMetaTag($key, $metaTags)
    {
        $htmlTags = '';
        if (is_array($metaTags)) {
            foreach ($metaTags as $keyName => $metaTag) {
                $htmlTags[] = "<meta " . $key . "=\"{$keyName}\" content=\"{$metaTag}\" />";
            }
        } else {
            $htmlTags[] = "<meta name=\"{$key}\" content=\"{$metaTags}\" />";
        }

        return implode("\n", $htmlTags);
    }

}