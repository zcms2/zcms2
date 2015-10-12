<?php

namespace ZCMS\Backend\System\Controllers;

use ZCMS\Core\Models\Users;
use ZCMS\Core\Utilities\ZZip;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use Phalcon\Mvc\View;

/**
 * Class DatabaseController
 *
 * Backup and download database. SuperAdmin with verify password need to download backup file!
 *
 * @package ZCMS\Backend\System\Controllers
 */
class DatabaseController extends ZAdminController
{
    /**
     * List all backup
     */
    public function indexAction()
    {
        $this->_toolbar->addBreadcrumb('m_system_system_manager');
        $this->_toolbar->addBreadcrumb('m_system_backup_database');
        $this->_toolbar->addHeaderPrimary('m_system_backup_database');
        $this->_toolbar->addCustomButton('system|database|backup', 'Backup Database', '/admin/system/database/backup/', 'glyphicon glyphicon-floppy-disk', 'btn btn-success');
        $this->_toolbar->addCustomButton('system|database|fullBackUp', 'Backup Full Site', '/admin/system/database/fullBackUp/', 'glyphicon glyphicon-retweet', 'btn btn-primary');

        $filesBackup = glob(APP_DIR . '/backup/database/' . '*.backup');
        $files = [];
        foreach ($filesBackup as $index => $file) {
            $fileOb = new \stdClass();
            $fileOb->id = $index + 1;
            $fileOb->name = basename($file);
            $fileOb->base64Name = base64_encode($fileOb->name);
            $fileOb->size = number_format((filesize($file) / (1024 * 1024)), 3) . ' MB';
            $files[] = $fileOb;
        }

        //Add filter
        $this->addFilter('filter_order', 'id', 'string');
        $this->addFilter('filter_order_dir', 'ASC', 'string');

        //Get all filter
        $filter = $this->getFilter();
        $this->view->setVar('_filter', $filter);

        $currentPage = $this->request->get('page');
        $this->view->setVar('_page', ZPagination::getPaginationNativeArray($files, $this->config->pagination->limit, $currentPage));

        $this->view->setVar('_pageLayout', [
            [
                'type' => 'check_all'
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'title' => 'File Name',
                'column' => 'name',
                'access' => $this->acl->isAllowed('system|database|download'),
                'link' => '/admin/system/database/download/',
                'link_prefix' => 'base64Name',
                'sort' => false
            ],
            [
                'type' => 'text',
                'title' => 'Size',
                'class' => 'text-center',
                'column' => 'size',
                'sort' => false
            ]
        ]);
    }

    /**
     * Backup database action
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function backupAction()
    {
        if ($this->acl->isAllowed('system|database|backup')) {

            $cmdResult = $this->backupDatabaseFile();
            if ($cmdResult == false) {
                $this->flashSession->notice('Backup database error!');

            } else {
                $this->flashSession->success('Backup database success with name: ' . $cmdResult);
            }
        }
        return $this->response->redirect('/admin/system/database/');
    }

    /**
     * Full backup site
     */
    public function fullBackUpAction()
    {
        if ($this->acl->isAllowed('system|database|fullBackUp')) {
            $databaseBackupName = $this->backupDatabaseFile();
            if ($databaseBackupName) {
                $fileName = generateAlias($this->config->website->sitename) . '_' . date('dmY_His') . '_from_UserID_' . $this->_user['id'] . '.backup.001';
                ini_set('max_execution_time', 0);
                $path = APP_DIR . '/backup/sources';
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
                $zip = new ZZip(ROOT_PATH, $path . DS . $fileName);
                $zip->notContain('app/backup');
                $zip->notContain('cache');
//               $zip->notContain('public/images');
//               $zip->notContain('sql');
//               $zip->addNeededContains('public/images/attribute-item-icons');
//               $zip->addNeededContains('public/images/attribute-item-images');
//               $zip->addNeededContains('public/images/user-images');
//               $zip->addNeededContains('public/images/tmp');
//               $zip->addNeededContains('public/images/product-images');
                $zip->addNeededContains('app/backup/database/' . $databaseBackupName);
                if ($zip->zip()) {
                    $this->flashSession->success('Backup site successfully with file name: ' . $path . DS . $fileName);
                } else {
                    $this->flashSession->notice('Backup site error');
                }
            } else {
                $this->flashSession->notice('Error while backup database');
            }
        }
        $this->response->redirect('/admin/system/database/');
    }

    /**
     * Backup file
     *
     * @return bool|string
     */
    private function backupDatabaseFile()
    {
        putenv("PGPASSWORD=" . $this->config->database->password);
        $path = ROOT_PATH . '/app/backup/database';
        if (!is_dir($path)) {
            mkdir($path, 0755);
        }
        $fileName = $this->config->database->dbname . '_' . date('dmY_His') . '_from_UserID_' . $this->_user['id'] . '.backup';
        $backupCMD = 'pg_dump -U ' . $this->config->database->username . ' ' . $this->config->database->dbname . ' > ' . $path . DS . $fileName;
        exec($backupCMD, $cmdOut, $cmdResult);
        putenv("PGPASSWORD");
        if ($cmdResult != 0) {
            return false;
        }
        return $fileName;
    }

    /**
     * Download database file
     *
     * @param null $base64Name
     * @return bool|\Phalcon\Http\ResponseInterface
     */
    public function downloadAction($base64Name = null)
    {
        if ($base64Name != null && isset($auth['id']) && $auth['id'] != 0) {
            /**
             * @var $user Users
             */
            $user = Users::findFirst([
                'conditions' => 'id = ?0',
                'bind' => [(int)$this->_user['id']]
            ]);
            if ($this->_user['is_supper_admin']) {
                $this->_toolbar->addBreadcrumb('m_system_system_manager');
                $this->_toolbar->addBreadcrumb('m_system_backup_database');
                $this->_toolbar->addHeaderPrimary('Download backup database');
                $this->_toolbar->addSaveButton('system|database|download', '/admin/system/database/download/', 'Download Database', 'glyphicon glyphicon-sort-by-attributes-alt');
                if ($this->request->isPost()) {
                    $password = $this->request->getPost('password');
                    if ($this->security->checkHash($password, $user->password) || md5($password) == $user->password) {
                        $fileName = base64_decode($base64Name);
                        $filePath = APP_DIR . '/backup/database/' . $fileName;
                        if (file_exists($filePath)) {
                            $fileType = filetype($filePath);
                            $fileSize = filesize($filePath);
                            $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
                            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                            header('Content-Description: File Transfer');
                            header('Content-type: ' . $fileType);
                            header('Content-length: ' . $fileSize);
                            header('Content-Disposition: attachment; filename="' . $fileName . '"');
                            readfile($filePath);
                            die();
                        } else {
                            $this->flashSession->warning('File not exists');
                        }
                    } else {
                        $this->flashSession->warning('Please enter your current password');
                    }
                }
            } else {
                return $this->response->redirect('/admin/system/database/');
            }
        } else {
            return $this->response->redirect('/admin/system/database/');
        }
        return false;
    }
}