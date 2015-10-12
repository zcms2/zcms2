<?php

namespace ZCMS\Core\Models\Behavior;

use Phalcon\Di as PDI,
    Phalcon\Mvc\Model\Behavior as PMMBehavior,
    Phalcon\Mvc\Model\BehaviorInterface,
    Phalcon\Mvc\ModelInterface,
    Phalcon\Db as PDb;

/**
 * Processing Nested Set Model on Phalcon
 *
 * Class NestedSetModel
 * @package ZCMS\Core\Models
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql _db
 */
class NestedSetModel extends PMMBehavior implements BehaviorInterface
{
    /**
     *  Class Behavior (extends NestedSetModel)
     * @var object
     */
    private $_owner;

    /**
     * Name of table in database
     *
     * @var string
     *
     */
    private $_table;

    /**
     * Name of database
     *
     * @var string
     */
    private $_db;

    /**
     * @var int
     */
    public $_parent = 0;

    /**
     * @var array|object
     */
    public $_data;

    /**
     * @var int
     */
    public $_id;

    /**
     * @var int
     */
    public $_orderArr;

    /**
     * Construct
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->_table = $params['table'];
        $this->_db = PDI::getDefault()->get('db');
    }

    /**
     * Update node info
     *
     * @return bool
     */
    public function updateNode()
    {
        $owner = $this->getOwner();

        $nodeInfo = $this->getNodeInfo($owner->id);

        $newParentId = $owner->parents;

        //Ban đầu chỉ thay đổi thông tin cơ bản mà chưa thay đổi parent, lft, rgt và level
        //Do vậy để lại cha như cũ.

        $owner->parents = $nodeInfo['parents'];
        $owner->lft = $nodeInfo['lft'];
        $owner->rgt = $nodeInfo['rgt'];
        $owner->level = $nodeInfo['level'];

        if (!$owner->save()) {
            return false;
        }

        if ($owner->parents == $newParentId) {
            return true;
        }

        if ($newParentId != null && $newParentId > 0) {
            if ($nodeInfo['parents'] != $newParentId) {
                return $this->moveNode($owner->id, $newParentId);
            }
        }

        return false;
    }

    /**
     * @param int $id
     * @param int $parent
     * @param array $options
     * @return bool
     */
    public function moveNode($id, $parent = 0, $options = null)
    {
        $this->_id = $id;
        $this->_parent = $parent;

        if ($options['position'] == 'right' || $options == null) {
            return $this->moveRight();
        }

        if ($options['position'] == 'left') {
            return $this->moveLeft();
        }

        if ($options['position'] == 'after') {
            return $this->moveAfter($options['brother_id']);
        }

        if ($options['position'] == 'before') {
            return $this->moveBefore($options['brother_id']);
        }

        return false;
    }

    /**
     * Move node to left position a unit on a level
     *
     * @return bool
     */
    public function moveUp()
    {
        $owner = $this->getOwner();
        $id = $owner->id;

        $nodeInfo = $this->getNodeInfo($id);
        $parentInfo = $this->getNodeInfo($nodeInfo['parents']);
        $sql = 'SELECT *
				   FROM ' . $this->_table . '
				   WHERE lft < ' . $nodeInfo['lft'] . '
				   AND parents = ' . $nodeInfo['parents'] . '
				   ORDER BY lft DESC
				   LIMIT 1
				   ';
        $nodeBrother = $this->_db->fetchOne($sql, PDb::FETCH_ASSOC);

        if (!empty($nodeBrother)) {
            $options = ['position' => 'before', 'brother_id' => $nodeBrother['id']];
            return $this->moveNode($id, $parentInfo['id'], $options);
        }

        return true;
    }

