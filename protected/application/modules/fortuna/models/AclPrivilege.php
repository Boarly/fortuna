<?php

/**
 * model of acl privileges table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_AclPrivilege extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of table
     * 
     * @var string
     * @access protected 
     */
    protected $_name    = 'acl_privilege';
    
    /**
     * primary key of table
     * 
     * @var string
     * @access protected 
     */
    protected $_primary = 'id';

   /**
    * get array of all privileges
    * in a format used in selectboxes
    * 
    * @since 1.0
    * @return array
    */
    public function getPrivilegesSelect() {
        $acm = new Fortuna_Model_AclResource();

        $select = $this
                            ->select()
                            ->setIntegrityCheck(false)
                            ->from($acm->getTableName() . ' AS c', array('controller' => 'name'))
                            ->joinLeft($this->getTableName() . ' AS a', 'c.id = a.acl_resource_id', array('action' => 'name', 'id'));

        $data = array();

        foreach ($this->fetchAll($select)->toArray() as $row) {
                $data[$row['id']] =  $row['controller'] . ' - ' . $row['action'];
        }

        return $data;
    }
    
   /**
    * get privileges by $resourceId
    * 
    * @param $resourceId id of resource to look up
    * @return array
    * @since 1.0 
    */
    public function getResourcePrivileges($resourceId) {
        $select = $this
                ->select()
                ->setIntegrityCheck(false)
                ->from($this->_name . ' AS p', array('*'))
                ->where('p.acl_resource_id = ?', $resourceId);

        return $this->fetchAll($select)->toArray();
    }

}