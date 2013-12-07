<?php

/**
 * controller class to output error messages like
 * exceptions (application errors) or errors
 * like "page not found"
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_ErrorController extends Zend_Controller_Action {
        
    /**
     * method to initialize the error controller,
     * initiates widely uses components
     * 
     * @since 1.0
     *  
     */
    public function init() {
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
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',
            'text/javascript'
        );

        $this->view->headLink()
        ->appendStylesheet($this->view->baseUrl('/assets/css/bootstrap.min.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap-responsive.min.css'))
        ->appendStylesheet('http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600')
        ->appendStylesheet($this->view->baseUrl('assets/css/font-awesome.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/adminia.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/adminia-responsive.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/pages/error.css'));
    }
    
    /**
     * action to handle errors
     * 
     * @since 1.0
     * @uses Zend_Controller_Plugin_ErrorHandler
     *  
     */
    public function errorAction() {

        $this->view->layout()->disableLayout();

        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 Fehler -- Controller oder Aktion nicht gefunden
                $this->getResponse()
                        ->setRawHeader('HTTP/1.1 404 Not Found');

                $this->view->title = $this->view->translate('404 Not Found');

                // ... Ausgabe für die Anzeige erzeugen...
                break;
            default:

                $this->view->title = $this->view->translate('Application Error');

                break;
        }


        $this->view->message = $errors->exception->getMessage();

        $this->view
        ->headTitle()->exchangeArray(array());

        $this->view
        ->headTitle('Fortuna CMS')
        ->headTitle($this->view->translate($this->view->title))
        ->setSeparator(' | ');

        $this->view->placeholder('pageTitle')->set($this->view->translate($this->view->title));

        die($this->view->render('error/error.phtml'));
    } 

}
