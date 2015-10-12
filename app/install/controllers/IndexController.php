<?php

use Phalcon\Db;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

/**
 * Class IndexController
 */
class IndexController extends InstallController
{
    public function indexAction()
    {
        $this->_checkENV();

        $config = $this->_getConfig();
        if (!is_writable($this->configPath)) {
            $this->flashSession->notice('File config /app/config/config.php is not writable');
            $this->view->setVar('hiddenButton', '1');
            return;
        }

        $step = (int)$this->request->get('step', 'int', 0);
        if ($step == 1) {
            //Do something
        } elseif ($step == 2) {
            $this->view->pick('index/setUpDatabase');
            try {
                $this->view->setVar('db_connect', 1);
                if (!$this->config->database->host) {
                    return;
                }
                $this->db->connect();
            } catch (\Exception $e) {
                $this->view->setVar('db_connect', 0);
                if ($this->request->isPost() && $this->request->getPost('host', 'string', '')) {
                    $config['database']['adapter'] = $this->request->getPost('adapter', 'string', '');
                    $config['database']['host'] = $this->request->getPost('host', 'string', '');
                    $config['database']['port'] = $this->request->getPost('port', 'string', '');
                    $config['database']['dbname'] = $this->request->getPost('dbname', 'string', '');
                    $config['database']['username'] = $this->request->getPost('username', 'string', '');
                    $config['database']['password'] = $this->request->getPost('password', 'string', '');

                    $this->di->set('db', function () use ($config) {
                        $adapter = 'Phalcon\Db\Adapter\Pdo\\' . $config['database']['adapter'];
                        if ($config['database']['adapter'] == 'Mysql') {
                            return new $adapter($config['database']);
                        } else {
                            return new $adapter(array(
                                'host' => $config['database']['host'],
                                'username' => $config['database']['username'],
                                'password' => $config['database']['password'],
                                'dbname' => $config['database']['dbname']
                            ));
                        }
                    });

                    try {
                        $this->db->connect();
                        $this->_saveConfig($config);
                        header('Location: ' . BASE_URI . '/install.php?step=3');
                        exit;
                    } catch (\Exception $e) {
                        header('Location: ' . BASE_URI . '/install.php?step=2');
                        exit;
                    }
                }
            }
        } elseif ($step == 3) {
            $this->view->pick('index/setupAccount');
            if ($this->request->isPost() && $this->request->get('siteName')) {
                $siteName = $this->request->get('siteName', ['string', 'striptags']);
                $email = $this->request->getPost('email', 'email');
                $firstName = $this->request->getPost('first_name', ['string', 'striptags']);
                $lastName = $this->request->getPost('last_name', ['string', 'striptags']);
                $password = $this->request->getPost('password', 'string');
                $salt = $this->security->getSaltBytes();
                $config['website']['siteName'] = trim(preg_replace("/[^A-Za-z0-9 ]/", '', $siteName), ' ');
                $config['mail']['mailFrom'] = $email;
                $config['cachePrefix'] = randomString('6') . '_';
                $config['mail']['smtpUser'] = $email;
                $validation = new Validation();
                $validation->add('email', new Email(array(
                    'message' => 'Your email in valid'
                )));
                $validation->add('password', new StringLength([
                    'max' => 32,
                    'min' => 6,
                    'messageMaximum' => 'Password must be of maximum 32 characters',
                    'messageMinimum' => 'Password must be of minimum 6 characters'
                ]));
                $validation->add('password', new Confirmation([
                    'message' => "Password doesn't match confirmation",
                    'with' => 'confirmPassword'
                ]));
                $messages = $validation->validate($this->request->getPost());
                if (count($messages)) {
                    foreach ($messages as $message) {
                        $this->flashSession->notice($message);
                    }
                    return;
                }
                $this->db->begin();
                //Rename old tables
                $queriesRenameOldTable = $this->_renameOldTablesQueries($this->config);
                foreach ($queriesRenameOldTable as $query) {
                    if ($query != '') {
                        if (!$this->db->execute($query)) {
                            $this->db->rollback();
                            $this->flashSession->notice('Rename old table in database error');
                        }
                    }
                }
                //Install new table in ZCMS
                $password = $password . $salt;
                $queriesZCMS = $this->_fetchQueries($this->config->database->adapter);
                $queriesZCMS[] = "INSERT INTO users (role_id, first_name, last_name, display_name, email, password, salt, avatar, is_active, language_code, reset_password_token, reset_password_token_at, active_account_at, active_account_token, coin, token, gender, mobile, birthday, default_bill_address, default_ship_address, default_payment, country_id, country_state_id, short_description, created_at, created_by, updated_at, updated_by) VALUES (1, '" . $firstName . "', '" . $lastName . "', '" . $firstName . ' ' . $lastName . "', '" . $email . "','" . $this->security->hash($password) . "', '" . $salt . "','/media/users/default.png', 1, 'en-GB', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '" . date('Y-m-h H:i:s') . "', 1, '" . date('Y-m-h H:i:s') . "', 1)";
                foreach ($queriesZCMS as $query) {
                    if ($query != '') {
                        if (!$this->db->execute($query)) {
                            $this->db->rollback();
                            $this->flashSession->notice('Install database error');
                        }
                    }
                }
                //Commit transaction
                if ($this->db->commit()) {
                    $status = $this->_saveConfig($config);
                    if ($status) {
                        @rename(ROOT_PATH . '/public/install.php', ROOT_PATH . '/public/_install.php');
                        //@rename(ROOT_PATH . '/app/install/', ROOT_PATH . '/app/_install/');
                        header('Location: ' . BASE_URI . '/admin/');
                        exit;
                    } else {
                        //Do something
                        $this->flashSession->error('Could not setup ZCMS. Please check your system or contact to ZCMS Support Team!');
                        $this->response->redirect(BASE_URI . 'install?step=1');
                    }
                    return;
                }
            }
        }
    }