    /**
     * Move node to right position a unit on a level
     *
     * @return bool
     */
    public function moveDown()
    {
        $owner = $this->getOwner();
        $id = $owner->id;

        $nodeInfo = $this->getNodeInfo($id);
        $parentInfo = $this->getNodeInfo($nodeInfo['parents']);

        $sql = 'SELECT *
				   FROM ' . $this->_table . '
				   WHERE lft > ' . $nodeInfo['lft'] . '
				   AND parents = ' . $nodeInfo['parents'] . '
				   ORDER BY lft ASC
				   LIMIT 1
				   ';
        $nodeBrother = $this->_db->fetchOne($sql, PDb::FETCH_ASSOC);

        if (!empty($nodeBrother)) {
            $options = ['position' => 'after', 'brother_id' => $nodeBrother['id']];
            return $this->moveNode($id, $parentInfo['id'], $options);
        }

        return true;
    }


    /**
     * @param int $id ID of node which you want get info
     * @return Object
     */
    public function getParentNode($id)
    {
        $infoNode = $this->getNodeInfo($id);
        $parentId = $infoNode['parents'];
        $infoParentNode = $this->getNodeInfo($parentId);
        return $infoParentNode;
    }


    /**
     * Update ordering of all node in tree
     *
     * @param array $data An array store info tree
     * @param array $orderArr An array store info of ordering
     */
    public function orderTree($data, $orderArr)
    {

        $orderGroup = $this->orderGroup($data);
        foreach ($orderGroup as $key => $val) {
            $tmpVal = [];
            foreach ($val as $key2 => $val2) {
                $tmpVal[$key2] = $orderArr[$key2];
            }
            natsort($tmpVal);
            $orderGroup[$key] = $tmpVal;
        }

        foreach ($orderGroup as $key => $val) {
            $tmpVal = [];
            foreach ($val as $key2 => $val2) {
                $info = $this->getNodeByLeft($key2);
                $tmpVal[$info['id']] = $val2;
            }
            $orderGroup[$key] = $tmpVal;
        }

        foreach ($orderGroup as $key => $val) {
            foreach ($val as $key2 => $val2) {
                $nodeID = $key2;
                $parent = $key;
                $this->moveNode($nodeID, $parent);
            }
        }
    }

    /**
     * Get info of node
     *
     * @param int $left Left value of node
     * @return mixed
     */
    protected function getNodeByLeft($left)
    {
        $sql = 'SELECT * FROM ' . $this->_table . ' WHERE lft = ' . $left;
        return $this->_db->fetchOne($sql, PDb::FETCH_ASSOC);
    }

    /**
     * Create node groups
     *
     * @param array $data An array store info tree
     * @return array
     */
    public function orderGroup($data = null)
    {
        $orderArr2 = [];

        if ($data != null) {
            $orderArr = [];
            if (count($data) > 0) {
                foreach ($data as $val) {
                    $orderArr[$val['id']] = [];
                    if (isset($orderArr[$val['parents']])) {
                        $orderArr[$val['parents']][] = $val['lft'];
                    }
                }
                $orderArr2 = [];
                foreach ($orderArr as $key => $val) {
                    $tmp = $orderArr[$key];
                    if (count($tmp) > 0) {
                        $orderArr2[$key] = array_flip($val);
                    }
                }

            }
        }
        $this->_orderArr = $orderArr2;
        return $this->_orderArr;
    }

    /**
     * Create ordering of node by left value
     *
     * @param int $parent ID of parent of current node
     * @param int $left Left value of current node
     * @return int An value of ordering
     */
    public function getNodeOrdering($parent, $left)
    {
        $ordering = $this->_orderArr[$parent][$left] + 1;
        return $ordering;
    }

    /**
     * Create breadcrumbs for nodes of tree
     *
     * @param int $id ID of current node
     * @param int $level_stop Level of parent where you want get info
     * @return mixed
     */
    public function breadcrumbs($id, $level_stop = null)
    {
        $sql = 'SELECT parent.*
				FROM ' . $this->_table . ' AS node,
			         ' . $this->_table . ' AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
			      AND node.id = ' . $id;

        if (isset($level_stop)) {
            $sql .= ' AND parent.level > ' . $level_stop;
        }

        $sql .= ' ORDER BY node.lft';

        return $this->_db->fetchAll($sql, PDb::FETCH_ASSOC);
    }

