<?php

namespace ZCMS\Core;

use Phalcon\Di as PDI;
use Phalcon\Exception;
use Phalcon\Mvc\Model as PModel;

/**
 * Class ZModel
 *
 * @package   ZCMS\Core
 * @since     0.0.1
 */
class ZModel extends PModel
{
    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $created_by;

    /**
     * @var string
     */
    public $updated_by;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * Execute before create
     */
    public function beforeCreate()
    {
        if (property_exists($this, 'created_at')) {
            $this->created_at = date("Y-m-d H:i:s");
        }

        if (property_exists($this, 'created_by')) {
            $this->created_by = $this->_getUserId();
        }

        if (property_exists($this, 'updated_by')) {
            $this->updated_by = $this->_getUserId();
        }

        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date("Y-m-d H:i:s");
        }
    }

    /**
     * Get current user
     *
     * @return mixed
     */
    private function _getUserId()
    {
        /**
         * @var \ZCMS\Core\ZSession $session
         */
        $session = $this->getDI()->get('session');
        $user = $session->get('auth');
        return $user['id'];
    }

    /**
     * Execute before update
     */
    public function beforeUpdate()
    {
        if (property_exists($this, 'updated_by')) {
            $this->updated_by = $this->_getUserId();
        }
        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date("Y-m-d H:i:s");
        }
    }

    /**
     * Re Order
     *
     * @param string $where
     * @param array $bind
     * @return bool
     * @throws \Phalcon\Exception
     */
    public function reOder($where = '', $bind = null)
    {
        if (property_exists($this, 'ordering')) {
            $options = [
                'order' => 'ordering ASC'
            ];
            if ($where) {
                $options['conditions'] = $where;
            }
            if ($bind) {
                $options['bind'] = $bind;
            }
            /**
             * @var $items \Phalcon\Mvc\Model[]
             */
            $items = $this->find($options);
            if (count($items)) {
                foreach ($items as $index => $item) {
                    if ($item->ordering != ($index + 1)) {
                        $item->ordering = $index + 1;
                        $item->save();
                    }
                }
            }
        } else {
            throw new Exception('This model does not support ordering');
        }
        return true;
    }

    /**
     * Mode up
     *
     * @param string $where
     * @param array $bind
     * @return bool
     * @throws Exception
     */
    public function moveUp($where = null, $bind = null)
    {
        $this->reOder();
        if (property_exists($this, 'ordering')) {
            $options = [
                'order' => 'ordering DESC'
            ];

            $options['conditions'] = 'ordering < ' . $this->ordering;

            if ($where) {
                $options['conditions'] .= ' AND ' . $where;
            }

            if ($bind) {
                $options['bind'] = $bind;
            }

            /**
             * @var $item \Phalcon\Mvc\Model
             */
            $item = $this->findFirst($options);

            $ordering = $this->ordering;

            if ($item) {
                $this->ordering = $item->ordering;
                $item->ordering = $ordering;
                $this->save();
                $item->save();
            }

        } else {
            throw new Exception(__('This model does not support ordering'));
        }
        return true;
    }

    /**
     * Move down
     *
     * @param string $where
     * @param array $bind
     * @return bool
     * @throws Exception
     */
    public function moveDown($where = null, $bind = null)
    {
        $this->reOder();
        if (property_exists($this, 'ordering')) {
            $options = [
                'order' => 'ordering ASC'
            ];

            $options['conditions'] = 'ordering > ' . $this->ordering;

            if ($where) {
                $options['conditions'] .= ' AND ' . $where;
            }

            if ($bind) {
                $options['bind'] = $bind;
            }

            $item = $this->findFirst($options);

            $ordering = $this->ordering;

            if ($item) {
                $this->ordering = $item->ordering;
                $item->ordering = $ordering;
                $this->save();
                $item->save();
            }
            return true;
        } else {
            throw new Exception('This model does not support ordering');
        }
    }

    /**
     * Get db
     *
     * @return \Phalcon\Db\Adapter\Pdo\Postgresql db
     */
    protected function _getDb()
    {
        return PDI::getDefault()->get('db');
    }
}