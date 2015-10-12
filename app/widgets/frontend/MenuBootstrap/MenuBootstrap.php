<?php

use Phalcon\Tag;
use ZCMS\Core\ZWidget;
use ZCMS\Core\Cache\ZCache;
use ZCMS\Core\Models\MenuTypes;
use ZCMS\Core\Models\Users;

/**
 * Class MenuBootstrap_Widget
 */
class MenuBootstrap_Widget extends ZWidget
{
    const CACHE_MENU_BOOTSTRAP_WIDGET = 'CACHE_MENU_BOOTSTRAP_WIDGET';

    /**
     * @param int $id
     * @param array $widgetInfo
     * @param array $options
     */
    public function __construct($id = null, $widgetInfo = null, $options = null)
    {
        $options = [
            'title' => '',
            'menu_left' => '0',
            'menu_right' => '0',
        ];
        parent::__construct($id, $widgetInfo, $options);
    }

    /**
     * Admin widget form
     *
     * @return string
     */
    public function form()
    {
        $menu_left = isset($this->options->menu_left) ? $this->options->menu_left : '';
        $menu_right = isset($this->options->menu_right) ? $this->options->menu_right : '';
        $title = isset($this->options->title) ? $this->options->title : '';

        $form = '<p><label for="' . $this->getFieldId('title') . '">' . __('gb_title') . '</label>';
        $form .= Tag::textField([
            $this->getFieldName('title'),
            'class' => 'form-control input-sm',
            'value' => $title
        ]);
        $form .= '</p>';

        $menuTypeAvailable = MenuTypes::find([
            'order' => 'name ASC'
        ]);
        $form .= '<p><label for="' . $this->getFieldId('menu_left') . '">' . __('w_menu_form_label_select_menu_left') . '</label>';
        $form .= Tag::select([
            $this->getFieldName('menu_left'),
            $menuTypeAvailable,
            'using' => ['menu_type_id', 'name'],
            'class' => 'form-control input-sm',
            'value' => $menu_left,
            'usingEmpty' => true
        ]);
        $form .= '</p>';

        $form .= '<p><label for="' . $this->getFieldId('menu_left') . '">' . __('w_menu_form_label_select_menu_right') . '</label>';
        $form .= Tag::select([
            $this->getFieldName('menu_right'),
            $menuTypeAvailable,
            'using' => ['menu_type_id', 'name'],
            'class' => 'form-control input-sm',
            'value' => $menu_right,
            'usingEmpty' => true
        ]);
        $form .= '</p>';
        return $form;
    }

    /**
     * Front end html
     * @return string
     */
    public function widget()
    {
        $menu_items_left = [];
        $menu_items_right = [];

        if (Users::isLoggedIn()) {
            $isLogin = true;
        } else {
            $isLogin = false;
        }

        if (isset($this->options->menu_left) && $this->options->menu_left != null) {
            $menu_items_left = $this->_getMenu($this->options->menu_left, $isLogin);
        }
        if (isset($this->options->menu_right) && $this->options->menu_right != null) {
            $menu_items_right = $this->_getMenu($this->options->menu_right, $isLogin);
        }

        $this->view->setVar('menu_items_left', $menu_items_left);
        $this->view->setVar('menu_items_right', $menu_items_right);
    }

    /**
     * Get menu
     *
     * @param int $menuTypeId
     * @param bool $isLogin
     * @return array
     */
    private function _getMenu($menuTypeId, $isLogin)
    {
        if ($isLogin) {
            $key = self::CACHE_MENU_BOOTSTRAP_WIDGET . '_login_' . $menuTypeId;
        } else {
            $key = self::CACHE_MENU_BOOTSTRAP_WIDGET . '_not_login' . $menuTypeId;
        }

        $cache = ZCache::getInstance();
        $menu = $cache->get($key);
        if ($menu === null) {
            $builder = new Phalcon\Mvc\Model\Query\Builder();
            $builder->columns('mi.menu_item_id AS id, mi.name, mi.full_link AS link, mi.thumbnail, md.parent_id, require_login, mi.icon, mi.class')
                ->addFrom('ZCMS\Core\Models\MenuItems', 'mi')
                ->innerJoin('ZCMS\Core\Models\MenuDetails', 'mi.menu_item_id = md.menu_item_id', 'md')
                ->innerJoin('ZCMS\Core\Models\MenuTypes', 'md.menu_type_id = mt.menu_type_id', 'mt')
                ->where('mi.published = 1 AND md.menu_type_id = ?0', [$menuTypeId])
                ->orderBy('ordering ASC');
            $menu_items = $builder->getQuery()->execute()->toArray();
            if (count($menu_items)) {
                $menu = $this->_repaidMenuItems($menu_items, $isLogin);
                $cache->save($key, $menu);
                return $menu;
            }
            return [];
        } else {
            return $menu;
        }
    }

    /**
     * Repaid menu items
     *
     * @param array $menuItems
     * @param int $parent
     * @param bool $isLogin
     * @return array
     */
    private function _repaidMenuItems($menuItems, $isLogin, $parent = 0)
    {
        $result = [];
        foreach ($menuItems as $item) {
            if ($item['icon'] != null) {
                $item['icon'] = '<i class="' . $item['icon'] . '"></i> ';
            }
            if ($item['parent_id'] == $parent) {
                $item['children'] = [];
                if ($item['require_login'] == 0 || ($isLogin && $item['require_login'] == 1) || (!$isLogin && $item['require_login'] == -1)) {
                    $result[] = $item;
                    $result[count($result) - 1]['children'] = $this->_repaidMenuItems($menuItems, $isLogin, $item['id']);
                }
            }
        }
        return $result;
    }
}

register_widget('MenuBootstrap_Widget');