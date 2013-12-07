<?php

/**
 * model of acl role privilege table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_AclRolePrivilege extends Fortuna_Db_Table_Abstract {
    
    /**
     *
     * name of table
     * 
     * @var    string
     * @access protected 
     */
	protected $_name    = 'acl_role_privilege';
    
    /**
     *
     * defination of tables' primary key
     * 
     * @var array
     * @access protected 
     */
	protected $_primary = array('acl_role_id', 'acl_privilege_id');
    
    /**
     *
     * get all roles with all privileges
     * 
     * @return array
     * @since 1.0 
     */
	public function getAllRolePrivileges() {
		$arm  = new Fortuna_Model_AclRole();
		$arom = new Fortuna_Model_AclResource();
		$apm  = new Fortuna_Model_AclPrivilege();

		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS rp', array())
				  ->join($arm->getTableName() . ' AS r', 'r.id = rp.acl_role_id', array('role' => 'name'))
				  ->join(
			        	$apm->getTableName() . ' AS p',
			        	'p.id = rp.acl_privilege_id',
			        	array(
			        	      'privilege' => 'name'
			        	)
			        )
					->join(
			        	$arom->getTableName() . ' AS ro',
			        	'ro.id = p.acl_resource_id',
			        	array(
			        	      'resource' => 'name'
			        	)
	        		)
	        		->where('rp.allowed = 1');

	    return $this->fetchAll($select)->toArray();
	}
    
    /**
     *
     * get all privileges of $roleId
     * 
     * @param int $roleId
     * @return array
     * @since 1.0
     */
	public function getRolePrivileges($roleId) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS r', array('acl_privilege_id'))
				  ->where('r.acl_role_id = ?', $roleId)
				  ->where('r.allowed = 1');	  

		$data = array();

		foreach ($this->fetchAll($select)->toArray() as $row) {
			$data[] = $row['acl_privilege_id'];
		}

		return $data;
	}
    
    /**
     *
     * add relation to database between $roleId
     * and $actionId
     * 
     * @param int $roleId
     * @param int $actionId
     * @return int
     * @since 1.0 
     */
	public function addRelation($roleId, $actionId) {
		return $this->insert(array(
	    	'acl_role_id'      => $roleId,
	    	'acl_privilege_id' => $actionId,
	    	'allowed'		   => 1
	    ));
	}
}