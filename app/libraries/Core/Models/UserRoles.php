<?php

namespace ZCMS\Core\Models;

use Phalcon\Db;
use Phalcon\Di as PDI;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\ZModel;

/**
 * Class AdminRole
 * @package ZCMS\Core\Models
 */
class UserRoles extends ZModel
{
    /**
     *
     * @var integer
     */
    public $role_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $is_super_admin;

    /**
     *
     * @var string
     */
    public $menu;

    /**
     *
     * @var string
     */
    public $acl;

    /**
     *
     * @var integer
     */
    public $location;

    /**
     *
     * @var integer
     */
    public $is_default;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    /**
     * Check item edit is is_super_admin
     *
     * @return bool
     */
    public function beforeSave()
    {
        //Cannot edit role id == 1 because Supper Administrator access all permission
        $auth = Users::getCurrentUser();
        if ($this->is_super_admin == 1 && $auth['is_super_admin'] != 1) {
            return false;
        }
        return true;
    }

    /**
     * Check url
     *
     * @param string $url
     * @return string
     */
    public static function checkUrl($url)
    {
        if ($url != "#") {
            if (strpos($url, "http://") === false || strpos($url, "https://" === false)) {
//               $url = BASE_URI . $url;
            }
        }
        return $url;
    }

    /**
     * @param int|array $ids
     * @return bool
     */
    public static function updateModuleMenu($ids = null)
    {
        if ($ids != null) {
            if (is_array($ids)) {
                ZArrayHelper::toInteger($ids);
            } else {
                $id = intval($ids);
                $ids = [];
                $ids[] = $id;
            }
        }

        $menus = CoreModules::find([
            'conditions' => 'menu != "" AND location = "backend" AND published = 1 AND base_name NOT IN("admin","system","template")',
            'order' => 'ordering ASC'
        ])->toArray();;
        if (!count($menus)) {
            $menus = [];
        }

        $menuAdmin = CoreModules::findFirst("base_name = 'admin'");
        $menuTemplate = CoreModules::findFirst("base_name = 'template'");
        $menuSystem = CoreModules::findFirst("base_name = 'system'");

        if ($menuTemplate) array_unshift($menus, $menuAdmin->toArray());
        if ($menuTemplate) $menus[] = $menuTemplate->toArray();
        if ($menuSystem) $menus[] = $menuSystem->toArray();

        $menusAll = [];
        foreach ($menus as $index => $menu) {
            $menusAll[] = unserialize($menu['menu']);
        }

        $newMenuAll = [];

        foreach ($menusAll as $menu) {
            if (isset($menu['link'])) {
                $menu['link'] = self::checkUrl($menu['link']);
                $tmp = $menu;
                $tmp['items'] = [];
                if (isset($menu['items']) && count($menu['items'])) {
                    foreach ($menu['items'] as $index1 => $item) {
                        if (isset($item['link'])) {
                            $item['link'] = self::checkUrl($item['link']);
                            $tmp['items'][$index1] = $item;
                            if (isset($item['items']) && count($item['items'])) {
                                foreach ($item['items'] as $index2 => $childItem) {
                                    $childItem['link'] = self::checkUrl($childItem['link']);
                                    $tmp['items'][$index1]['items'][$index2] = $childItem;
                                }
                            }
                        }
                    }
                }
                $newMenuAll[] = $tmp;
            }
        }

        $menusAll = $newMenuAll;

        if (count($ids)) {
            /**
             * @var UserRoles[] $roles
             */
            $roles = UserRoles::find('id IN (' . implode(',', $ids) . ')');
        } else {
            $roles = UserRoles::find();
        }

        $menuForRole = [];
        foreach ($roles as $role) {
            if ($role->role_id != 1) {
                $rules = UserRoles::getRules($role->role_id);
                if (count($rules)) {
                    $menuTMPArray = [];
                    foreach ($menusAll as $menu) {
                        if ($menu['rule'] == "" || self::checkRuleMenuInRole($menu['rule'], $rules)) {
                            $menuTMP = [
                                'menu_name' => $menu['menu_name'],
                                'module' => $menu['module'],
                                'link' => $menu['link'],
                                'rule' => $menu['rule'],
                                'link_class' => $menu['link_class'],
                                'icon_class' => $menu['icon_class'],
                                'link_target' => $menu['link_target'],
                            ];
                            $menuTMP['items'] = [];
                            if (isset($menu['items']) && count($menu['items'])) {
                                foreach ($menu['items'] as $index1 => $item) {

                                    if ($item['rule'] == "" || self::checkRuleMenuInRole($item['rule'], $rules)) {
                                        $childItem = [];
                                        if (isset($item['items'])) {
                                            $childItem = $item['items'];
                                        }

                                        $item['items'] = [];

                                        $menuTMP['items'][$index1] = $item;
                                        if (count($childItem)) {
                                            foreach ($childItem as $cItem) {
                                                if ($cItem['rule'] == "" || self::checkRuleMenuInRole($cItem['rule'], $rules)) {
                                                    $menuTMP['items'][$index1]['items'][] = $cItem;
                                                }
                                            }

                                        }

                                        if (!count($menuTMP['items'][$index1]['items']) && ($menuTMP['items'][$index1]['link'] == "" || $menuTMP['items'][$index1]['link'] == "#") && $menuTMP['items'][$index1]["role"] == "") {
                                            unset($menuTMP['items'][$index1]);
                                        }
                                    }
                                }
                            }
                            if (!count($menuTMP['items']) && ($menuTMP['link'] == "" || $menuTMP['link'] == "#") && $menuTMP["rule"] == "") {

                            } else {
                                $menuTMPArray[] = $menuTMP;
                            }
                        }
                    }
                    $menuForRole[$role->role_id] = $menuTMPArray;
                } else {
                    $menuForRole[$role->role_id] = null;
                }
            } else {
                $menuForRole[$role->role_id] = $menusAll;
            }
        }

        foreach ($roles as $role) {
            $role->menu = serialize($menuForRole[$role->role_id]);
            if (!$role->save()) {
                //Do something
            }
        }
        return true;
    }

    /**
     * Get rules with role_id
     *
     * @param int $roleId
     * @return array
     */
    public static function getRules($roleId)
    {
        $query = 'SELECT ar.mca FROM user_rules AS ar
                      INNER JOIN user_role_mapping AS arm ON arm.rule_id = ar.rule_id
                      WHERE arm.role_id = ' . $roleId;
        /**
         * @var \Phalcon\Db\Adapter\Pdo\Postgresql $db
         */
        $db = PDI::getDefault()->get('db');
        $rules = $db->fetchAll($query, Db::FETCH_ASSOC);
        $rules = array_column($rules, 'mca');
        return $rules;
    }

    /**
     * Check rule menu in role
     *
     * @param string $roleMenu
     * @param array $rules
     * @return bool
     */
    public static function checkRuleMenuInRole($roleMenu, $rules)
    {


        if (substr_count($roleMenu, '|') == 1) {
            $roleMenu .= "|";
        }

        foreach ($rules as $rule) {
            if (strpos($rule, $roleMenu) !== false) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get default customer role
     *
     * @return UserRoles
     */
    public static function getDefaultCustomerRole(){
        return self::findFirst([
            'conditions' => 'location = 0 AND is_default =1'
        ]);
    }

    /**
     * Get default customer role ID
     *
     * @return integer
     */
    public static function getDefaultCustomerRoleID(){
        $role = self::getDefaultCustomerRole();
        if($role){
            return $role->role_id;
        }
        return 0;
    }
}