    /**
     * Fetch queries form file
     *
     * @param string $dbType postgresql | mysql
     * @return array
     */
    private function _fetchQueries($dbType)
    {
        $dbType = strtolower($dbType);
        if ($dbType == 'postgresql') {
            $queriesContent = file_get_contents(ROOT_PATH . '/app/install/sql/postgresql/zcms.sql');
            $queries = explode("--ZCMS--", $queriesContent);
            $queries[] = 'CREATE OR REPLACE FUNCTION zcms_cut_string(x text, l int4) RETURNS text AS $BODY$ DECLARE tmp1 TEXT ARRAY; lTmp1 INT; y TEXT; BEGIN IF length(x) <= l THEN RETURN x; END IF; y = left(x, l); tmp1 = regexp_split_to_array(y, E\'\\\\s+\'); lTmp1 = length(tmp1 [array_length(tmp1, 1)]); y = left(y, l - lTmp1); RETURN y; END; $BODY$ LANGUAGE plpgsql';
            $queries[] = 'CREATE OR REPLACE FUNCTION zcms_generate_alias(str text) RETURNS text AS $BODY$ DECLARE coDau TEXT; kDau TEXT; BEGIN coDau = \'áàảãạâấầẩẫậăắằẳẵặđéèẻẽẹêếềểễệíìỉĩịóòỏõọôốồổỗộơớờởỡợúùủũụưứừửữựýỳỷỹỵÁÀẢÃẠÂẤẦẨẪẬĂẮẰẲẴẶĐÉÈẺẼẸÊẾỀỂỄỆÍÌỈĨỊÓÒỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢÚÙỦŨỤƯỨỪỬỮỰÝỲỶỸỴ\'; kDau = \'aaaaaaaaaaaaaaaaadeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyyAAAAAAAAAAAAAAAAADEEEEEEEEEEEIIIIIOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYY\'; FOR i IN 0..length(coDau) LOOP str = replace(str, substr(coDau, i, 1), substr(kDau, i, 1)); END LOOP; RETURN (lower(str)); END; $BODY$ LANGUAGE plpgsql';
        } else {
            $queriesContent = file_get_contents(ROOT_PATH . '/app/install/sql/mysql/zcms.sql');
            $queries = explode("##ZCMS##", $queriesContent);
        }
        return $queries;
    }

    /**
     * Get current config
     *
     * @return array
     */
    private function _getConfig()
    {
        return include ROOT_PATH . '/app/config/config.php';
    }

    /**
     * Save config file
     *
     * @param array $config
     * @return bool
     */
    private function _saveConfig($config)
    {
        if ($config['database']['adapter'] == 'Mysql') {
            $config['database']['charset'] = 'utf8';
        } else {
            if (isset($config['database']['charset']) || empty($config['database']['charset'])) {
                unset($config['database']['charset']);
            }
        }
        $varExport = var_export($config, true);
        $content = "<?php\nreturn $varExport;\n";
        $result = file_put_contents(ROOT_PATH . '/app/config/config.php', $content);
        return $result;
    }

    /**
     * Rename old table if exists
     *
     * @param mixed $config
     * @return array
     */
    private function _renameOldTablesQueries($config)
    {
        $queries = [];
        if ($config->database->adapter == 'Mysql') {
            $queryGetAllTables = 'SHOW TABLES';
            $result = $this->db->fetchAll($queryGetAllTables, Db::FETCH_ASSOC);
            foreach ($result as $item) {
                $tableName = array_pop($item);
                $queries[] = 'ALTER TABLE ' . $tableName . ' RENAME TO backup_old_' . $tableName;
            }
        } elseif ($config->database->adapter == 'Postgresql') {
            if (isset($config->database->schema) && $config->database->schema) {
                $schema = $config->database->schema;
            } else {
                $schema = 'public';
            }
            $queryGetAllTables = "SELECT table_name FROM information_schema.tables WHERE table_schema='" . $schema . "' AND table_type='BASE TABLE'";
            $result = $this->db->fetchAll($queryGetAllTables, Db::FETCH_ASSOC);
            foreach ($result as $item) {
                $tableName = array_pop($item);
                $queries[] = 'ALTER TABLE ' . $tableName . ' RENAME TO backup_old_' . $tableName;
            }
        }
        return $queries;
    }

    /**
     * Check ENV
     */
    private function _checkENV()
    {
        //Check Phalcon framework installation
        $this->view->setVar('env_phalcon', extension_loaded('phalcon'));
        //Check APC cache
        $this->view->setVar('env_apc', extension_loaded('apc'));
        //Check memcache
        $this->view->setVar('env_memcache', extension_loaded('memcache'));
        //Check mbstring
        $this->view->setVar('env_mbstring', extension_loaded('mbstring'));
        //Check postgresql
        $this->view->setVar('env_PDO', extension_loaded('PDO'));
        $this->view->setVar('env_pdo_pgsql', extension_loaded('pdo_pgsql'));
    }
}