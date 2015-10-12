<?php

namespace ZCMS\Backend\Template\Controllers;

use ZCMS\Core\Models\CoreTemplates;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZTranslate;

/**
 * Class IndexController
 *
 * @package ZCMS\Backend\Template\Controllers
 * @version 0.0.1
 */
class IndexController extends ZAdminController
{

    /**
     * Index action
     *
     * Display list template
     */
    public function indexAction()
    {
        //Add template language
        $this->_addTemplateLang();
        //Update all template backend
        $this->_updateAllTemplate('backend');
        //Update all template frontend
        $this->_updateAllTemplate('frontend');

        //Add toolbar button
        $this->_toolbar->addNewButton('install');

        //Add filter
        $this->addFilter('filter_order', 'template_id', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');
        $this->addFilter('filter_search', '', 'string');
        $this->addFilter('filter_location', '', 'string');

        //Get all filter
        $filter = $this->getFilter();

        $conditions = [];

        if (trim($filter['filter_search'])) {
            $conditions[] = "name like '%" . trim($filter['filter_search']) . "%'";
        }

        if ($filter['filter_location']) {
            $conditions[] = "location = '" . $filter['filter_location'] . "'";
        }

        /**
         * @var CoreTemplates[] $items
         */
        $items = CoreTemplates::find([
            'conditions' => implode(' AND ', $conditions),
            'order' => $filter['filter_order'] . ' ' . $filter['filter_order_dir'],
        ]);

        if (!count($items)) {
            $this->flashSession->notice(__('m_template_notice_there_are_no_template_matching_your_query'));
        }

        $currentPage = $this->request->getQuery('page', 'int');
        $paginationLimit = $this->config->pagination->limit;

        $filter_location = [
            '' => __('gb_select_location'),
            'backend' => __('gb_backend'),
            'frontend' => __('gb_frontend')
        ];

        //Set filter to view
        $this->view->setVar('filter_location', $filter_location);

        //Create pagination
        $this->view->setVar('_page', ZPagination::getPaginationModel($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);

        //Set column name, value
        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all',
                'column' => 'template_id'
            ],
            [
                'type' => 'index',
                'title' => '#',
            ],
            [
                'type' => 'text',
                'title' => 'gb_template_name',
                'column' => 'name',
                'translation' => true,
            ],
            [
                'type' => 'text',
                'title' => 'gb_description',
                'column' => 'description',
                'translation' => true,
            ],
            [
                'type' => 'text',
                'title' => __('gb_version'),
                'class' => 'text-center',
                'column' => 'version'
            ],
            [
                'type' => 'text',
                'title' => 'gb_author',
                'class' => 'text-center',
                'column' => 'author'
            ],
            [
                'type' => 'action',
                'title' => 'gb_active',
                'column' => 'published',
                'link_prefix' => 'template_id',
                'class' => 'text-center col-published',
                'action' => [
                    [
                        'condition' => '==',
                        'condition_value' => '1',
                        'link' => '/admin/template/index/#',
                        'link_title' => 'gb_default_language',
                        'access' => 1,
                        'icon_class' => 'glyphicon glyphicon-star orange',
                    ],
                    [
                        'condition' => '==',
                        'condition_value' => '0',
                        'link' => '/admin/template/index/publish/',
                        'link_title' => 'm_system_language_message_set_default_language',
                        'access' => $this->acl->isAllowed('system|language|published'),
                        'icon_class' => 'glyphicon glyphicon-star grey',
                    ]
                ]
            ],
            [
                'type' => 'text',
                'title' => 'gb_location',
                'class' => 'text-center',
                'column' => 'location',
                'label' => [
                    [
                        'condition' => '==',
                        'condition_value' => 'backend',
                        'class' => 'label label-sm label-success',
                        'text' => 'gb_backend'
                    ],
                    [
                        'condition' => '!=',
                        'condition_value' => 'backend',
                        'class' => 'label label-sm label-warning',
                        'text' => 'gb_frontend'
                    ]
                ],
                'translation' => true,
            ],
            [
                'type' => 'id',
                'title' => 'gb_id',
                'column' => 'template_id'
            ]
        ]);
    }

    /**
     * Update template information
     *
     * @param string $location Value backend|frontend
     */
    private function _updateAllTemplate($location)
    {
        if ($location === 'frontend' || $location === 'backend') {
            $templates = get_child_folder(APP_DIR . '/templates/' . $location . '/');
            if (count($templates)) {
                $templateTmp = [];
                foreach ($templates as $template) {
                    $templateTmp[] = "'" . $template . "'";
                }
                /**
                 * @var CoreTemplates[] $templateMustDelete
                 */
                $templateMustDelete = CoreTemplates::find([
                    'conditions' => 'base_name NOT IN(' . implode(',', $templateTmp) . ") AND location='" . $location . "'"
                ]);

                if (count($templateMustDelete) > 0) {
                    foreach ($templateMustDelete as $tMD) {
                        if (method_exists($tMD, "delete")) {
                            $tMD->delete();
                        }
                    }
                }

                foreach ($templates as $template) {
                    $pathTemplate = APP_DIR . '/templates/' . $location . '/' . $template . '/template.json';
                    if ($resource = check_template($pathTemplate)) {
                        $templateObject = CoreTemplates::findFirst('base_name ="' . $template . '" AND location = "' . $location . '"');
                        if (!$templateObject) {
                            $templateObject = new CoreTemplates();
                            $templateObject->base_name = $template;
                            $templateObject->published = 0;
                            $templateObject->location = $location;
                        }
                        $templateObject->name = $resource['name'];
                        $templateObject->uri = $resource['uri'];
                        $templateObject->author = $resource['author'];
                        $templateObject->authorUri = $resource['authorUri'];
                        $templateObject->version = $resource['version'];
                        $templateObject->tag = $resource['tag'];
                        $templateObject->description = $resource['description'];
                        if (!$templateObject->save()) {
                            $this->flashSession->error(__('m_template_notice_not_update_template', ['1' => $templateObject->name, '2' => '$location', '3' => APP_DIR . '/templates/{$location}/' . $templateObject->base_name . '/template.json']));
                        };
                    } else {
                        $this->flashSession->error(__('m_template_notice_not_update_template', ['1' => 'Base name: ' . $template, '2' => '$location', '3' => APP_DIR . '/templates/{$location}/' . $template . '/template.json']));
                    }
                }
                /**
                 * @var CoreTemplates[] $templatePublished
                 */
                $templatePublished = CoreTemplates::find('published = 1 AND location="' . $location . '"');
                if (!count($templatePublished)) {
                    /**
                     * @var CoreTemplates $templateDefault
                     */
                    $templateDefault = CoreTemplates::findFirst('base_name = "default" AND location="frontend"');
                    if ($templateDefault) {
                        $templateDefault->published = 1;
                        $templateDefault->save();
                    }
                }
            }
        }
    }

    /**
     * Published template
     *
     * @param int $id
     * @param string $redirect
     * @param bool $log
     * @return \Phalcon\Http\ResponseInterface|void
     */
    public function publishAction($id = null, $redirect = null, $log = true)
    {
        //Add template language
        $this->_addTemplateLang();

        $id = (int)$id;
        /**
         * @var CoreTemplates $templateMustPublish
         */
        $templateMustPublish = CoreTemplates::findFirst($id);
        if ($templateMustPublish) {
            $query = "UPDATE core_templates SET published = 0 WHERE location = '{$templateMustPublish->location}'";
            $this->db->execute($query);
            $templateMustPublish->published = 1;
            $templateMustPublish->save();
            file_put_contents(APP_DIR . '/' . $templateMustPublish->location . '/index.volt', '{% extends "../../../templates/' . $templateMustPublish->location . '/' . $templateMustPublish->base_name . '/index.volt" %}');
            if ($templateMustPublish->location == 'frontend') {
                //Do something
            } elseif ($templateMustPublish->location == 'backend') {
                //Do something
            }
            $this->flashSession->success(__('m_template_notice_template_is_active', ['1' => __($templateMustPublish->name), '2' => $templateMustPublish->location]));
        } else {
            $this->flashSession->error(__('m_template_notice_template_not_exists'));
        }
        return $this->response->redirect('/admin/template/');
    }

    /**
     * Add template language
     */
    private function _addTemplateLang()
    {
        $templates = get_child_folder(APP_DIR . '/templates/backend/');
        ZTranslate::getInstance()->addTemplateLang($templates);
        $templates = get_child_folder(APP_DIR . '/templates/frontend/');
        ZTranslate::getInstance()->addTemplateLang($templates, 'frontend');
    }
}