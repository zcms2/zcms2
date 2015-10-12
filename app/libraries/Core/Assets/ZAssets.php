<?php

namespace ZCMS\Core\Assets;

use Phalcon\Assets\Manager as PManager;

/**
 * Class ZAssets
 *
 * @package ZCMS\Core\Assets
 * @author ZCMS Team
 */
class ZAssets extends PManager
{
    /**
     * @var array
     */
    public $cssDeclaration = [];

    /**
     * @var array
     */
    public $jsDeclaration = [];

    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->_collections = [];
    }

    /**
     * Add string Css
     *
     * @param string $str
     * @param string $type
     */
    public function addCssDeclaration($str, $type = 'text/css')
    {
        $this->cssDeclaration[] = "<style type=\"{$type}\">" . $str . '</style>';
    }

    /**
     * Print output Css
     *
     * @param string $name
     */
    public function outputCss($name = null)
    {
        parent::outputCss($name);
        if ($name == null) {
            echo implode("", $this->cssDeclaration);
        } else {
            if (method_exists($this->_collections["{$name}"], "outputCss")) {
                $this->_collections["{$name}"]->outputCss();
            }
        }
    }

    /**
     * Add string Js
     *
     * @param string $str
     * @param string $type
     */
    public function addJsDeclaration($str, $type = "text/javascript")
    {
        $this->jsDeclaration[] = "<script type=\"{$type}\">" . $str . "</script>";
    }

    /**
     * Print output Js
     *
     * @param string $name
     */
    public function outputJs($name = null)
    {
        parent::outputJs($name);
        if ($name == null) {
            echo implode("", $this->jsDeclaration);
        } else {
            if (method_exists($this->_collections["{$name}"], "outputJs")) {
                $this->_collections["{$name}"]->outputJs();
            }
        }
    }

    /**
     * Creates/Returns a collection of resources
     *
     * @overwrite \Phalcon\Assets\Collection->collection
     *
     * @param string $name
     * @return \Phalcon\Assets\Collection
     */
    public function collection($name)
    {
        if (!isset($this->_collections["{$name}"])) {
            $this->_collections["{$name}"] = new ZCollection();
        }
        return $this->_collections["{$name}"];
    }
}