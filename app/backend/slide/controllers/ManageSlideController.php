<?php

namespace ZCMS\Backend\Slide\Controllers;

use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\SlideShows;
use ZCMS\Core\Models\SlideShowItems;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\Utilities\ZImageHelper;
use ZCMS\Backend\Slide\Forms\SlideShowItemForm;

/**
 * Class ManageSlideController
 *
 * @package ZCMS\Backend\Slide\Controllers
 */
class ManageSlideController extends ZAdminController
{
    const SLIDE_SHOW_FOLDER_UPLOAD = 'media/slide-shows';

    /**
     * @var string PHQL Model
     */
    public $_model = 'SlideShowItems';

    /**
     * @var string Model name in database
     */
    public $_modelBaseName = 'slide_show_items';

    /**
     * Publish slide item
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     * @return \Phalcon\Http\ResponseInterface
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        $redirect = '/admin/slide/';
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }
        if (count($ids)) {
            /**
             * @var SlideShowItems $slideShow
             */
            $slideShow = SlideShowItems::findFirst([
                'conditions' => 'slide_show_item_id = ?0',
                'bind' => [$ids[0]]
            ]);

            if ($slideShow) {
                $redirect = '/admin/slide/manage-slide/slide/' . $slideShow->slide_show_id;
            }
        }
        parent::publishAction($id, $redirect, $log);
        return $this->response->redirect($redirect);
    }

    /**
     * UnPublish slide item
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     * @return \Phalcon\Http\ResponseInterface
     */
    public function unPublishAction($id = null, $redirect = null, $log = true)
    {
        $redirect = '/admin/slide/';
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }
        if (count($ids)) {
            /**
             * @var SlideShowItems $slideShow
             */
            $slideShow = SlideShowItems::findFirst([
                'conditions' => 'slide_show_item_id = ?0',
                'bind' => [$ids[0]]
            ]);

            if ($slideShow) {
                $redirect = '/admin/slide/manage-slide/slide/' . $slideShow->slide_show_id;
            }
        }
        parent::unPublishAction($id, $redirect, $log);
        return $this->response->redirect($redirect);
    }

    /**
     * List all slide show item(s) in 1 slide show
     *
     * @param $id
     * @return bool
     */
    public function slideAction($id)
    {
        $id = intval($id);
        /**
         * @var $slideShow SlideShows
         */
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$id]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addNewButton('new', '/admin/slide/manage-slide/new/' . $id . '/');
        $this->_toolbar->addDeleteButton('delete', '/admin/slide/manage-slide/delete/' . $id . '/');
        $this->_toolbar->addCancelButton('index', '/admin/slide/', 'Backward', 'fa fa-backward');

        //Add sorting
        $this->addFilter('filter_order', 'slide_show_item_id', 'string');
        $this->addFilter('filter_order_dir', 'DESC', 'string');
        $this->addFilter('filter_column_title', '', 'string');
        $this->addFilter('filter_column_title', '', 'string');
        $this->addFilter('filter_published', '', 'string');
        $this->addFilter('filter_id', '', 'int');


        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);

        $conditions = [];

        if ($filter['filter_column_title']) {
            $conditions[] = "title ILIKE '%" . htmlspecialchars($filter['filter_column_title']) . "%'";
        }

        if ($filter['filter_id']) {
            $conditions[] = "slide_show_item_id = " . intval($filter['filter_id']);
        }

        if ($filter['filter_published'] != '') {
            $conditions[] = "published = " . intval($filter['filter_published']);
        }

        $conditions[] = 'slide_show_id = ' . $id;

        $slides = SlideShowItems::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir']
        ]);

        $paginationLimit = $this->config->pagination->limit;
        $currentPage = $this->request->getQuery('page', 'int');

        $this->view->setVar('_page', ZPagination::getPaginationModel($slides, $paginationLimit, $currentPage));
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'slide_show_item_id'
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'image',
                'width' => '100',
                'height' => 'auto',
                'uri_prefix' => BASE_URI,
                'column' => 'image',
                'css' => 'width : 105px',
                'title' => 'Image',
                'class' => 'text-center',
                'default_thumbnail' => '/media/default/no-image.png'
            ],
            [
                'type' => 'link',
                'title' => 'Slide Item Name',
                'column' => 'title',
                'link' => '/admin/slide/manage-slide/edit/' . $id . '/',
                'access' => $this->acl->isAllowed('slide|manage-slide|edit'),
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_column_title',
                    'attributes' => []
                ]
            ],
            [
                'type' => 'actions',
                'title' => 'gb_ordering',
                'link_prefix' => 'slide_show_item_id',
                'class' => 'text-center',
                'column' => 'ordering',
                'display_value' => true,
                'actions' => [
                    [
                        'link_title' => 'gb_move_up',
                        'link' => '/admin/slide/manage-slide/moveUp/' . $id . '/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-up',
                        'access' => $this->acl->isAllowed('slide|manage-slide|edit'),
                    ],
                    [
                        'link_title' => 'gb_move_down',
                        'link' => '/admin/slide/manage-slide/moveDown/' . $id . '/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-down',
                        'access' => $this->acl->isAllowed('slide|manage-slide|edit'),
                    ]
                ]
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'access' => $this->acl->isAllowed('slide|manage-slide|edit'),
                'link' => '/admin/slide/manage-slide/',
                'column' => 'published',
                'filter' => [
                    'type' => 'select',
                    'name' => 'filter_published',
                    'attributes' => [
                        'useEmpty' => true,
                        'emptyText' => __('gb_all'),
                        'value' => $filter['filter_published'] == '' ? -1 : $filter['filter_published']
                    ],
                    'value' => [
                        0 => 'No',
                        1 => 'Yes',
                    ]
                ]
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'slide_show_item_id',
                'css' => 'width : 100px',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_id',
                    'attributes' => []
                ]
            ]
        ]);

        return true;
    }

    /**
     * Delete slide show item(s)
     *
     * @param int|null $slideShowID
     * @param string $redirect
     * @return \Phalcon\Http\ResponseInterface|void
     */
    public function deleteAction($slideShowID, $redirect = null)
    {
        $slideShowID = intval($slideShowID);
        /**
         * @var SlideShows $slideShow
         */
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$slideShowID]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        $ids = $this->request->getPost('ids');
        ZArrayHelper::toInteger($ids);

        foreach ($ids as $id) {
            $this->deleteSlideShowItem($id);
        }
        $this->flashSession->success('Delete slide show item(s) successfully!');
        if ($redirect) {
            return $redirect;
        }

        return $this->response->redirect('/admin/slide/manage-slide/slide/' . $slideShowID);
    }

    /**
     * Delete slide show item(s)
     *
     * @param $slideShowItemID
     * @return bool
     */
    private function deleteSlideShowItem($slideShowItemID)
    {
        /**
         * @var $slideShowItem SlideShowItems
         */
        $slideShowItem = SlideShowItems::findFirst($slideShowItemID);
        if ($slideShowItem) {
            $file = ROOT_PATH . '/public' . $slideShowItem->image;
            if (!is_dir($file) && file_exists($file) && strpos($file, ROOT_PATH . '/public/' . self::SLIDE_SHOW_FOLDER_UPLOAD) !== false) {
                unlink($file);
            }
            $slideShowItem->delete();
            return true;
        }
        return null;
    }

    /**
     * New slide show item
     *
     * @param $id
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function newAction($id)
    {
        $id = intval($id);
        /**
         * @var SlideShows $slideShow
         */
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$id]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        $this->_toolbar->addHeaderPrimary(__('m_admin_manage_slide_new_slide_item') . ': <i>' . $slideShow->title . '</i>');
        $this->_toolbar->addSaveButton('slide|manage-slide|new');
        $this->_toolbar->addCancelButton('slide|manage-slide|index', '/admin/slide/manage-slide/slide/' . $id . '/');
        $form = new SlideShowItemForm();
        $this->view->setVar('form', $form);

        if ($this->request->isPost()) {
            $slideShowItems = new SlideShowItems();
            if ($form->isValid($_POST, $slideShowItems)) {
                $slideShowItems->slide_show_id = $id;
                $ordering = SlideShowItems::maximum([
                    'column' => 'ordering',
                    'conditions' => 'slide_show_id = ' . $id,
                ]);
                $ordering++;
                $slideShowItems->ordering = $ordering;
                if ($slideShowItems->save()) {
                    $imageStatus = ZImageHelper::uploadImages($this->request->getUploadedFiles(), self::SLIDE_SHOW_FOLDER_UPLOAD . '/' . $slideShow->slide_show_id . '/slide-item', 'image');

                    if ($imageStatus['status']) {
                        $slideShowItems->image = $imageStatus['imageUrl'];
                        $slideShowItems->save();
                    } else {
                        if ($imageStatus['message']) {
                            $this->flashSession->notice($imageStatus['message']);
                        }
                        if ($imageStatus['bugMessage']) {
                            $this->flashSession->notice($imageStatus['bugMessage']);
                        }
                    }

                    $this->flashSession->success('Add new slide show successfully');
                    return $this->response->redirect('/admin/slide/manage-slide/slide/' . $id . '/');
                } else {
                    $this->setFlashSession($slideShowItems->getMessages(), 'notice');
                }
            }
        }
        return null;
    }

    /**
     * Edit slide show item
     *
     * @param $slideID
     * @param $slideItemID
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function editAction($slideID, $slideItemID)
    {
        $this->view->pick('manage-slide/new');
        $slideID = intval($slideID);
        /**
         * @var $slideShow SlideShows
         */
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$slideID]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        $slideItemID = intval($slideItemID);
        /**
         * @var $slideShowItems SlideShowItems
         */
        $slideShowItems = SlideShowItems::findFirst([
            'conditions' => 'slide_show_item_id = ?0',
            'bind' => [$slideItemID]
        ]);

        if (!$slideShowItems) {
            return $this->response->redirect('/admin/slide/');
        }
        $this->_toolbar->addHeaderPrimary(__('m_admin_manage_slide_edit_slide_item') . ': <i>' . $slideShow->title . '</i>');
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index', '/admin/slide/manage-slide/slide/' . $slideID . '/');
        $form = new SlideShowItemForm($slideShowItems);
        $this->view->setVar('slideShowItems', $slideShowItems);
        $this->view->setVar('form', $form);

        if ($this->request->isPost()) {
            if ($form->isValid($_POST, $slideShowItems)) {
                if ($slideShowItems->save()) {
                    $imageStatus = ZImageHelper::uploadImages($this->request->getUploadedFiles(), self::SLIDE_SHOW_FOLDER_UPLOAD . '/' . $slideShow->slide_show_id . '/slide-item', 'image', null, $slideShowItems->image);

                    if ($imageStatus['status']) {
                        $slideShowItems->image = $imageStatus['imageUrl'];
                        $slideShowItems->save();
                    } else {
                        if ($imageStatus['message']) {
                            $this->flashSession->notice($imageStatus['message']);
                        }
                        if ($imageStatus['bugMessage']) {
                            $this->flashSession->notice($imageStatus['bugMessage']);
                        }
                    }

                    $this->flashSession->success('Add new slide show successfully');
                    return $this->response->redirect('/admin/slide/manage-slide/edit/' . $slideShow->slide_show_id . '/' . $slideShowItems->slide_show_item_id . '/');
                } else {
                    $this->setFlashSession($slideShowItems->getMessages(), 'notice');
                }
            }
        }
        return null;
    }

    /**
     * Move up slide show item (Ordering)
     *
     * @param $slideID
     * @param $slideItemID
     * @return \Phalcon\Http\ResponseInterface
     * @throws \Phalcon\Exception
     */
    public function moveUpAction($slideID, $slideItemID)
    {
        $slideID = intval($slideID);
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$slideID]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        $slideItemID = intval($slideItemID);

        /**
         * @var SlideShowItems $slideShowItems
         */
        $slideShowItems = SlideShowItems::findFirst([
            'conditions' => 'slide_show_item_id = ?0',
            'bind' => [$slideItemID]
        ]);

        if (!$slideShowItems) {
            return $this->response->redirect('/admin/slide/');
        }

        if ($slideShowItems->moveUp('slide_show_id = ?0', [$slideID])) {
            $this->flashSession->success('Move up successfully');
        } else {
            $this->flashSession->warning('Move up error');
        }

        return $this->response->redirect('/admin/slide/manage-slide/slide/' . $slideID . '/');
    }

    /**
     * Move down slide show item (Ordering)
     * @param $slideID
     * @param $slideItemID
     * @return \Phalcon\Http\ResponseInterface
     * @throws \Phalcon\Exception
     */
    public function moveDownAction($slideID, $slideItemID)
    {
        $slideID = intval($slideID);
        $slideShow = SlideShows::findFirst([
            'conditions' => 'slide_show_id = ?0',
            'bind' => [$slideID]
        ]);

        if (!$slideShow) {
            return $this->response->redirect('/admin/slide/');
        }

        /**
         * @var SlideShowItems $slideShowItems
         */
        $slideItemID = intval($slideItemID);
        $slideShowItems = SlideShowItems::findFirst([
            'conditions' => 'slide_show_item_id = ?0',
            'bind' => [$slideItemID]
        ]);

        if (!$slideShowItems) {
            return $this->response->redirect('/admin/slide/');
        }

        if ($slideShowItems->moveDown('slide_show_id = ?0', [$slideID])) {
            $this->flashSession->success('Move down up successfully');
        } else {
            $this->flashSession->warning('Move down error');
        }

        return $this->response->redirect('/admin/slide/manage-slide/slide/' . $slideID . '/');
    }
}