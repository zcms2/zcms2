<?php

namespace ZCMS\Core\Utilities;

use Phalcon\Di as PDI;
use ZCMS\Core\Plugins\ZAcl;

/**
 * Class helper coder add button action in admin view
 *
 * @package   ZCMS\Core
 * @since     0.0.1
 */
class ZToolbarHelper
{
    /**
     * @var ZToolbarHelper
     */
    public static $instance;

    /**
     * @var string Website <title>
     */
    public $title = [];

    /**
     * @var array
     */
    public $breadcrumb = [];

    /**
     * @var string
     */
    public $headerPrimary = '';

    /**
     * @var string
     */
    public $headerSecond = '';

    /**
     * @var array
     */
    public $buttons = [];

    /**
     * @var array
     */
    public $html_filter = [];

    /**
     * @var \ZCMS\Core\Plugins\ZAcl
     */
    public $security;

    /**
     * @var string
     */
    public $moduleName;

    /**
     * @var string
     */
    public $controllerName;

    /**
     * Get instance object
     *
     * @param string $moduleName Module base name
     * @param string $controllerName Controller base name
     * @return ZToolbarHelper
     */
    public static function getInstance($moduleName, $controllerName)
    {
        if (!is_object(self::$instance)) {
            self::$instance = new ZToolbarHelper($moduleName, $controllerName);
        }
        return self::$instance;
    }

    /**
     * Instance construct
     *
     * @param string $moduleName Module base name
     * @param string $controllerName Controller base name
     */
    public function __construct($moduleName, $controllerName)
    {
        $this->moduleName = $moduleName;
        $this->controllerName = $controllerName;

        $this->security = ZAcl::getInstance();
        $this->breadcrumb[] = [
            'title' => __('gb_admin_home'),
            'link' => '',
            'icon_class' => 'fa fa-home',
            'active' => false
        ];
    }

    /**
     * Add Html Filter
     *
     * @param string $html
     */
    public function addHtmlFilter($html = '')
    {
        $this->html_filter[] = $html;
    }

    /**
     * Render html filter
     *
     * @return string
     */
    public function renderHtmlFilter()
    {
        return implode('', $this->html_filter);
    }

    /**
     * Add breadcrumb
     *
     * @param array|string $option ['title' => val, ['link' => val, 'icon_class' = val ]];
     * @param bool $active
     * @return bool
     */
    public function addBreadcrumb($option, $active = true)
    {
        if (is_string($option)) {
            $option = ['title' => $option];
        }
        //Check link
        if (isset($option['title'])) {
            $translation = array_key_exists('translation', $option);
            if ($translation || ($translation && $option['translation'] == true)) {
                $option['title'] = __($option['title']);
            }
        } else {
            return false;
        }

        //Check link
        if (!isset($option['link']) || !$this->security->isAllowedLink($option['link'])) {
            $option['link'] = '';
        }

        //Check icon_class
        if (!isset($option['icon_class'])) {
            $option['icon_class'] = '#';
        }

        //Add active
        $option['active'] = $active;

        $this->breadcrumb[] = $option;
        return true;
    }

    /**
     * Get breadcrumb
     *
     * @return array
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * Set title <title></title>
     *
     * @param string $str
     */
    public function addTitle($str = 'gb_zcms_dashboard')
    {
        $this->title[] = __($str);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        if (count($this->title) > 0) {
            return strip_tags(implode(' - ', $this->title));
        } elseif (strlen($this->headerPrimary) > 0 || strlen($this->headerSecond) > 0) {
            return strip_tags($this->headerPrimary . ($this->headerSecond != '' ? ' - ' . $this->headerSecond : ''));
        }
        return __('gb_zcms_dashboard');
    }

    /**
     * Get header primary
     *
     * @return string
     */
    public function getHeaderPrimary()
    {
        return $this->headerPrimary;
    }

    /**
     * Get header second
     *
     * @return string
     */
    public function getHeaderSecond()
    {
        return $this->headerSecond;
    }

    /**
     * Get Html button
     *
     * @return string
     */
    public function getButtons()
    {
        return implode('', $this->buttons);
    }

    /**
     * Add header primary
     *
     * @param null $str
     */
    public function addHeaderPrimary($str = null)
    {
        $this->headerPrimary = __($str);
    }

