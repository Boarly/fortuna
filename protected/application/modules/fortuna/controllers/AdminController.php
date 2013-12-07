<?php

/**
 * admin controller class for accessing the admin area
 * this class is intialized if /admin is accessed
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_AdminController extends Zend_Controller_Action {

    /**
     * method to initialize controller,
     * called at Zend_Controller_Action
     * 
     * @since 1.0
     * @uses Zend_Auth
     * @uses Zend_Navigation
     * @uses Fortuna_Version
     *  
     */
    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/login');
        }

        $this->view
             ->headTitle()->exchangeArray(array());

        $this->view
             ->headTitle('Fortuna CMS')
             ->setSeparator(' | ');

        $this->_helper->layout()->setLayout('admin');

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
            'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.1/jquery.dataTables.min.js',
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
        ->appendStylesheet($this->view->baseUrl('assets/css/datatables.css'))
        ->appendStylesheet($this->view->baseUrl('assets/chosen/chosen.css'));

        $navConfig    = new Zend_Config_Xml(APPLICATION_PATH . '/../data/navigations/admin.xml');
        $navContainer = new Zend_Navigation($navConfig);
        
        $this->view->navigation($navContainer)
                   ->setAcl(Zend_Registry::get('Zend_Acl'))
                   ->setRole(Zend_Auth::getInstance()->getIdentity()->acl_role)
                   ->menu()
                   ->setPartial(array('partials/main-nav.phtml', 'fortuna'));

        $this->view->identity = Zend_Auth::getInstance()->getIdentity();

        $this->view->version = Fortuna_Version::VERSION;
    }
    
    /**
     * action to display list of
     * available roles
     * 
     * @since 1.0
     * @uses Fortuna_Model_AclRole
     *  
     */
    public function rolesAction() {
        $arm = new Fortuna_Model_AclRole();

        $this->view->roles = $arm->getRoles();

        $this->view->headTitle($this->view->translate('Roles'));
        $this->view->pageTitle = 'Roles';
        $this->view->placeholder('pageIcon')->set('lock');
    }
    
    /**
     * method to show and edit
     * and existing role or to add a new one
     * 
     * @since 1.0
     * @uses Fortuna_Form_AclRole
     *  
     */
    public function showRoleAction() {
        if ($this->_hasParam('cancel')) {
            $this->_redirect('admin/roles');
        }

        $arm = new Fortuna_Model_AclRole();

        $this->view->form = new Fortuna_Form_AclRole();

        if ($this->_getParam('id') != '') {
            $data = $arm->getRole($this->_getParam('id'));

            if (!$data || $data['standard']) {
                $this->_redirect('admin/roles');
            }

            $this->view->form->setDefaults($data);

            $this->view->data = $data;
        }

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $arm->saveRole($_POST, $this->_getParam('id'));

            $this->_redirect('admin/roles');
        }

        $this->view->headTitle($this->view->translate('Roles'));
        $this->view->pageTitle = 'Roles';
        $this->view->placeholder('pageIcon')->set('lock');

        $this->view->navigation()->findOneByLabel('Roles')->setActive();
    }
    
    /**
     * action to display delete role form
     * and to delete role after confirmation
     * 
     * @since 1.0
     * @uses Fortuna_Model_AclRole
     *  
     */
    public function deleteRoleAction() {
        $arm  = new Fortuna_Model_AclRole();
        $data = $arm->getRole($this->_getParam('id'));

        if (!$data || $data['standard']) {
            $this->_redirect('admin/roles');
        }

        if ($this->_getParam('confirm') != '') {
            $arm->delete('id = ' . $this->_getParam('id'));

            $this->_redirect('admin/roles');
        }

        $this->view->data = $data;

        $this->view->headTitle($this->view->translate('Roles'));
        $this->view->pageTitle = 'Roles';
        $this->view->placeholder('pageIcon')->set('lock');
    }
    
    /**
     * action to display account form
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     * @uses Fortuna_Form_Account
     * @uses Zend_Auth
     *  
     */
    public function accountAction() {
        $um       = new Fortuna_Model_User();
        $identity = Zend_Auth::getInstance()->getIdentity();

        $this->view->form = new Fortuna_Form_Account();

        $this->view->form->setDefaults($um->getUser($identity->id));

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            if (trim($this->_getParam('password')) == '') {
                unset($_POST['password']);
            }

            $um->saveUser($_POST, $identity->id);

            $this->_redirect('admin/account');
        }

        $this->view->headTitle($this->view->translate('Account Settings'));
        $this->view->pageTitle = 'Account Settings';
        $this->view->placeholder('pageIcon')->set('user');
    }
    
    /**
     * action to display settings form
     * which gives the possibility to change
     * database settings and to update the database schema
     * if needed
     * 
     * @since 1.0
     * @uses Zend_Registry
     * @uses Fortuna_Setup
     * @uses Fortuna_Form_Setup
     *  
     */
    public function settingsAction() {
        $config = Zend_Registry::get('Zend_Config');
        $setup  = new Fortuna_Setup($config);


        if ($this->_hasParam('migrate')) {
            $db = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db');

            $setup->migrate($db);

            $this->_redirect('admin/settings');
        }

        $this->view->form = new Fortuna_Form_Settings();

        $this->view->php        = $setup->getPHPVersionData();
        $this->view->extensions = $setup->getExtensionData();
        $this->view->writable   = $setup->getWritableData();
        $this->view->schema     = $setup->getSchemaData();

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $this->view->form->writeConfig();

            $this->_redirect('admin/settings');
        }

        $this->view->headTitle($this->view->translate('Settings'));
        $this->view->pageTitle = 'Settings';
        $this->view->placeholder('pageIcon')->set('cog');
    }
    
    /**
     * action to display list of users
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     *  
     */
    public function usersAction() {
        $arm = new Fortuna_Model_User();

        $this->view->users = $arm->getUsers();

        $this->view->headTitle($this->view->translate('Users'));
        $this->view->pageTitle = 'Users';
        $this->view->placeholder('pageIcon')->set('user');
    }
    
    /**
     * action to display form to
     * show and edit a user or to add a new one
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     * @uses Fortuna_Form_User
     * @uses Zend_Auth
     *  
     */
    public function showUserAction() {
        if ($this->_hasParam('cancel')) {
            $this->_redirect('admin/users');
        }

        $um = new Fortuna_Model_User();

        $this->view->form = new Fortuna_Form_User();

        if ($this->_getParam('id') != '') {
            $data = $um->getUser($this->_getParam('id'));

            if (!$data) {
                $this->_redirect('admin/users');
            }

            $this->view->form->setDefaults($data);

            $this->view->data = $data;
        }

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $um->saveUser($_POST, $this->_getParam('id'));

            $this->_redirect('admin/users');
        }

        $this->view->headTitle($this->view->translate('Users'));
        $this->view->pageTitle = 'Users';
        $this->view->placeholder('pageIcon')->set('user');

        $this->view->navigation()->findOneByLabel('Users')->setActive();
    }
    
    /**
     * action to delete an existing user
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     *  
     */
    public function deleteUserAction() {
        $um  = new Fortuna_Model_User();
        $data = $um->getUser($this->_getParam('id'));

        if (!$data) {
            $this->_redirect('admin/users');
        }

        if ($this->_getParam('confirm') != '') {
            $um->delete('id = ' . $this->_getParam('id'));

            $this->_redirect('admin/users');
        }

        $this->view->data = $data;
        $this->view->pageTitle = 'Users';

        $this->view->navigation()->findOneByLabel('Users')->setActive();
    }
    
    /**
     * action with list of all articles
     * 
     * @since 1.0
     * @uses Fortuna_Model_Article
     *  
     */
    public function articlesAction() {
        $am = new Fortuna_Model_Article();

        $this->view->articles = $am->getArticles();

        $this->view->headTitle($this->view->translate('Articles'));
        $this->view->pageTitle = 'Articles';
        $this->view->placeholder('pageIcon')->set('pencil');
    }
    
    /**
     * action with form to display article and to edit
     * or delte one
     * 
     * @since 1.0
     * @uses Fortuna_Model_Article
     * @uses Fortuna_Form_Article
     *  
     */
    public function showArticleAction() {
        if ($this->_hasParam('cancel')) {
            $this->_redirect('admin/articles');
        }

        if ($this->_hasParam('view')) {
            $this->_redirect('article/id/' . $this->_getParam('id'));
        }

        $this->view->headLink()
                   ->appendStylesheet($this->view->baseUrl('assets/css/bootstrap-wysihtml5.css'));

        $this->view->headScript()
                   ->appendFile(
                        $this->view->baseUrl('assets/js/wysihtml5-0.3.0_rc3.min.js'),
                        'text/javascript'
                    )
                    ->appendFile(
                        $this->view->baseUrl('assets/js/bootstrap-wysihtml5.js'),
                        'text/javascript'
                    );

        $am = new Fortuna_Model_Article();

        $this->view->form = new Fortuna_Form_Article();

        if ($this->_getParam('id') != '') {
            $data = $am->getArticle($this->_getParam('id'));

            if (!$data) {
                $this->_redirect('admin/articles');
            }

            $this->view->form->setDefaults($data);

            $this->view->data = $data;
        }

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $am->saveArticle($_POST, $this->_getParam('id'));

            $this->_redirect('admin/articles');
        }

        $this->view->headTitle($this->view->translate('Articles'));
        $this->view->pageTitle = 'Articles';
        $this->view->placeholder('pageIcon')->set('pencil');

        $this->view->navigation()->findOneByLabel('Articles')->setActive();
    }
    
    /**
     * action to delete article
     * 
     * @since 1.0
     * @uses Fortuna_Model_Article
     *  
     */
    public function deleteArticleAction() {
        $am  = new Fortuna_Model_Article();
        $data = $am->getArticle($this->_getParam('id'));

        if (!$data) {
            $this->_redirect('admin/articles');
        }

        if ($this->_getParam('confirm') != '') {
            $am->delete('id = ' . $this->_getParam('id'));

            $this->_redirect('admin/articles');
        }

        $this->view->data = $data;
        $this->view->pageTitle = 'Articles';
    }
    
    /**
     * action to list categories available
     * 
     * @since 1.0
     * @uses Fortuna_Model_Category
     *  
     */
    public function categoriesAction() {
        $cm = new Fortuna_Model_Category();

        $this->view->categories = $cm->getCategories();

        $this->view->headTitle($this->view->translate('Categories'));
        $this->view->pageTitle = 'Categories';
        $this->view->placeholder('pageIcon')->set('tag');
    }
    
    /**
     * action to display form to
     * show and edit a category or to add a new one
     * 
     * @since 1.0
     * @uses Fortuna_Model_Category
     * @uses Fortuna_Form_Category
     *  
     */
    public function showCategoryAction() {
        if ($this->_hasParam('cancel')) {
            $this->_redirect('admin/categories');
        }

        if ($this->_hasParam('view')) {
            $this->_redirect('category/id/' . $this->_getParam('id'));
        }

        $cm = new Fortuna_Model_Category();

        $this->view->form = new Fortuna_Form_Category();

        if ($this->_getParam('id') != '') {
            $data = $cm->getCategory($this->_getParam('id'));

            if (!$data) {
                $this->_redirect('admin/categories');
            }

            $this->view->form->setDefaults($data);

            $this->view->data = $data;
        }

        if ($this->getRequest()->isPost() && $this->view->form->isValid($_POST)) {
            $cm->saveCategory($_POST, $this->_getParam('id'));

            $this->_redirect('admin/categories');
        }

        $this->view->headTitle($this->view->translate('Categories'));
        $this->view->pageTitle = 'Categories';
        $this->view->placeholder('pageIcon')->set('tag');

        $this->view->navigation()->findOneByLabel('Categories')->setActive();

    }
    
    /**
     * action to delete an existing category
     * 
     * @since 1.0
     * @uses Fortuna_Model_Category
     *  
     */
    public function deleteCategoryAction() {
        $cm  = new Fortuna_Model_Category();
        $data = $cm->getCategory($this->_getParam('id'));

        if (!$data) {
            $this->_redirect('admin/categories');
        }

        if ($this->_getParam('confirm') != '') {
            $cm->delete('id = ' . $this->_getParam('id'));

            $this->_redirect('admin/categories');
        }

        $this->view->navigation()->findOneByLabel('Categories')->setActive();

        $this->view->data = $data;
        $this->view->pageTitle = 'Categories';
    }
    
    /**
     * action to edit two-level navigation based on
     * articles and categories
     * 
     * @since 1.0
     * @uses Fortuna_Model_Navigation
     * @uses Fortuna_Model_Article
     * @uses Fortuna_Model_Category
     *  
     */
    public function navigationAction() {
        $nm = new Fortuna_Model_Navigation();

        if ($this->getRequest()->isXmlHttpRequest() && $this->_hasParam('items')) {

            if ($nm->saveNavTree($this->_getParam('items'))) {
                die('1');
            } else {
                die('2');
            }
        }

        $this->view->headTitle($this->view->translate('Navigation'));
        $this->view->pageTitle = 'Navigation';
        $this->view->placeholder('pageIcon')->set('list');

        $this->view->headLink()
                   ->appendStylesheet($this->view->baseUrl('/assets/css/navigation-builder.css'));

        $this->view->headScript()
                        ->appendFile(
                            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',
                            'text/javascript'
                        )
                        ->appendFile(
                            $this->view->baseUrl('assets/js/jquery.mjs.nestedSortable.js'),
                            'text/javascript'
                        )
                        ->appendFile(
                            $this->view->baseUrl('assets/js/jquery.serializetree.js'),
                            'text/javascript'
                        );

        $am = new Fortuna_Model_Article();
        $cm = new Fortuna_Model_Category();

        $this->view->articles   = $am->getArticles();
        $this->view->categories = $cm->getCategories();
        $this->view->navigation = $nm->getNavTree();
    }
    
    /**
     * action to display dashboard of admin area
     * with some stats about articles an categories in the system
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     * @uses Fortuna_Model_Article
     * @uses Fortuna_Model_ArticleCategory
     * @uses Fortuna_Model_Category
     *  
     */
    public function indexAction() {

        $this->view->headScript()
        ->appendFile(
            $this->view->baseUrl('assets/js/excanvas.min.js'),
            'text/javascript'
        )
        ->appendFile(
            $this->view->baseUrl('assets/js/jquery.flot.js'),
            'text/javascript'
        )
        ->appendFile(
            $this->view->baseUrl('assets/js/jquery.flot.pie.js'),
            'text/javascript'
        )
        ->appendFile(
            $this->view->baseUrl('assets/js/jquery.flot.orderBars.js'),
            'text/javascript'
        )
        ->appendFile(
            $this->view->baseUrl('assets/js/jquery.flot.resize.js'),
            'text/javascript'
        );

        $am  = new Fortuna_Model_Article();
        $cm  = new Fortuna_Model_Category();
        $um  = new Fortuna_Model_User();
        $acm = new Fortuna_Model_ArticleCategory();

        $this->view->articlesCount       = $am->getArticlesCount();
        $this->view->categoriesCount     = $cm->getCategoriesCount();
        $this->view->usersCount          = $um->getUsersCount();
        $this->view->articlesCategoryAvg = $acm->getArticlesCategoryAvg();
        $this->view->articlesPerCategory = $acm->getArticlesPerCategory();
        $this->view->articlesLately      = $am->getArticlesLately();

        $this->view->headTitle($this->view->translate('Dashboard'));
        $this->view->pageTitle = 'Dashboard';
        $this->view->placeholder('pageIcon')->set('home');
    }
    
    /**
     * action to display page with frequently asked questions
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     * @uses Fortuna_Form_User
     * @uses Zend_Registry
     * @uses simplexml_load_file()
     *  
     */
    public function faqAction() {
        $faq             = simplexml_load_file(Zend_Registry::get('Zend_Config')->fortuna->faq_path);
        $this->view->faq = $faq->faq;

        $this->view->headScript()
        ->appendFile(
            $this->view->baseUrl('assets/js/faq.js'),
            'text/javascript'
        );

        $this->view->headLink()
             ->appendStylesheet($this->view->baseUrl('assets/css/pages/faq.css'));

        $this->view->headTitle($this->view->translate('Frequently Asked Questions'));
        $this->view->pageTitle = 'Frequently Asked Questions';
        $this->view->placeholder('pageIcon')->set('pushpin');
    }
}

