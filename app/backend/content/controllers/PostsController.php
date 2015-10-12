<?php

namespace ZCMS\Backend\Content\Controllers;

use ZCMS\Backend\Content\Forms\PostForm;
use ZCMS\Core\Models\Posts;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\ZArrayHelper;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;

/**
 * Class PostsController
 *
 * @package ZCMS\Backend\Content\Controllers
 */
class PostsController extends ZAdminController
{

    /**
     * List all posts
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
        $this->addFilter('filter_post_id', '', 'int');
        $this->addFilter('filter_published', '', 'string');
        $this->addFilter('filter_role', '', 'int');

        //Get filter
        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);

        $conditions = [];

        if ($filter['filter_column_title']) {
            $conditions[] = "LOWER(p.title) LIKE '%" . strtolower($filter['filter_column_title']) . "%'";
        }

        if ($filter['filter_post_id']) {
            $conditions[] = "p.post_id = " . intval($filter['filter_post_id']);
        }

        if ($filter['filter_published'] != '' && $filter['filter_published'] != '-1') {
            $conditions[] = "p.published = " . intval($filter['filter_published']);
        }

        if($filter['filter_role'] != '' && $filter['filter_role'] != '-1'){
            $conditions[] = "u.role_id = " . intval($filter['filter_role']);
        }

        $items = $this->modelsManager->createBuilder()
            ->columns('p.post_id, p.title, p.created_at, p.updated_at, p.published AS published, u.display_name, c.title as c_title')
            ->addFrom('ZCMS\Core\Models\Posts', 'p')
            ->join('ZCMS\Core\Models\Users', 'p.created_by = u.user_id', 'u')
            ->leftJoin('ZCMS\Core\Models\PostCategory', 'p.category_id = c.category_id', 'c')
            ->where(implode(' AND ', $conditions))
            ->orderBy($filter['filter_order'] . ' ' . $filter['filter_order_dir']);
        $paginationLimit = $this->config->pagination->limit;
        $currentPage = $this->request->getQuery('page', 'int');

        $this->view->setVar('_page', ZPagination::getPaginationQueryBuilder($items, $paginationLimit, $currentPage));
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'post_id'
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'title' => 'm_content_form_post_form_title',
                'column' => 'title',
                'link' => '/admin/content/posts/edit/',
                'access' => $this->acl->isAllowed('content|posts|edit'),
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_column_title',
                    'attributes' => []
                ]
            ],
            [
                'type' => 'text',
                'title' => 'm_content_form_post_form_category_id',
                'column' => 'c_title',
                'class' => 'text-center'
            ],
            [
                'type' => 'published',
                'title' => 'gb_published',
                'access' => $this->acl->isAllowed('content|posts|edit'),
                'link' => '/admin/content/posts/',
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
                'type' => 'text',
                'title' => 'gb_created_by',
                'column' => 'display_name',
                'class' => 'text-center',
                'filter' => [
                    'type' => 'select',
                    'name' => 'filter_role',
                    'attributes' => [
                        'using' => [
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
                'column' => 'post_id',
                'css' => 'width : 10px',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_post_id',
                    'attributes' => []
                ],
            ]
        ]);
    }

    /**
     * Add new post
     */
    public function newAction()
    {
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        $postForm = new PostForm();
        $this->view->setVar('form', $postForm);
        $post = new Posts();
        if ($this->request->isPost() && $postForm->isValid($_POST, $post)) {
            if ($post->save()) {
                $this->flashSession->success('m_content_category_message_add_new_post_successfully');
                $this->response->redirect('/admin/content/posts/edit/' . $post->post_id . '/');
            } else {
                $this->setFlashSession($post->getMessages(), 'notice');
                return;
            }
        }
    }

    /**
     * Edit post
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->_toolbar->addSaveButton();
        $this->_toolbar->addCancelButton('index');

        /**
         * @var Posts $post
         */
        $post = Posts::findFirst([
            'conditions' => 'post_id = ?0',
            'bind' => [(int)$id]
        ]);

        if (!$post) {
            $this->response->redirect('/admin/content/posts');
            return;
        }

        $postForm = new PostForm($post);
        $this->view->setVar('form', $postForm);
        if ($this->request->isPost() && $postForm->isValid($_POST, $post)) {
            if ($post->save()) {
                $this->flashSession->success('m_content_post_message_update_post_successfully');
                $this->response->redirect('/admin/content/posts/edit/' . $post->post_id . '/');
            } else {
                $this->setFlashSession($post->getMessages(), 'notice');
                return;
            }
        }
        $this->view->pick('posts/new');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $ids = $this->request->getPost('ids');
        ZArrayHelper::toInteger($ids);
        $count = count($ids);
        if ($count) {
            /**
             * @var Posts[] $posts
             */
            $posts = Posts::find([
                'conditions' => 'post_id IN (' . implode(',', $ids) . ')'
            ]);
            foreach ($posts as $p) {
                $p->delete();
            }
            if ($count == 1) {
                $this->flashSession->success('m_content_post_message_delete_one_post_successfully');
            } else {
                $this->flashSession->success(__('m_content_post_message_delete_posts_successfully', [$count]));
            }
        }
        $this->response->redirect('/admin/content/posts/');
    }
}