    /**
     * Add header second
     *
     * @param null $str
     */
    public function addHeaderSecond($str = null)
    {
        $this->headerSecond = __($str);
    }

    /**
     * Helper add new button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param null $onClickEvent
     */
    public function addNewButton($rule = 'new', $buttonLink = null, $buttonName = 'gb_add_new', $buttonIconClass = 'glyphicon glyphicon-plus', $buttonTypeClass = 'btn btn-success', $onClickEvent = null)
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add delete button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addDeleteButton($rule = 'delete', $buttonLink = null, $buttonName = 'gb_delete', $buttonIconClass = 'glyphicon glyphicon-remove', $buttonTypeClass = 'btn btn-danger delete', $onClickEvent = 'return ZCMS.deleteSubmit(this);')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add trash button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addTrashButton($rule, $buttonLink = null, $buttonName = 'gb_trash', $buttonIconClass = 'glyphicon glyphicon-trash', $buttonTypeClass = 'btn btn-danger delete', $onClickEvent = 'return ZCMS.trashSubmit(this);')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add published button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addPublishedButton($rule = 'publish', $buttonLink = null, $buttonName = 'gb_published', $buttonIconClass = 'glyphicon glyphicon-check', $buttonTypeClass = 'btn btn-success btn-green', $onClickEvent = 'return ZCMS.publishedSubmit(this);')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add unpublished button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addUnPublishedButton($rule = 'unPublish', $buttonLink = null, $buttonName = 'gb_unpublished', $buttonIconClass = 'glyphicon glyphicon-share', $buttonTypeClass = 'btn btn-warning btn-orange', $onClickEvent = 'return ZCMS.unPublishedSubmit(this);')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add edit button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addEditButton($rule = 'edit', $buttonLink = null, $buttonName = 'gb_edit', $buttonIconClass = 'glyphicon glyphicon-edit', $buttonTypeClass = 'btn btn-primary', $onClickEvent = 'return ZCMS.editButtonSubmit(this);')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add save button
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addSaveButton($rule = '', $buttonLink = null, $buttonName = 'gb_save', $buttonIconClass = 'glyphicon glyphicon-floppy-saved', $buttonTypeClass = 'btn btn-primary', $onClickEvent = 'return ZCMS.submitForm();')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add save and edit button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addSaveAndEditButton($rule, $buttonLink = null, $buttonName = 'gb_save', $buttonIconClass = 'glyphicon glyphicon-floppy-saved', $buttonTypeClass = 'btn btn-primary', $onClickEvent = "eturn ZCMS.saveAndEditForm('save_and_edit');")
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add update button
     *
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     */
    public function addUpdateButton($rule = 'update', $buttonLink = null, $buttonName = 'gb_update', $buttonIconClass = 'glyphicon glyphicon-cloud-download', $buttonTypeClass = 'btn btn-primary')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass);
        }
    }

    /**
     * @param string $rule
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addCancelButton($rule, $buttonLink = null, $buttonName = 'gb_cancel', $buttonIconClass = 'glyphicon glyphicon-ban-circle', $buttonTypeClass = 'btn btn-warning', $onClickEvent = '')
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * Add black button
     *
     * @param string $buttonLink
     * @param string $buttonName
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     * @param string $buttonIconClassDir
     */
    public function addBackButton($buttonLink = '#', $buttonName = 'gb_back', $buttonIconClass = 'glyphicon glyphicon-circle-arrow-left', $buttonTypeClass = 'btn btn-purple', $onClickEvent = 'javascript:history.go(-1); return false;', $buttonIconClassDir = 'left')
    {
        if ($buttonLink != '#') {
            $this->buttons[] = $this->renderButton(null, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, null, $buttonIconClassDir);
        } else {
            $this->buttons[] = $this->renderButton(null, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent, $buttonIconClassDir);
        }
    }

    /**
     * Add custom button
     *
     * @param string $rule
     * @param string $buttonName
     * @param string $buttonLink
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     */
    public function addCustomButton($rule, $buttonName, $buttonLink, $buttonIconClass = null, $buttonTypeClass = 'btn', $onClickEvent = null)
    {
        if ($this->_isAllowed($rule)) {
            $this->buttons[] = $this->renderButton($rule, $buttonName, $buttonLink, $buttonIconClass, $buttonTypeClass, $onClickEvent);
        }
    }

    /**
     * @param string $rule
     * @param string $name
     * @param string $link
     * @param string $classLink
     * @param string $iconLink
     * @param string $title
     * @return string
     */
    public function renderEditLink($rule, $name, $link = "#", $classLink = "", $iconLink = "", $title = "Edit item")
    {

        if ($this->_isAllowed($rule)) {
            $html = '<a href="' . $link . '" title="' . $title . '" ';
            if ($classLink) {
                $html .= ' class="' . $classLink . '"';
            }
            $html .= " >";
            if ($iconLink) {
                $html .= '<i class="' . $iconLink . '"></i> ';
            }
            $html .= $name . '<a>';

            return $html;
        }
        return $title;
    }

    /**
     * Render published link
     *
     * @param string $rule
     * @param object $item
     * @param $link
     * @param array $title
     * @param string $iconLink
     * @return string
     */
    public function renderPublishedLink($rule, $item, $link, $title = [0 => "gb_unpublished_this_item", 1 => "gb_published_this_item"], $iconLink = "fa fa-check-circle")
    {
        if ($this->_isAllowed($rule)) {
            if ($item->published == 1) {
                $title = __($title[1]);
                $html = '<a href="' . $link . '/unpublished/' . $item->id . '" title="' . $title . '"><i class="' . $iconLink . ' green"></i></a>';
            } else {
                $title = __($title[0]);
                $html = '<a href="' . $link . '/published/' . $item->id . '" title="' . $title . '"><i class="' . $iconLink . ' red"></i></a>';
            }
        } else {
            if ($item->published == 1) {
                $html = '<i class="' . $iconLink . ' green"></i>';
            } else {
                $html = '<i class="' . $iconLink . ' red"></i>';
            }
        }
        return $html;
    }

    /**
     * Render admin button html
     *
     * @param string $rule
     * @param string $buttonName
     * @param string $buttonLink
     * @param string $buttonIconClass
     * @param string $buttonTypeClass
     * @param string $onClickEvent
     * @param string $buttonIconClassDir
     * @return string
     */
    public function renderButton($rule, $buttonName, $buttonLink, $buttonIconClass = null, $buttonTypeClass = null, $onClickEvent = null, $buttonIconClassDir = "left")
    {
        $buttonLink = $this->_generateLink($rule, $buttonLink);
        //Validate button link
        if (!filter_var($buttonLink, FILTER_VALIDATE_URL) && $buttonLink != "#" && $buttonLink != '') {
            $buttonLink = BASE_URI . $buttonLink;
        }

        $html = '';
        //Add href
        if ($buttonLink == '') {
            $html .= '<a href="#" ';
        } else {
            $html .= "<a href=\"" . $buttonLink . "\"";
        }


        //Add event onclick
        if (strlen($onClickEvent) > 0) {
            $html .= " onclick=\"" . $onClickEvent . "\"";
        }

        $html .= ' class="' . $buttonTypeClass . ' btn-sm"';

        $html .= ">";

        if ($buttonIconClassDir == "left") {
            if (strlen($buttonIconClass) > 0) {
                $html .= '<span class="' . $buttonIconClass . '"></span> ';
            }
            //Add translation button name
            $html .= __($buttonName);
        } else {
            //Add translation button name
            $html .= __($buttonName);
            if (strlen($buttonIconClass) > 0) {
                $html .= ' <span class="' . $buttonIconClass . '""></span>';
            }
        }
        $html .= '</a>';

        return $html;
    }

    /**
     * Rule allowed permission
     *
     * @param string $rule
     * @return bool
     */
    private function _isAllowed($rule)
    {
        if (strpos($rule, '|') && !empty($this->moduleName) && !empty($this->controllerName)) {
            return $this->security->isAllowed($this->moduleName . '|' . $this->controllerName . '|' . $rule);
        }
        return $this->security->isAllowed($rule);
    }

    /**
     * Auto generate button link
     *
     * @param string $rule
     * @param string $buttonLink
     * @return string
     */
    private function _generateLink($rule, $buttonLink)
    {
        if ($rule != null && $buttonLink == null && strpos($rule, '/') === false) {
            if (strpos($rule, '|') !== false) {
                return '/admin/' . str_replace('|', '/', $rule) . '/';
            } else {
                return '/admin/' . $this->moduleName . '/' . $this->controllerName . '/' . $rule . '/';
            }
        }
        return $buttonLink;
    }
}