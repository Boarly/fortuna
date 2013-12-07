<?php

/**
 * filter to localize boolean field values from database
 * 
 * @package Fortuna
 * @subpackage Fortuna_Filter
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Filter_BooleanToLocalized implements Zend_Filter_Interface {
	const YES = 'yes';
	const NO  = 'no';
    
    /**
     * localize boolean $value
     * 
     * @param int|boolean $value boolean value to localize
     * @return string
     * @since 1.0 
     */
    public function filter($value) {
    	if ($value) {
    		return Zend_Registry::get('Zend_Translate')->_(self::YES);
    	}

        return Zend_Registry::get('Zend_Translate')->_(self::NO);
    }
}