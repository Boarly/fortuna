<?php

/**
 * abstract db table (model) class for
 * fortuna module
 * 
 * @package Fortuna
 * @subpackage Fortuna_Db_Table
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
abstract class Fortuna_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    
    /**
     * setup $this->_name of table
     * with table prefix
     * 
     * @uses Zend_Registry
     * @since 1.0
     * @access protected
     *  
     */
    protected function _setupTableName() {
        parent::_setupTableName();

        $prefix 	  = Zend_Registry::get('Zend_Config')->fortuna->table_prefix;
        $this->_name = $prefix . $this->_name;
    }
    
    /**
     * get the name of the table
     * 
     * @return string
     * @since 1.0
     */
    public function getTableName() {
        return $this->_name;
    }
    
    /**
     * check of $where exists in the table
     * 
     * @param  array $where condition to check
     * @since 1.0
     * @return boolean 
     */
    public function exists(array $where) {
        $select = $this
                ->select()
                ->from($this->_name . ' AS t', array('*'))
                ->limit(1);

        foreach ($where as $condition) {
            $select->where($condition);
        }

        $res = $this->fetchRow($select);

        if ($res instanceof Zend_Db_Table_Row) {
            return true;
        }

        return false;
    }
}
