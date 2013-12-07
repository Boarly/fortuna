<?php

/**
 * auth controller class used for auth area pages
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_AuthController extends Zend_Controller_Action {

    /**
     * FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     * @access protected
     */
    protected $_flashMessenger = null;
    
    /**
     * method to initialize auth controller,
     * called at Zend_Controller_Action; objects used by
     * every single action is being initialized
     * 
     * @since 1.0
     * @uses Zend_Navigation
     *  
     */
    public function init() {
        $this->_helper->layout()->setLayout('auth');

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');

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
        ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap.min.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap-responsive.min.css'))
        ->appendStylesheet('http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600')
        ->appendStylesheet($this->view->baseUrl('assets/css/font-awesome.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/adminia.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/adminia-responsive.css'))
        ->appendStylesheet($this->view->baseUrl('assets/css/pages/login.css'));

        $navConfig    = new Zend_Config_Xml(APPLICATION_PATH . '/../data/navigations/auth.xml');
        $navContainer = new Zend_Navigation($navConfig);
        
        $this->view->navigation($navContainer)
                   ->menu()
                   ->setPartial(array('partials/horizontal-nav.phtml', 'fortuna'));

        $this->view
             ->headTitle()->exchangeArray(array());

        $this->view
             ->headTitle('Fortuna CMS')
             ->setSeparator(' | ');       
    }
    
    /**
     * action to redirect to auth/login
     * 
     * @since 1.0
     *  
     */
    public function indexAction() {
        $this->_redirect('/auth/login');
    }
    
    /**
     * action to display login form,
     * page is used to login to the admin area
     * 
     * @since 1.0
     * @uses Zend_Auth
     * @uses Zend_Navigation
     * @uses Zend_Session
     * @uses Fortuna_Model_AclRole
     *  
     */
    public function loginAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/admin');
        }

        if ($this->getRequest()->isPost() && $this->_getParam('email') != '') {
            $auth = Zend_Auth::getInstance();
            
            $dbAdapter   = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

            $um = new Fortuna_Model_User();

            $authAdapter
            ->setTableName($um->getTableName())
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)')
            ->setIdentity($this->_getParam('email'))
            ->setCredential($this->_getParam('password'));
             
            // Authentifizierungs Versuch, das Ergebnis abspeichern
            $result = $auth->authenticate($authAdapter);
             
            if (!$result->isValid()) {
                // Authentifizierung fehlgeschlagen; die genauen Gründe ausgeben
                foreach ($result->getMessages() as $message) {
                   $this->_flashMessenger->addMessage($message);
                }

                $this->view->messages = $this->_flashMessenger->getCurrentMessages();
                
            } else {
                if ($this->_getParam('remember')) {
                    Zend_Session::rememberMe(86400);
                }

                $storage = $auth->getStorage();
             
                // Die Identität als Objekt speichern, wobei die
                // Passwort Spalte unterdrückt wird
                $storage->write($authAdapter->getResultRowObject(
                    null,
                    'password'
                ));

                $arm                           = new Fortuna_Model_AclRole();
                $roleData                      = $arm->getRole($auth->getIdentity()->acl_role_id);
                $auth->getIdentity()->acl_role = $roleData['name'];

                $storage->write($auth->getIdentity());

                $this->_redirect('/admin');
            }
        }

        $this->view->headTitle($this->view->translate('Login'));
        $this->view->pageTitle = 'Login';
    }
    
    /**
     * action to logout the admin area
     * 
     * @since 1.0
     * @uses Zend_Auth
     *  
     */
    public function logoutAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            Zend_Auth::getInstance()->clearIdentity();
        }

        $this->_redirect('/auth/login');
    }
    
    /**
     * action to recover password
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     * @uses Fortuna_Form_Recover
     *  
     */
    public function recoverAction() {
        $um = new Fortuna_Model_User();


        if ($this->_getParam('link') != '') {
            if ($um->exists(array('password_link = "' . $this->_getParam('link') .'"'))) {
                 $um->recoverPassword($this->_getParam('link'));

                 $this->_redirect('/auth/login');
            } else {
                $this->_flashMessenger->addMessage('The password link you entered is invalid.');

                $this->view->messages = $this->_flashMessenger->getCurrentMessages();
            }
        }

        $this->view->form = new Fortuna_Form_Recover();

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) { 

            $um->recover($this->_getParam('email'));

            $this->_redirect('auth/recover');
        } else if ($this->getRequest()->isPost()) {
            $this->_flashMessenger->addMessage('There were some errors.');

            $this->view->messages = $this->_flashMessenger->getCurrentMessages();
        }

        $this->view->headTitle($this->view->translate('Recover Password'));
        $this->view->pageTitle = 'Recover Password';
    }
}