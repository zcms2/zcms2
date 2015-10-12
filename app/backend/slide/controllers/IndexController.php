<?php

namespace ZCMS\Backend\Slide\Controllers;

use ZCMS\Core\ZPagination;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\Models\SlideShows;
use ZCMS\Core\Models\SlideShowItems;
use ZCMS\Core\Utilities\ZImageHelper;
use ZCMS\Backend\Slide\Forms\SlideShowForm;

/**
 * Class IndexController
 *
 * @package ZCMS\Backend\Slide\Controllers
 */
class IndexController extends ZAdminController
{

    /**
     * Define image folder
     */
    const SLIDE_SHOW_FOLDER_UPLOAD = 'media/slide-shows';

    /**
     * @var string PHQL Model
     */
    public $_model = 'ZCMS\Core\Models\SlideShows';

    /**
     * @var string Model name in database
     */
    public $_modelBaseName = 'slide_shows';

    /**
     * List all slide show
     */
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addNewButton();
        $this->_toolbar->addDeleteButton();

        //Add sorting
        $this->addFilter('filter_order', 'slide_show_id', 'string');
        $this->addFilter('filter_order_dir', 'DESC', 'string');
        $this->addFilter('filter_column_title', '', 'string');
        $this->addFilter('filter_slide_show_id', '', 'int');
        $this->addFilter('filter_published', '', 'string');

