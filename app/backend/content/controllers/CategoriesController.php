<?php

namespace ZCMS\Backend\Content\Controllers;

use ZCMS\Backend\Content\Forms\CategoryForm;
use ZCMS\Core\Models\PostCategory;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;

/**
 * Class CategoriesController
 *
 * @package ZCMS\Backend\Content\Controllers
 */
class CategoriesController extends ZAdminController
{
    /**
     * List all categories
     */
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addPublishedButton();
        $this->_toolbar->addUnPublishedButton();
        $this->_toolbar->addNewButton();
        $this->_toolbar->addDeleteButton();

        //Add sorting
        $this->addFilter('filter_order', 'lft', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_column_title', '', 'string');
        $this->addFilter('filter_category_id', '', 'int');
        $this->addFilter('filter_published', '', 'int');
        $this->addFilter('filter_role', '', 'int');

        //Get filter
        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);

        $conditions = [];

        $conditions[] = 'root = 0';

        if ($filter['filter_column_title']) {
            $conditions[] = "LOWER(title) LIKE '%" . strtolower($filter['filter_column_title']) . "%'";
        }

        if ($filter['filter_category_id']) {
            $conditions[] = "category_id = " . intval($filter['filter_category_id']);
        }

        if ($filter['filter_published'] != '' && $filter['filter_published'] != '-1') {
            $conditions[] = "published = " . intval($filter['filter_published']);
        }

        if($filter['filter_role'] != '' && $filter['filter_role'] != '-1'){
            $conditions[] = "u.role_id = " . intval($filter['filter_role']);
        }

        $items = $this->modelsManager->createBuilder()
            ->columns('c.category_id, c.title, c.created_at, c.updated_at, c.published, c.lft, c.rgt, c.level, u.display_name')
            ->addFrom('ZCMS\Core\Models\PostCategory', 'c')
            ->join('ZCMS\Core\Models\Users', 'c.created_by = u.user_id', 'u')
            ->where(implode(' AND ', $conditions))
            ->orderBy($filter['filter_order'] . ' ' . $filter['filter_order_dir']);
        $paginationLimit = $this->config->pagination->limit;
        $currentPage = $this->request->getQuery('page', 'int');