    /**
     * Processing move node to before position of other node
     *
     * @param integer $brother_id
     * @return bool
     */
    protected function moveBefore($brother_id)
    {

        $infoMoveNode = $this->getNodeInfo($this->_id);

        $lftMoveNode = $infoMoveNode['lft'];
        $rgtMoveNode = $infoMoveNode['rgt'];
        $widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);

        $sqlReset = 'UPDATE ' . $this->_table . '
					 SET rgt = (rgt -  ' . $rgtMoveNode . '),
					 	 lft = (lft -  ' . $lftMoveNode . ')
					  WHERE lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
        $this->execute($sqlReset);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt -  ' . $widthMoveNode . ')
							WHERE rgt > ' . $rgtMoveNode;
        $this->execute($slqUpdateRight);

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft -  ' . $widthMoveNode . ')
						  WHERE lft > ' . $rgtMoveNode;
        $this->execute($slqUpdateLeft);

        $infoBrotherNode = $this->getNodeInfo($brother_id);

        $lftBrotherNode = $infoBrotherNode['lft'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft +  ' . $widthMoveNode . ')
						  WHERE lft >= ' . $lftBrotherNode . '
						  AND rgt>0';
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt +  ' . $widthMoveNode . ')
							WHERE rgt >= ' . $lftBrotherNode;
        $this->execute($slqUpdateRight);

        $infoParentNode = $this->getNodeInfo($this->_parent);
        $levelMoveNode = $infoMoveNode['level'];
        $levelParentNode = $infoParentNode['level'];
        $newLevelMoveNode = $levelParentNode + 1;

        $slqUpdateLevel = 'UPDATE ' . $this->_table . '
						  SET level = (level  -  ' . $levelMoveNode . ' + ' . $newLevelMoveNode .
            ')
						  WHERE rgt <= 0';
        $this->execute($slqUpdateLevel);

        $newParent = $infoParentNode['id'];
        $newLeft = $infoBrotherNode['lft'];
        $newRight = $infoBrotherNode['lft'] + $widthMoveNode - 1;
        $slqUpdateParent = 'UPDATE ' . $this->_table . '
						  SET parents = ' . $newParent . ',
						      lft = ' . $newLeft . ',
						  	  rgt = ' . $newRight . '
						  WHERE id = ' . $this->_id;
        $this->execute($slqUpdateParent);

        $slqUpdateNode = 'UPDATE ' . $this->_table . '
						  SET rgt = (rgt +  ' . $newRight . '),
						   	  lft = (lft +  ' . $newLeft . ')
						  WHERE rgt <0';
        $this->execute($slqUpdateNode);

        return true;
    }

    /**
     * Processing move node to after position of other node
     *
     * @param integer $brother_id
     * @return bool
     */
    protected function moveAfter($brother_id)
    {

        $infoMoveNode = $this->getNodeInfo($this->_id);

        $lftMoveNode = $infoMoveNode['lft'];
        $rgtMoveNode = $infoMoveNode['rgt'];
        $widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);


        $sqlReset = 'UPDATE ' . $this->_table . '
					 SET rgt = (rgt -  ' . $rgtMoveNode . '),
					 	 lft = (lft -  ' . $lftMoveNode . ')
					  WHERE lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;

        $this->execute($sqlReset);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt -  ' . $widthMoveNode . ')
							WHERE rgt > ' . $rgtMoveNode;
        $this->execute($slqUpdateRight);

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft -  ' . $widthMoveNode . ')
						  WHERE lft > ' . $rgtMoveNode;
        $this->execute($slqUpdateLeft);

        $infoBrotherNode = $this->getNodeInfo($brother_id);

        $rgtBrotherNode = $infoBrotherNode['rgt'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft +  ' . $widthMoveNode . ')
						  WHERE lft > ' . $rgtBrotherNode . '
						  AND rgt>0';
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt +  ' . $widthMoveNode . ')
							WHERE rgt > ' . $rgtBrotherNode;
        $this->execute($slqUpdateRight);

        $infoParentNode = $this->getNodeInfo($this->_parent);

        $levelMoveNode = $infoMoveNode['level'];
        $levelParentNode = $infoParentNode['level'];
        $newLevelMoveNode = $levelParentNode + 1;

        $slqUpdateLevel = 'UPDATE ' . $this->_table . '
						  SET level = (level  -  ' . $levelMoveNode . ' + ' . $newLevelMoveNode .
            ')
						  WHERE rgt <= 0';
        $this->execute($slqUpdateLevel);

        $newParent = $infoParentNode['id'];
        $newLeft = $infoBrotherNode['rgt'] + 1;
        $newRight = $infoBrotherNode['rgt'] + $widthMoveNode;

        $slqUpdateParent = 'UPDATE ' . $this->_table . '
						  SET parents = ' . $newParent . ',
						      lft = ' . $newLeft . ',
						  	  rgt = ' . $newRight . '
						  WHERE id = ' . $this->_id;
        $this->execute($slqUpdateParent);

        $slqUpdateNode = 'UPDATE ' . $this->_table . '
						  SET rgt = (rgt +  ' . $newRight . '),
						   	  lft = (lft +  ' . $newLeft . ')
						  WHERE rgt <0';
        $this->execute($slqUpdateNode);

        return true;
    }

    /**
     * Processing move node to left position of other node
     *
     * @return bool
     */
    protected function moveLeft()
    {

        $infoMoveNode = $this->getNodeInfo($this->_id);

        $lftMoveNode = $infoMoveNode['lft'];
        $rgtMoveNode = $infoMoveNode['rgt'];
        $widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);

        $sqlReset = 'UPDATE ' . $this->_table . '
					 SET rgt = (rgt -  ' . $rgtMoveNode . '),
					 	 lft = (lft -  ' . $lftMoveNode . ')
					  WHERE lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
        $this->execute($sqlReset);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt -  ' . $widthMoveNode . ')
							WHERE rgt > ' . $rgtMoveNode;
        $this->execute($slqUpdateRight);

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft -  ' . $widthMoveNode . ')
						  WHERE lft > ' . $rgtMoveNode;
        $this->execute($slqUpdateLeft);

        $infoParentNode = $this->getNodeInfo($this->_parent);
        $lftParentNode = $infoParentNode['lft'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft +  ' . $widthMoveNode . ')
						  WHERE lft > ' . $lftParentNode . '
						  AND rgt > 0
						  ';
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt +  ' . $widthMoveNode . ')
							WHERE rgt > ' . $lftParentNode;
        $this->execute($slqUpdateRight);

        $levelMoveNode = $infoMoveNode['level'];
        $levelParentNode = $infoParentNode['level'];
        $newLevelMoveNode = $levelParentNode + 1;

        $slqUpdateLevel = 'UPDATE ' . $this->_table . '
						  SET level = (level  -  ' . $levelMoveNode . ' + ' . $newLevelMoveNode .
            ')
						  WHERE rgt <= 0';
        $this->execute($slqUpdateLevel);


        $newParent = $infoParentNode['id'];
        $newLeft = $infoParentNode['lft'] + 1;
        $newRight = $infoParentNode['lft'] + $widthMoveNode;
        $slqUpdateParent = 'UPDATE ' . $this->_table . '
						  SET parents = ' . $newParent . ',
						      lft = ' . $newLeft . ',
						  	  rgt = ' . $newRight . '
						  WHERE id = ' . $this->_id;
        $this->execute($slqUpdateParent);


        $slqUpdateNode = 'UPDATE ' . $this->_table . '
						  SET rgt = (rgt +  ' . $newRight . '),
						   	  lft = (lft +  ' . $newLeft . ')
						  WHERE rgt <0';
        $this->execute($slqUpdateNode);

        return true;
    }

    /**
     * Processing move node to right position of other node
     *
     * @return bool
     */
    protected function moveRight()
    {

        $infoMoveNode = $this->getNodeInfo($this->_id);

        $lftMoveNode = $infoMoveNode['lft'];
        $rgtMoveNode = $infoMoveNode['rgt'];
        $widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);

        $sqlReset = 'UPDATE ' . $this->_table . '
					 SET rgt = (rgt -  ' . $rgtMoveNode . '),
					 	 lft = (lft -  ' . $lftMoveNode . ')
					  WHERE lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
        $this->execute($sqlReset);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt -  ' . $widthMoveNode . ')
							WHERE rgt > ' . $rgtMoveNode;
        $this->execute($slqUpdateRight);

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft -  ' . $widthMoveNode . ')
						  WHERE lft > ' . $rgtMoveNode;
        $this->execute($slqUpdateLeft);

        $infoParentNode = $this->getNodeInfo($this->_parent);
        $rgtParentNode = $infoParentNode['rgt'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft +  ' . $widthMoveNode . ')
						  WHERE lft >= ' . $rgtParentNode . '
						  AND rgt > 0
						  ';
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						   SET rgt = (rgt +  ' . $widthMoveNode . ')
							WHERE rgt >= ' . $rgtParentNode;
        $this->execute($slqUpdateRight);

        $levelMoveNode = $infoMoveNode['level'];
        $levelParentNode = $infoParentNode['level'];
        $newLevelMoveNode = $levelParentNode + 1;

        $slqUpdateLevel = 'UPDATE ' . $this->_table . '
						  SET level = (level  -  ' . $levelMoveNode . ' + ' . $newLevelMoveNode .
            ')
						  WHERE rgt <= 0';
        $this->execute($slqUpdateLevel);

        $newParent = $infoParentNode['id'];
        $newLeft = $infoParentNode['rgt'];
        $newRight = $infoParentNode['rgt'] + $widthMoveNode - 1;
        $slqUpdateParent = 'UPDATE ' . $this->_table . '
						  SET parents = ' . $newParent . ',
						      lft = ' . $newLeft . ',
						  	  rgt = ' . $newRight . '
						  WHERE id = ' . $this->_id;
        $this->execute($slqUpdateParent);

        $slqUpdateNode = 'UPDATE ' . $this->_table . '
						  SET rgt = (rgt +  ' . $newRight . '),
						   	  lft = (lft +  ' . $newLeft . ')
						  WHERE rgt <0';
        $this->execute($slqUpdateNode);

        return true;
    }

    /**
     * Insert a new node to tree (move: left - right - before - after)
     *
     * @param array $options Array store info of new node
     * @return bool
     */
    public function insertNode($options = null)
    {
        $this->_parent = $this->getOwner()->parents;

        if ($options['position'] == 'right' || $options == null)
            return $this->insertRight();

        if ($options['position'] == 'left')
            return $this->insertLeft();

        if ($options['position'] == 'after')
            return $this->insertAfter($options['brother_id']);

        if ($options['position'] == 'before')
            return $this->insertBefore($options['brother_id']);
        return true;
    }

    /**
     * Execute query
     *
     * @param $sql
     * @return mixed
     */
    public function execute($sql)
    {
        $success = $this->_db->query($sql);
        return $success;
    }

    /**
     * Insert a new node to right position of other node
     *
     * @return bool
     */
    protected function insertRight()
    {
        $owner = $this->getOwner();

        $parentInfo = $owner::findFirst($this->_parent)->toArray();

        $parentRight = $parentInfo['rgt'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = lft + 2
						  WHERE lft > ' . $parentRight;
        $this->execute($slqUpdateLeft);


        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						  SET rgt = rgt + 2
						  WHERE rgt >= ' . $parentRight;
        $this->execute($slqUpdateRight);

        $owner->lft = $parentRight;
        $owner->rgt = $parentRight + 1;
        $owner->level = $parentInfo['level'] + 1;

        $success = $owner->save();

        return $success;

    }

    /**
     * Insert a new node to left position of other node
     *
     * @return bool
     */
    protected function insertLeft()
    {
        $owner = $this->getOwner();

        $parentInfo = $owner::findFirst($this->_parent)->toArray();
        $parentLeft = $parentInfo['lft'];

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = lft + 2
						  WHERE lft > ' . $parentLeft;
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						  SET rgt = rgt + 2
						  WHERE rgt > ' . ($parentLeft + 1);
        $this->execute($slqUpdateRight);

        $owner->lft = $$parentLeft + 1;
        $owner->rgt = $parentLeft + 2;
        $owner->level = $parentInfo['level'] + 1;

        $success = $owner->save();
        return $success;
    }

    /**
     * Insert a new node to after position of other node
     *
     * @param int $brother_id ID of node which you want insert new node to after position
     * @return bool
     */
    protected function insertAfter($brother_id)
    {

        $owner = $this->getOwner();

        $parentInfo = $owner::findFirst($this->_parent)->toArray();
        $brotherInfo = $owner::findFirst($brother_id)->toArray();

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = lft + 2
						  WHERE lft > ' . $brotherInfo['rgt'];

        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						  SET rgt = rgt + 2
						  WHERE rgt > ' . $brotherInfo['rgt'];

        $this->execute($slqUpdateRight);

        $owner->lft = $brotherInfo['rgt'] + 1;
        $owner->rgt = $brotherInfo['rgt'] + 2;
        $owner->level = $parentInfo['level'] + 1;

        $success = $owner->save();
        return $success;
    }

    /**
     * Insert a new node to before position of other node
     *
     * @param int $brother_id
     * @return bool
     */
    protected function insertBefore($brother_id)
    {

        $owner = $this->getOwner();

        $parentInfo = $owner::findFirst($this->_parent)->toArray();
        $brotherInfo = $owner::findFirst($brother_id)->toArray();

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = lft + 2
						  WHERE lft >= ' . $brotherInfo['lft'];

        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						  SET rgt = rgt + 2
						  WHERE rgt >= ' . ($brotherInfo['lft'] + 1);

        $this->execute($slqUpdateRight);

        $owner->lft = $brotherInfo['lft'];
        $owner->rgt = $brotherInfo['lft'] + 1;
        $owner->level = $parentInfo['level'] + 1;

        $success = $owner->save();
        return $success;
    }

    /**
     * Create a string from a data array
     *
     * @param array $data
     * @return string
     */
    protected function createUpdateQuery($data)
    {
        $result = '';
        if (count($data) > 0) {

            $i = 1;
            foreach ($data as $key => $val) {
                if ($i == 1) {
                    $result .= " " . $key . " = '" . $val . "' ";
                } else {
                    $result .= " ," . $key . " = '" . $val . "' ";
                }
                $i++;
            }
        }
        return $result;
    }

    /**
     * Create a string from a data array
     *
     * @param array $data
     * @return array
     */
    public function createInsertQuery($data)
    {
        $cols = '';
        $values = '';
        if (count($data) > 0) {

            $i = 1;
            foreach ($data as $key => $val) {
                if ($i == 1) {
                    $cols .= "`" . $key . "`";
                    $values .= "'" . $val . "'";
                } else {
                    $cols .= ",`" . $key . "`";
                    $values .= ",'" . $val . "'";
                }
                $i++;
            }
        }
        $result['cols'] = $cols;
        $result['values'] = $values;
        return $result;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }

    /**
     * Calculate total nodes
     * @param int $parents
     * @return int Total nodes
     */
    public function totalNode($parents = 0)
    {
        $sql = 'SELECT lft,rgt FROM ' . $this->_table . ' WHERE parents = ' . $parents;
        $result = $this->_db->fetchOne($sql, PDb::FETCH_ASSOC);
        $lft = $result['lft'];
        $rgt = $result['rgt'];
        $total = ($rgt - $lft + 1) / 2;
        return $total;
    }

    /**
     * @param int $lft
     * @param int $rgt
     * @return int
     */
    public function widthNode($lft, $rgt)
    {
        $width = $rgt - $lft + 1;
        return $width;
    }

    /**
     * Remove node
     *
     * @param $id
     * @param string $options
     */
    public function removeNode($id, $options = 'branch')
    {
        $this->_id = $id;

        if ($options == 'branch')
            $this->removeBranch();
        if ($options == 'node')
            $this->removeOne();
    }

    /**
     * Remove a branch of tree
     *
     * @return true
     */
    protected function removeBranch()
    {

        $infoNodeRemove = $this->getNodeInfo($this->_id);

        $rgtNodeRemove = $infoNodeRemove['rgt'];
        $lftNodeRemove = $infoNodeRemove['lft'];
        $widthNodeRemove = $this->widthNode($lftNodeRemove, $rgtNodeRemove);

        $slqDelete = 'DELETE FROM ' . $this->_table . '
					  WHERE lft BETWEEN ' . $lftNodeRemove . ' AND ' . $rgtNodeRemove;
        $this->execute($slqDelete);

        $slqUpdateLeft = 'UPDATE ' . $this->_table . '
						  SET lft = (lft - ' . $widthNodeRemove . ')
						  WHERE lft > ' . $rgtNodeRemove;
        $this->execute($slqUpdateLeft);

        $slqUpdateRight = 'UPDATE ' . $this->_table . '
						  SET rgt = (rgt - ' . $widthNodeRemove . ')
						  WHERE rgt > ' . $rgtNodeRemove;
        $this->execute($slqUpdateRight);

        return true;
    }

    /**
     * Remove current node
     *
     * @return true
     */
    protected function removeOne()
    {
        $nodeInfo = $this->getNodeInfo($this->_id);

        $sql = 'SELECT id
				FROM ' . $this->_table . '
				WHERE parents = ' . $nodeInfo['id'] . '
				ORDER BY lft ASC ';
        $childIds = $this->_db->fetchOne($sql, PDb::FETCH_NUM);

        rsort($childIds);

        if (count($childIds) > 0) {
            foreach ($childIds as $val) {
                $id = $val;
                $parent = $nodeInfo['parents'];
                $options = ['position' => 'after', 'brother_id' => $nodeInfo['id']];
                $this->moveNode($id, $parent, $options);
            }
            $this->removeNode($nodeInfo['id']);
        }

        return true;
    }

    /**
     * Get info node of tree
     *
     * @param $id
     * @return Object
     */
    public function getNodeInfo($id)
    {
        $owner = $this->getOwner();
        return $owner::findFirst($id)->toArray();
    }

    /**
     * Get current node
     *
     * @return Object
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * Set current node
     *
     * @param ModelInterface $owner
     */
    public function setOwner($owner)
    {
        $this->_owner = $owner;
    }

    /**
     * @param ModelInterface $model
     * @param string $method
     * @param null $arguments
     * @return mixed|null|string|void
     */
    public function missingMethod(ModelInterface $model, $method, $arguments = null)
    {
        if (method_exists($this, $method)) {
            $this->setOwner($model);
            $result = call_user_func_array([$this, $method], $arguments);
            if ($result === null) {
                return '';
            }
            return $result;
        }
        return null;
    }

    /**
     * Get tree
     *
     * @param int $parents
     * @param string $items
     * @param array $exclude_id
     * @param int $level
     * @return array
     */
    public function listItem($parents = 0, $items = 'all', $exclude_id = null, $level = 0)
    {

        $lftExclude = null;
        $rgtExclude = null;

        $owner = $this->getOwner();
        $parentNodeInfo = $owner->findFirst($parents)->toArray();

        $sqlItems = 'SELECT node.*
					 FROM ' . $this->_table . ' AS node ';

        if ($items == 'all') {
            $sqlItemsLR = ' WHERE node.lft >= ' . $parentNodeInfo['lft'] . '
					       AND node.rgt <= ' . $parentNodeInfo['rgt'] . ' ';
        } else {
            $sqlItemsLR = ' WHERE node.lft > ' . $parentNodeInfo['lft'] . '
					       AND node.rgt < ' . $parentNodeInfo['rgt'] . ' ';
        }

        if ($exclude_id != null && (int)$exclude_id > 0) {
            $sqlExclude = '	SELECT lft, rgt
					   		FROM ' . $this->_table . '
					   		WHERE id = ' . $exclude_id;
            $rowExclude = $this->_db->fetchOne($sqlExclude, PDb::FETCH_OBJ);
            $lftExclude = $rowExclude->lft;
            $rgtExclude = $rowExclude->rgt;
        }

        $sqlItems .= $sqlItemsLR;

        if ($level != 0) {
            $sqlItems .= ' AND node.level <=  ' . $level . ' ';
        }

        $sqlItems .= ' ORDER BY node.lft ';

        $rows = $this->_db->fetchAll($sqlItems, PDb::FETCH_OBJ);

        $dataArr = [];
        if ($rows && isset($rowExclude)) {
            foreach ($rows as $row) {
                if ($row->lft < $lftExclude || $row['lft'] > $rgtExclude) {
                    $dataArr[] = $row;
                }
            }
        } else {
            return $rows;
        }
        return $dataArr;
    }

    /**
     * Get tree
     *
     * @param int $parents
     * @param string $items
     * @param array $exclude_id
     * @param int $level
     * @return array
     */
    public function listItemOLD($parents = 0, $items = 'all', $exclude_id = null, $level = 0)
    {

        $owner = $this->getOwner();
        $parentNodeInfo = $owner->findFirst($parents)->toArray();

        $sqlItems = 'SELECT node.*
					 FROM ' . $this->_table . ' AS node ';

        if ($items == 'all') {
            $sqlItemsLR = ' WHERE node.lft >= ' . $parentNodeInfo['lft'] . '
					       AND node.rgt <= ' . $parentNodeInfo['rgt'] . ' ';
        } else {
            $sqlItemsLR = ' WHERE node.lft > ' . $parentNodeInfo['lft'] . '
					       AND node.rgt < ' . $parentNodeInfo['rgt'] . ' ';
        }

        if ($exclude_id != null && (int)$exclude_id > 0) {
            $sqlExclude = '	SELECT lft, rgt
					   		FROM ' . $this->_table . '
					   		WHERE id = ' . $exclude_id;
            $rowExclude = $this->_db->fetchOne($sqlExclude, PDb::FETCH_ASSOC);
            $lftExclude = $rowExclude['lft'];
            $rgtExclude = $rowExclude['rgt'];
        }

        $sqlItems .= $sqlItemsLR;

        if ($level != 0) {
            $sqlItems .= ' AND node.level <=  ' . $level . ' ';
        }

        $sqlItems .= ' ORDER BY node.lft ';

        $rows = $this->_db->fetchAll($sqlItems, PDb::FETCH_ASSOC);

        $dataArr = [];
        if ($rows && isset($rowExclude) && isset($lftExclude) && isset($rgtExclude)) {
            foreach ($rows as $row) {
                if ($row['lft'] < $lftExclude || $row['lft'] > $rgtExclude) {
                    $dataArr[] = $row;
                }
            }
        } else {
            return $rows;
        }
        return $dataArr;
    }

    /**
     * Destruct function
     */
    public function __destruct()
    {

    }

}