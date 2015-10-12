<?php

namespace ZCMS\Backend\Menu\Controllers;

use Phalcon\Security;
use ZCMS\Core\Utilities\ZImageHelper;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\MenuItems;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\Utilities\Images\ZImages;
use ZCMS\Backend\Menu\Forms\MenuItemForm;

/**
 * Class IndexController
 *
 * @package ZCMS\Backend\Admin\Controllers
 * @author ZCMS Team
 * @version 0.0.1
 */
class MenuItemController extends ZAdminController
{

    /**
     * Image folder upload
     *
     * @var string
     */
    const MENU_ITEM_IMAGE_FOLDER_UPLOAD = 'media/menu/origin/';
    const MENU_ITEM_IMAGE_THUMB_FOLDER_UPLOAD = 'media/menu/thumb/';

    /**
     * Thumb width
     *
     * @var int
     */
    public $thumbWidth = 200;

    /**
     * Thumb height
     *
     * @var int
     */
    public $thumbHeight = 0; // => auto

    /**
     * Primary key for this model
     *
     * @var string Model primary key
     */
    public $_modelPrimaryKey = 'menu_item_id';


    /**
     * Phalcon Model
     * @var string
     */
    public $_model = '\ZCMS\Core\Models\MenuItems';

    /**
     * Model base name
     *
     * @var string
     */
    public $_modelBaseName = 'menu_items';

    /**
     * Index Action
     */
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addNewButton();
        $this->_toolbar->addDeleteButton();