        $this->view->setVar('_page', ZPagination::getPaginationQueryBuilder($items, $paginationLimit, $currentPage));
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'category_id'
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'title' => 'm_content_form_category_form_title',
                'column' => 'title',
                'link' => '/admin/content/categories/edit/',
                'access' => $this->acl->isAllowed('content|categories|edit'),
                'pad_column' => 'level',
                'pad_type' => STR_PAD_LEFT,
                'pad_string' => '&mdash; ',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_column_title',
                    'attributes' => []
                ]
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'access' => $this->acl->isAllowed('content|categories|edit'),
                'link' => '/admin/content/categories/',
                'column' => 'published',
                'filter' => [
                    'type' => 'select',
                    'name' => 'filter_published',
                    'attributes' => [
                        'useEmpty' => true,
                        'emptyValue' => '-1',
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
                'type' => 'actions',
                'title' => 'gb_ordering',
                'link_prefix' => 'category_id',
                'class' => 'text-center',
                'column' => 'lft',
                'actions' => [
                    [
                        'link_title' => 'gb_move_up',
                        'link' => '/admin/content/categories/moveUp/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-up', // Co icon_class thi ko hien thi title
                        'access' => $this->acl->isAllowed('content|categories|edit'),
                    ],
                    [
                        'link_title' => 'gb_move_down',
                        'link' => '/admin/content/categories/moveDown/',
                        'link_class' => '',
                        'icon_class' => 'glyphicon glyphicon-chevron-down', // Co icon_class thi ko hien thi title
                        'access' => $this->acl->isAllowed('content|categories|edit'),
                    ]
                ]
            ],
            [
                'type' => 'text',
                'title' => 'gb_created_by',
                'column' => 'display_name',
                'class' => 'text-center',
                'filter' => [
                    'type' => 'select',
                    'name' => 'filter_role',
                    'attributes' => [
                        'using' =>[
                            'role_id',
                            'name'
                        ],
                        'useEmpty' => true,
                        'emptyValue' => '-1',
                        'emptyText' => __('gb_all'),
                        'value' => $filter['filter_role'] == '' ? -1 : $filter['filter_role']
                    ],
                    'value' => UserRoles::find()
                ]
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
                'column' => 'category_id',
                'css' => 'width : 10px',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_category_id',
                    'attributes' => []
                ],
            ]
        ]);
    }

    /**
     * New Category
     */
    public function newAction()
    {
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        $categoryForm = new CategoryForm();
        $this->view->setVar('form', $categoryForm);
        $category = new PostCategory();
        if ($this->request->isPost() && $categoryForm->isValid($_POST, $category)) {
            $parent = PostCategory::findFirst((int)$_POST['parent']);
            if ($parent) {
                if ($category->appendTo($parent)) {
                    $this->flashSession->success('m_content_category_message_add_new_category_successfully');
                    $this->response->redirect('/admin/content/categories/edit/' . $category->category_id . '/');
                    return;
                } else {
                    $this->setFlashSession($category->getMessages(), 'notice');
                    return;
                }
            } else {
                $this->flashSession->notice('m_content_category_message_parent_category_not_exists');
                return;
            }
        }
    }

    /**
     * Edit Category
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        /**
         * @var PostCategory $category
         */
        $category = PostCategory::findFirst([
            'conditions' => "root = 0 AND module = 'content'  AND category_id = ?0",
            'bind' => [(int)$id]
        ]);

        if (!$category) {
            $this->flashSession->notice('m_content_category_message_category_not_exist');
            $this->response->redirect('/admin/content/categories/');
            return;
        }

        $parentCategory = $category->parent();
        if ($parentCategory) {
            $oldParent = $parentCategory->category_id;
        } else {
            $oldParent = 0;
        }


        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        $categoryForm = new CategoryForm($category, ['edit' => true]);
        $this->view->setVar('form', $categoryForm);

        $success = true;

        if ($this->request->isPost() && $categoryForm->isValid($_POST, $category)) {
            /**
             * @var PostCategory $parent
             */
            $parent = PostCategory::findFirst((int)$_POST['parent']);
            if ($parent) {
                if ($parent->category_id == $oldParent && $category->saveNode()) {
                    $success = true;
                } elseif ($parent->category_id != $oldParent && $category->saveNode()) {
                    if ($category->moveAsLast($parent)) {
                        $success = true;
                    }
                }
                if ($success) {
                    $this->flashSession->success('m_content_category_message_update_category_successfully');
                } else {
                    $this->setFlashSession($category->getMessages(), 'notice');
                }
            } else {
                $this->flashSession->notice('m_content_category_message_parent_category_not_exists');
                return;
            }
        }
        $this->view->pick('categories/new');
    }

    /**
     * Move up
     *
     * @param $id
     */
    public function moveUpAction($id)
    {
        /**
         * @var PostCategory $category
         */
        $success = false;
        $category = PostCategory::findFirst($id);
        if ($brother = $category->prev()) {
            if ($category->moveBefore($brother)) {
                $success = true;
            }
        }
        if ($success) {
            $this->flashSession->success('m_content_category_message_move_up_category_successfully');
        } else {
            $this->flashSession->success('m_content_category_message_cannot_move_up_this_category');
        }
        $this->response->redirect('/admin/content/categories/');
        return;
    }

    /**
     * Move down
     *
     * @param $id
     */
    public function moveDownAction($id)
    {
        /**
         * @var PostCategory $category
         */
        $success = false;
        $category = PostCategory::findFirst($id);
        if ($brother = $category->next()) {
            if ($category->moveAfter($brother)) {
                $success = true;
            }
        }
        if ($success) {
            $this->flashSession->success('m_content_category_message_move_down_category_successfully');
        } else {
            $this->flashSession->success('m_content_category_message_cannot_move_down_this_category');
        }
        $this->response->redirect('/admin/content/categories/');
        return;
    }

    public function publishAction($id)
    {
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }

        $count = count($ids);
        if ($count) {
            foreach ($ids as $id) {
                /**
                 * @var PostCategory $category
                 */
                $category = PostCategory::findFirst($id);
                $parent = $category->parent();
                if ($category && (!$parent || $parent->published == 1)) {
                    /**
                     * @var PostCategory[] $child
                     */
                    $child = $category->children();
                    foreach ($child as $cat) {
                        $cat->published = 1;
                        $cat->saveNode();
                    }
                    $category->published = 1;
                    $category->saveNode();
                }
            }
            if ($count > 1) {
                $this->flashSession->success(__('m_content_category_message_categories_successfully_published', [$count]));
            } else {
                $this->flashSession->success('m_content_category_message_one_category_successfully_published');
            }
        }
        $this->response->redirect('/admin/content/categories/');
    }

    public function unpublishAction($id)
    {
        if ($id) {
            $id = intval($id);
            $ids[] = $id;
        } else {
            $ids = $this->request->getPost('ids');
            ZArrayHelper::toInteger($ids);
        }

        $count = count($ids);
        if ($count) {
            foreach ($ids as $id) {
                /**
                 * @var PostCategory $category
                 */
                $category = PostCategory::findFirst($id);
                if ($category) {
                    /**
                     * @var PostCategory[] $child
                     */
                    $child = $category->children();
                    foreach ($child as $cat) {
                        $cat->published = 0;
                        $cat->saveNode();
                    }
                    $category->published = 0;
                    $category->saveNode();
                }
            }
            if ($count > 1) {
                $this->flashSession->success(__('m_content_category_message_categories_successfully_unpublished', [$count]));
            } else {
                $this->flashSession->success('m_content_category_message_one_category_successfully_unpublished');
            }
        }
        $this->response->redirect('/admin/content/categories/');
    }
}