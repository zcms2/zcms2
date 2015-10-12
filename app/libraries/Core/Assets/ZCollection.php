<?php

namespace ZCMS\Core\Assets;

use Phalcon\Assets\Collection as PCollection;

/**
 * Class ZCollection
 *
 * @package ZCMS\Core\Assets
 * @author ZCMS Team
 */
class ZCollection extends PCollection
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
     * @param string $str
     * @param string $type
     */
    public function addCssDeclaration($str, $type = 'text/css')
    {
        $this->cssDeclaration[] = "<style type=\"{$type}\">" . $str . '</style>';
    }

    /**
     * @param string $str
     * @param string $type
     */
    public function addJsDeclaration($str, $type = 'text/javascript')
    {
        $this->jsDeclaration[] = "<script type=\"{$type}\">" . $str . "</script>";
    }

    /**
     * Print output Js
     */
    public function outputJs()
    {
        echo implode('', $this->jsDeclaration);
    }

    /**
     * Print output Css
     */
    public function outputCss()
    {
        echo implode('', $this->cssDeclaration);
    }
}