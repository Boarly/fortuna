<?php

/**
 * install controller class, serves as external installer
 * that can be deleted after first installation
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_InstallController extends Zend_Controller_Action {	
    
    /**
     * method to initialize install controller
     * 
     * @since 1.0
     * @uses Zend_Navigation
     * @uses Fortuna_Version 
     */
    public function init() {
    	$this->_helper->layout()->setLayout('install');

        $this->view->headMeta()
	        ->setCharset('UTF-8')
	        ->appendHttpEquiv('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no')
	        ->appendHttpEquiv('apple-mobile-web-app-capable', 'yes');

	        $this->view->headScript()
	        ->appendFile(
	            'http://html5shim.googlecode.com/svn/trunk/html5.js',
	            'text/javascript',
	            array('conditional' => 'lt IE 9')
	        )
	        ->appendFile(
	            'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
	            'text/javascript'
	        )
	        ->appendFile(
	            $this->view->baseUrl('assets/js/bootstrap.min.js'),
	            'text/javascript'
	        )
            ->appendFile(
                $this->view->baseUrl('assets/chosen/chosen.jquery.min.js'),
                'text/javascript'
            );

	        $this->view->headLink()
	        ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap.min.css'))
	        ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap-responsive.min.css'))
	        ->appendStylesheet('http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600')
	        ->appendStylesheet($this->view->baseUrl('assets/css/font-awesome.css'))
	        ->appendStylesheet($this->view->baseUrl('assets/css/adminia.css'))
	        ->appendStylesheet($this->view->baseUrl('assets/css/adminia-responsive.css'))
	        ->appendStylesheet($this->view->baseUrl('assets/css/pages/dashboard.css'))
            ->appendStylesheet($this->view->baseUrl('assets/chosen/chosen.css'));

        $this->view
             ->headTitle('Fortuna CMS')
             ->setSeparator(' | ');

        $navConfig    = new Zend_Config_Xml(APPLICATION_PATH . '/../data/navigations/install.xml');
        $navContainer = new Zend_Navigation($navConfig);
        
        $this->view->navigation($navContainer)
                   ->menu()
                   ->setPartial(array('partials/horizontal-nav.phtml', 'fortuna'));

        $this->view->version = Fortuna_Version::VERSION;
    }
    
    /**
     * action to migrate database schema
     * 
     * @since 1.0
     * @uses Zend_Registry
     * @uses Fortuna_Setup
     * @uses Zend_Controller_Front 
     */
    public function migrateAction() {
    	$this->_helper->viewRenderer->setNoRender();

    	$config = Zend_Registry::get('Zend_Config');
    	$setup  = new Fortuna_Setup($config);
    	$db     = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db');

    	$setup->migrate($db);

    	$this->_redirect('/install');
    }
    
    /**
     * action to install the Fortuna CMS
     * 
     * @since 1.0
     * @uses Zend_Registry
     * @uses Fortuna_Setup
     * @uses Fortuna_Form_Settings 
     */
    public function indexAction() {	
        $config = Zend_Registry::get('Zend_Config');

        $setup = new Fortuna_Setup($config);

        $this->view->php 		= $setup->getPHPVersionData();
	    $this->view->extensions = $setup->getExtensionData();
	    $this->view->writable   = $setup->getWritableData();
	    $this->view->schema     = $setup->getSchemaData();

        $this->view->form = new Fortuna_Form_Settings();

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $this->view->form->writeConfig();

            $this->_redirect('/install');
        }

        $this->view->headTitle($this->view->translate('Installation'));

        $this->view->placeholder('pageTitle')->set($this->view->translate('Installation'));
    }
    
    /**
     * action to display frequently asked questions
     * 
     * @since 1.0
     * @uses simplexml_load_file()
     * @uses Zend_Registry
     *  
     */
    public function faqAction() {
    	$faq = simplexml_load_file(Zend_Registry::get('Zend_Config')->fortuna->faq_path);
    	$this->view->faq = $faq->faq;

    	$this->view->headScript()
	        ->appendFile(
	            $this->view->baseUrl('/assets/js/faq.js'),
	            'text/javascript'
	        );

    	$this->view->headLink()
	         ->appendStylesheet($this->view->baseUrl('assets/css/pages/faq.css'));

    	$this->view->headTitle($this->view->translate('FAQ'));

        $this->view->placeholder('pageTitle')->set($this->view->translate('FAQ'));
    }

}