        //Add filter
        $this->addFilter('filter_order', 'name', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');

        //Get all filter
        $filter = $this->getFilter();

        $conditions = [];

        $conditions[] = 'menu_item_id > 0';

        $items = MenuItems::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $paginationLimit = $this->config->pagination->limit;

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);

        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'menu_item_id'
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'image',
                'title' => 'm_menu_form_menu_item_thumbnail',
                'class' => 'col-id text-center',
                'column' => 'thumbnail',
                'width' => '80',
                'height' => 'auto',
                'uri_prefix' => BASE_URI . '/',
                'default_thumbnail' => '/media/default/no-image.png'
            ],
            [
                'type' => 'link',
                'title' => 'm_menu_form_menu_item_name',
                'column' => 'name',
                'link' => '/admin/menu/menuitem/edit/',
                'access' => $this->acl->isAllowed('menu|menuitem|edit'),
            ],
            [
                'type' => 'text',
                'title' => 'm_menu_form_menu_item_link',
                'column' => 'link'
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'column' => 'published',
                'link' => '/admin/menu/menuitem/',
                'access' => $this->acl->isAllowed('menu|menuitem|index')
            ],
            [
                'type' => 'date',
                'title' => 'gb_created_at',
                'column' => 'created_at',
            ],
            [
                'type' => 'date',
                'title' => 'gb_updated_at',
                'column' => 'updated_at',
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'menu_item_id',
            ]
        ]);

    }

    /**
     * New menu item
     *
     * @return bool|null
     */
    public function newAction()
    {
        //Add Resource
        $this->addJssCss();

        //Add toolbar button
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('menu|menuitem|index');
        $menuItem = new MenuItems();
        $this->view->setVar('item', $menuItem);
        $form = new MenuItemForm();
        $this->view->setVar('form', $form);
        if ($this->request->isPost()) {
            if ($form->isValid($_POST)) {

                $form->bind($_POST, $menuItem);
                if (!$menuItem->save()) {
                    foreach ($menuItem->getMessages() as $mgs) {
                        $this->flashSession->error($mgs);
                    }
                } else {
                    $imageStatus = ZImageHelper::uploadImages($this->request->getUploadedFiles(), self::MENU_ITEM_IMAGE_FOLDER_UPLOAD, 'thumbnail');
                    if ($imageStatus['status']) {
                        $menuItem->image = $imageStatus['imageUrl'];
                        $menuItem->thumbnail = $this->_createThumbnailImage($imageStatus['imageUrl'], self::MENU_ITEM_IMAGE_THUMB_FOLDER_UPLOAD, $imageStatus['imageName']);
                    } else {
                        if ($imageStatus['message']) {
                            $this->flashSession->notice($imageStatus['message']);
                        }
                        if ($imageStatus['bugMessage']) {
                            $this->flashSession->notice($imageStatus['bugMessage']);
                        }
                    }
                    $menuItem->save();
                    $this->flashSession->success('New menu item was created successfully');
                    $this->response->redirect('/admin/menu/menuitem');
                    return true;
                }
            } else {
                $this->flashSession->error('gb_please_check_required_filed');
            }
        }
        return null;
    }

    /**
     * Edit menu item
     *
     * @param int $id
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function editAction($id)
    {
        //Add Resource
        $this->addJssCss();

        //Add toolbar button
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        /**
         * @var MenuItems $menuItem
         */
        $menuItem = MenuItems::findFirst($id);
        $this->view->setVar('item', $menuItem);
        if ($menuItem) {
            $form = new MenuItemForm($menuItem);
            $this->view->setVar('form', $form);
            if ($this->request->isPost()) {
                if ($form->isValid($_POST)) {
                    $form->bind($_POST, $menuItem);
                    if (!$menuItem->save()) {
                        foreach ($menuItem->getMessages() as $mgs) {
                            $this->flashSession->error($mgs);
                        }
                    } else {
                        $imageStatus = ZImageHelper::uploadImages($this->request->getUploadedFiles(), self::MENU_ITEM_IMAGE_FOLDER_UPLOAD, 'thumbnail', null, $menuItem->image);
                        if ($imageStatus['status']) {
                            $menuItem->image = $imageStatus['imageUrl'];
                            $menuItem->thumbnail = $this->_createThumbnailImage($imageStatus['imageUrl'], self::MENU_ITEM_IMAGE_THUMB_FOLDER_UPLOAD, $imageStatus['imageName'], $menuItem->thumbnail);
                        } else {
                            if ($imageStatus['message']) {
                                $this->flashSession->notice($imageStatus['message']);
                            }
                            if ($imageStatus['bugMessage']) {
                                $this->flashSession->notice($imageStatus['bugMessage']);
                            }
                        }
                        $menuItem->save();
                        $this->flashSession->success('m_menu_message_update_menu_item_successfully');
                        return $this->response->redirect('/admin/menu/menuitem/');
                    }
                } else {
                    $this->flashSession->error('gb_please_check_required_filed');
                }
            }
            $this->view->pick('menuitem/new');
        } else {
            return $this->response->redirect('/admin/menuitem/');
        }

        return true;
    }

    /**
     * Delete menu item
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function deleteAction()
    {
        $ids = $this->request->getPost('ids');
        ZArrayHelper::toInteger($ids);

        if (count($ids)) {
            $query = 'DELETE FROM menu_details WHERE menu_item_id IN (' . implode(',', $ids) . ')';
            $this->db->execute($query);
            $query = 'DELETE FROM menu_items WHERE menu_item_id IN (' . implode(',', $ids) . ')';
            $this->db->execute($query);
            $this->flashSession->success(__($this->_getPrefixMessage() . 'message_items_successfully_unpublished', ['1' => $this->db->affectedRows()]));
        }
        return $this->response->redirect('/admin/menu/menuitem/');
    }

    /**
     * Add Image
     *
     * @param string $file
     * @param string $thumbDir
     * @param string $thumbnailName
     * @param string $oldFile
     * @return string
     */
    private function _createThumbnailImage($file, $thumbDir, $thumbnailName, $oldFile = null)
    {
        if ($oldFile) {
            @unlink(ROOT_PATH . '/public' . $oldFile);
        }
        $file = trim($file, '/');
        if (file_exists($file) && !is_dir($file) && is_dir($thumbDir)) {
            $handle = new ZImages($file);
            if ($handle->uploaded) {
                $handle->file_dst_name = $thumbnailName;
                $handle->image_resize = true;
                $handle->image_x = $this->thumbWidth;
                if ($this->thumbHeight == 0) {
                    $handle->image_ratio_y = true;
                } else {
                    $handle->image_y = $this->thumbHeight;
                }

                $handle->file_overwrite = true;
                $handle->process($thumbDir);
                if ($handle->processed) {
                    return DS . $handle->file_dst_pathname;
                } else {
                    return '';
                }
            }
        }
        return '';
    }

    /**
     * Ajax
     */
    public function ajaxRequestAction()
    {
        if ($this->request->isAjax()) {
            //Add filter
            $this->addFilter('filter_order', 'name', 'string');
            $this->addFilter('filter_order_dir', 'ASC', 'string');

            //Get all filter
            $filter = $this->getFilter();

            $conditions = [];

            $conditions[] = 'id > 0';

            $bind = [];
            foreach ($_POST as $key => $value) {
                $conditions[] = '{$key} = ?{($index++)}';
                $bind[] = $value;
            }

            $items = MenuItems::find([
                'conditions' => implode(' AND ', $conditions),
                'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
                'bind' => $bind
            ]);

            $currentPage = $this->request->getQuery('page', 'int', 1);
            $paginationLimit = $this->config->pagination->limit;

            //Create pagination
            $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

            //Set search value
            $this->view->setVar('_filter', $filter);
            //Set column name, value
            $this->view->setVar('_pageLayout', [
                [
                    'type' => 'check_all',
                ],
                [
                    'type' => 'check_box',
                    'title' => '#',
                ],
                [
                    'type' => 'default',
                    'title' => 'm_menu_form_menu_item_name',
                    'column' => 'name',
                    'display' => 'edit',
                    'link' => '/admin/menu/menuitem/edit/',
                    'access' => $this->acl->isAllowed('menu|menuitem|edit'),
                ],
                [
                    'type' => 'default',
                    'title' => 'gb_published',
                    'class' => 'text-center col-published',
                    'column' => 'published',
                    'link' => '/admin/menu/menuitem/',
                    'display' => 'published',
                    'access' => $this->acl->isAllowed('menu|menuitem|index')
                ],
                [
                    'type' => 'default',
                    'title' => 'gb_created_at',
                    'column' => 'created_at',
                    'display' => 'date',
                    'class' => 'text-center col-date'
                ],
                [
                    'type' => 'default',
                    'title' => 'gb_updated_at',
                    'column' => 'updated_at',
                    'display' => 'text',
                    'class' => 'text-center col-date'
                ],
                [
                    'type' => 'default',
                    'title' => 'gb_id',
                    'column' => 'menu_item_id'
                ]
            ]);
        }
    }

    /**
     * Add CSS
     */
    private function addJssCss()
    {
        //Add Resource
        $this->assets->collection('css_header')
            ->addCss('/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css');

        $this->assets->collection('js_footer')
            ->addJs('/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js');
    }

    /**
     * Reload menu when change domain name
     */
    public function reloadMenuAction()
    {
        $menuItems = MenuItems::find();
        foreach ($menuItems as $item) {
            /**
             * @var MenuItems $item
             */
            $item->save();
        }
    }
}
