<?php

namespace ZCMS\Modules\Media\Controllers\Admin;


use Phalcon\Mvc\View;
use ZCMS\Core\ZPagination;
use ZCMS\Core\Models\Medias;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\Utilities\MediaUpload;
use ZCMS\Core\Utilities\ZArrayHelper;

/**
 * Class ManagerController
 *
 * @package ZCMS\Backend\Media\Controllers
 */
class ManagerController extends ZAdminController
{
    public function indexAction()
    {
        //Add toolbar button
        $this->_toolbar->addDeleteButton();

        //Add filter
        $this->addFilter('filter_order', 'first_name', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_search', '', 'string');
        $this->addFilter('filter_role', '', 'string');

        /**
         * @var UserRoles[] $roles
         */
        $roles = UserRoles::find();
        $rolesData = ['' => __('gb_select_role')];
        foreach ($roles as $role) {
            $rolesData[$role->role_id] = $role->name;
        }

        $this->view->setVar('rolesData', $rolesData);

        //Get all filter
        $filter = $this->getFilter();


        $conditions = [];

        if (trim($filter['filter_role'])) {
            $conditions[] = "role_id = " . intval($filter['filter_role']);
        }

        $filter['filter_search'] = trim($filter['filter_search']);

        if ($filter['filter_search']) {
            $conditions[] = "CONCAT(first_name, ' ', last_name) ILIKE '%" . $filter['filter_search'] . "%' OR email like '%" . $filter['filter_search'] . "%'";
        }

        //Get all user
        $items = $this->modelsManager->createBuilder()
            ->columns('m.media_id, m.title, u.display_name, m.created_at')
            ->addFrom('ZCMS\Core\Models\Medias', 'm')
            ->join('ZCMS\Core\Models\Users', 'm.created_by = u.user_id', 'u')
            ->where(implode(' AND ', $conditions))
            ->orderBy($filter['filter_order'] . ' ' . $filter['filter_order_dir']);

        $currentPage = $this->request->getQuery('page', 'int');
        $paginationLimit = $this->config->pagination->limit;

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationQueryBuilder($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);
        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'media_id'
            ],
            [
                'type' => 'text',
                'title' => 'm_media_form_media_form_title',
                'column' => 'title'
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'media_id'
            ]
        ]);
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
             * @var Medias[] $posts
             */
            $medias = Medias::find([
                'conditions' => 'media_id IN (' . implode(',', $ids) . ')'
            ]);
            foreach ($medias as $m) {
                if(file_exists(ROOT_PATH . '/public' . $m->src)){
                    unlink(ROOT_PATH . '/public' . $m->src);
                }
                $m->delete();
            }
            if ($count == 1) {
                $this->flashSession->success('m_media_media_message_delete_one_media_successfully');
            } else {
                $this->flashSession->success(__('m_media_media_message_delete_medias_successfully', [$count]));
            }
        }
        $this->response->redirect('/admin/media/manager/');
    }

    public function getMediaAction()
    {
        $keyword = $this->request->get('keyword');
        $page = (int)$this->request->get('page');
        if($page <= 0){
            $page = 1;
        }
        $offset = ($page - 1) * 20;
        if($offset <= 0){
            $offset = 0;
        }
        if ($keyword) {
            $result = Medias::find([
                'conditions' => 'title LIKE \'%' . $keyword . '%\'',
                'limit' => 20,
                'offset' => $offset,
                'order' => 'created_at DESC'
            ])->toArray();
        } else {
            $result = Medias::find([
                'limit' => 20,
                'offset' => $offset,
                'order' => 'created_at DESC'
            ])->toArray();
        }

        die(json_encode([
            'code' => 1,
            'data' => $result
        ]));
    }

    public function newAction()
    {
        $this->view->setVar('max_file_upload', (int)ini_get("upload_max_filesize"));
    }

    public function uploadImageAction()
    {
        if ($this->request->isAjax()) {
            if ($files = $this->request->getUploadedFiles()) {
                $response = (new MediaUpload($files[0]))->response;
                if ($response['code'] == 0) {
                    //$this->response->setStatusCode(200, $response['msg']);
                } else {
                    $this->response->setStatusCode(406, $response['msg']);
                }
                $this->view->disableLevel(View::LEVEL_NO_RENDER);
            }
        }
    }
}