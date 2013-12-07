<?php

/**
 * bootstrap class for fortuna module
 * 
 * @package Fortuna
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    /**
     * method to register bootstrap options
     * at Zend_Registry key Zend_Config
     * 
     * @return Zend_Config
     * @access protected
     */
    protected function _initConfig() {
        $config = new Zend_Config($this->getOptions(), true);

        Zend_Registry::set('Zend_Config', $config);

        return $config;
    }
    
    /**
     * method to init Fortuna_Filter namespace as a default
     * Zend_Filter namespace
     * 
     * @since 1.0
     * @uses Zend_Filter
     */
    protected function _initFilters() {
        Zend_Filter::addDefaultNamespaces(array('Fortuna_Filter'));
    }

    
    /**
     * method to init access control list plugin
     * 
     * @uses Zend_Controller_Front
     * @uses Fortuna_Controller_Plugin_Acl 
     */
    protected function _initAcl() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Fortuna_Controller_Plugin_Acl());
    }

}