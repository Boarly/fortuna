<?php

/**
 * model of acl resources table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_AclResource extends Fortuna_Db_Table_Abstract {
    
    /**
     *
     * name of table
     * 
     * @var string
     * @access protected 
     */
    protected $_name    = 'acl_resource';
    
    /**
     *
     * primary key of table
     * 
     * @var    string
     * @access protected
     */
    protected $_primary = 'id';
    
    /**
     *
     * get all resources
     * 
     * @return array
     * @since 1.0 
     */
    public function getResources() {
        $select = $this
                    ->select()
                    ->from($this->_name . ' AS r', array('*'));

        return $this->fetchAll($select)->toArray();
    }
}