        //Get filter
        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);

        $conditions = [];

        if ($filter['filter_column_title']) {
            $conditions[] = "LOWER(title) LIKE '%" . strtolower(htmlspecialchars($filter['filter_column_title'])) . "%'";
        }

        if ($filter['filter_slide_show_id']) {
            $conditions[] = "slide_show_id = " . intval($filter['filter_slide_show_id']);
        }

        if ($filter['filter_published'] != '') {
            $conditions[] = "published = " . intval($filter['filter_published']);
        }

        $slides = SlideShows::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir']
        ]);

        $paginationLimit = $this->config->pagination->limit;
        $currentPage = $this->request->getQuery('page', 'int');

        $this->view->setVar('_page', ZPagination::getPaginationModel($slides, $paginationLimit, $currentPage));
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'slide_show_id'
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
                'title' => 'm_slide_form_slide_show_form_image',
                'class' => 'text-center',
                'default_thumbnail' => '/media/default/no-image.png'
            ],
            [
                "type" => "link",
                "title" => "m_slide_form_slide_show_form_title",
                "column" => "title",
                "link" => "/admin/slide/index/edit/",
                "access" => $this->acl->isAllowed('slide|index|edit'),
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_column_title',
                    'attributes' => []
                ]
            ],
            [
                "type" => "action",
                "title" => "m_admin_slide_index_manage_slide_items",
                "css" => "width: 120px;",
                "class" => 'text-center',
                'link_prefix' => 'slide_show_id',
                "action" => [
                    [
                        'condition' => '!=',
                        'condition_value' => '0',
                        'link' => '/admin/slide/manage-slide/slide/',
                        'link_title' => 'Manage this slide',
                        'icon_class' => 'fa fa-edit',
                        "access" => $this->acl->isAllowed('slide|manage-slide|slide'),
                    ]
                ],
                "column" => "title",
                "sort" => false
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'access' => $this->acl->isAllowed('slide|index|edit'),
                'link' => '/admin/slide/index/',
                'column' => 'published',
                'filter' => [
                    'type' => 'select',
                    'name' => 'filter_published',
                    'attributes' => [
                        'useEmpty' => true,
                        'emptyValue' => '0',
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
                'column' => 'slide_show_id',
                'css' => 'width : 100px',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_slide_show_id',
                    'attributes' => []
                ]
            ]
        ]);
    }

    /**
     * Edit slide show
     *
     * @param $id
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function editAction($id)
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

        $this->view->pick('index/new');
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCustomButton('slide|manageSlide|slide', 'm_admin_slide_index_manage_slide_show_items', '/admin/slide/manage-slide/slide/' . $id . '/', 'fa fa-edit', 'btn btn-success');
        $this->_toolbar->addCancelButton('index');

        $form = new SlideShowForm($slideShow);
        $this->view->setVar('form', $form);
        $this->view->setVar('slideShow', $slideShow);

        if ($this->request->isPost()) {

            if ($form->isValid($_POST, $slideShow)) {
                if ($slideShow->save()) {
                    $imageStatus = ZImageHelper::uploadImages(
                        $this->request->getUploadedFiles(),
                        self::SLIDE_SHOW_FOLDER_UPLOAD . '/' . $slideShow->slide_show_id,
                        'image',
                        null,
                        $slideShow->image);
                    if ($imageStatus['status']) {
                        $slideShow->image = $imageStatus['imageUrl'];
                        $slideShow->save();
                    } else {
                        if ($imageStatus['message']) {
                            $this->flashSession->notice($imageStatus['message']);
                        }
                        if ($imageStatus['bugMessage']) {
                            $this->flashSession->notice($imageStatus['bugMessage']);
                        }
                    }
                    $this->flashSession->success(__('m_admin_slide_message_updated_slide_show_successfully', [$slideShow->title]));
                    return $this->response->redirect('/admin/slide/index/edit/' . $slideShow->slide_show_id . '/');
                } else {
                    $this->setFlashSession($slideShow->getMessages(), 'notice');
                }
            }
        }
        return null;
    }

    /**
     * Add new slide show
     *
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function newAction()
    {
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');
        $form = new SlideShowForm();
        $this->view->setVar('form', $form);

        if ($this->request->isPost()) {
            $slideShow = new SlideShows();
            if ($form->isValid($_POST, $slideShow)) {
                if ($slideShow->save()) {
                    $imageStatus = ZImageHelper::uploadImages($this->request->getUploadedFiles(), self::SLIDE_SHOW_FOLDER_UPLOAD . '/' . $slideShow->slide_show_id, 'image');
                    if ($imageStatus['status']) {
                        $slideShow->image = $imageStatus['imageUrl'];
                        $slideShow->save();
                    } else {
                        if ($imageStatus['message']) {
                            $this->flashSession->notice($imageStatus['message']);
                        }
                        if ($imageStatus['bugMessage']) {
                            $this->flashSession->notice($imageStatus['bugMessage']);
                        }
                    }
                    $this->flashSession->success(__('m_admin_slide_message_add_new_slide_show_successfully', [$slideShow->title]));
                    return $this->response->redirect('/admin/slide/index/edit/' . $slideShow->slide_show_id . '/');
                } else {
                    $this->setFlashSession($slideShow->getMessages(), 'notice');
                }
            }
        }
        return null;
    }

    /**
     * Delete slide show
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function deleteAction()
    {
        $id = 0;
        $ids = $this->request->getPost('ids');
        if (count($ids)) {
            $id = $ids[0];
        }

        if ($id > 0) {
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

            /**
             * @var $slideShowItems SlideShowItems[]
             */
            $slideShowItems = SlideShowItems::find([
                'conditions' => 'slide_show_item_id = ?0',
                'bind' => [$id]
            ]);

            if (count($slideShowItems)) {
                $this->flashSession->notice('m_admin_slide_message_you_must_delete_slide_show_items_before_delete_slide_show');
            } else {
                $file = ROOT_PATH . '/public' . $slideShow->image;
                if (!is_dir($file) && file_exists($file) && strpos($file, ROOT_PATH . '/public/' . self::SLIDE_SHOW_FOLDER_UPLOAD) !== false) {
                    unlink($file);
                }
                $slideShow->delete();
                $this->flashSession->success('m_admin_slide_message_you_must_delete_slide_show_successfully');
                return $this->response->redirect('/admin/slide/');
            }
        }
        return $this->response->redirect('/admin/slide/');
    }
}