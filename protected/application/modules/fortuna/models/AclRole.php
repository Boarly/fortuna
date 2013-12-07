<?php

/**
 * model of acl roles table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_AclRole extends Fortuna_Db_Table_Abstract {
    
    /**
     *
     * name of table
     * 
     * @var string
     * @access protected
     *  
     */
    protected $_name    = 'acl_role';
    
    /**
     *
     * name of primary eky
     * 
     * @var string
     * @access protected 
     */
    protected $_primary = 'id';

    
    /**
     *
     * method to get all roles
     * 
     * @return array
     * @since 1.0 
     */
    public function getRoles() {
        $select = $this
                    ->select()
                    ->from($this->_name . ' AS r', array('*'));


        return $this->fetchAll($select)->toArray();
    }
    
    /**
     *
     * get all roles in selectbox format
     * 
     * @return array
     * @since 1.0 
     */
    public function getRolesSelect() {

        $select = $this
                    ->select()
                    ->from($this->_name . ' AS r', array('*'));

        $data = array();

        foreach ($this->fetchAll($select)->toArray() as $row) {
                $data[$row['id']] =  $row['name'];
        }

        return $data;
    }
    
    /**
     *
     * get role by $id
     * 
     * @return array
     * @param $id id of role to fetch
     * @since 1.0 
     */
    public function getRole($id) {
        $select = $this
                    ->select()
                    ->from($this->_name . ' AS r', array('*'))
                    ->where('r.id = ?', $id);

        $res = $this->fetchRow($select);

        if ($res instanceof Zend_Db_Table_Row) {
            $data = $res->toArray();

            $aram 				     = new Fortuna_Model_AclRolePrivilege();
            $data['role_privileges'] = $aram->getRolePrivileges($id);

            return $data;
        } else {
            return array();
        }
    }
    
    /**
     *
     * save role to database via insert or update
     * 
     * @param array    $data data to save
     * @param int|null $id   id where to save role
     * @since 1.0 
     */
    public function saveRole(array $data, $id = null) {
        if (empty($id)) {
            $id = $this->insert(array('name' => $data['name']));
        } else {
            $this->update(array('name' => $data['name']), 'id = ' . $id);
        }

        $aram = new Fortuna_Model_AclRolePrivilege();
        $aram->delete('acl_role_id = ' . $id);

        if (!empty($data['role_privileges'])) {
            foreach ($data['role_privileges'] as $actionId) {
                    $aram->addRelation($id, $actionId);
            }
        }
    }
}