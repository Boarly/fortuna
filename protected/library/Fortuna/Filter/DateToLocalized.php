<?php

/**
 * filter to localize date field values from database
 * 
 * @package Fortuna
 * @subpackage Fortuna_Filter
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Filter_DateToLocalized implements Zend_Filter_Interface {
    
    /**
     * filter $value timestamp and get real date
     * 
     * @param int $value
     * @return string
     * @uses Zend_Date
     * @since 1.0
     */
    public function filter($value) {
    	$date = new Zend_Date($value);

    	return $date->get(Zend_Date::DATE_MEDIUM);
    }
}