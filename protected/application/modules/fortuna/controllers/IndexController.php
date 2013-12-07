<?php

/**
 * index contoller class, uses for the cms frontend
 * (to display the categories and pages contents)
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_IndexController extends Zend_Controller_Action {
    
    /**
     * method to init index controller class'
     * widely used components
     * 
     * @since 1.0
     * @uses Zend_Registry
     * @uses Zend_Auth
     * @uses Fortuna_Model_Navigation
     * @uses Zend_Navigation 
     *  
     */
    public function init() {   
        $this->_helper->layout()->setLayout('main');

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
        );

        $this->view->headLink()
        ->appendStylesheet($this->view->baseUrl('/assets/themes/' . Zend_Registry::get('Zend_Config')->fortuna->theme . '/bootstrap.css'))
        ->appendStylesheet($this->view->baseUrl('/assets/css/bootstrap-responsive.min.css'));

        $request = $this->getRequest();
        $this->view
             ->headTitle(Zend_Registry::get('Zend_Config')->fortuna->app_title)
             ->setSeparator(' | ');

        $this->view->placeholder('appTitle')->set(Zend_Registry::get('Zend_Config')->fortuna->app_title);

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        }

        $nm        = new Fortuna_Model_Navigation();
        $navConfig = array();
        
        foreach ($nm->getNavTree() as $k => $item) {
            $navConfig['item_' . $k] = array(
                'uri'   => $this->view->baseUrl('/' . $item['type'] . '/' . $item['slug']),
                'label' => $item['label']
            );

            if (isset($item['childs'])) {
                $navConfig['item_' . $k]['pages'] = array();

                foreach ($item['childs'] as $childK => $child) {
                   $navConfig['item_' . $k]['pages']['child_' . $childK] = array(
                       'uri'   => $this->view->baseUrl('/' . $child['type'] . '/' . $child['slug']),
                       'label' => $child['label']
                   );
                }
            }
        }

        $navContainer = new Zend_Navigation(new Zend_Config($navConfig));
        
        $this->view->navigation($navContainer)
                   ->menu()
                   ->setPartial(array('partials/horizontal-nav.phtml', 'fortuna'));
    }
    
    /**
     * action used to display articles,
     * identified by slug or id
     * 
     * @since 1.0
     * @uses Fortuna_Model_Article
     * @uses Fortuna_ArticleCategory
     *  
     */
    public function articleAction() {
        $am  = new Fortuna_Model_Article();
        $acm = new Fortuna_Model_ArticleCategory();

        if ($this->_hasParam('slug') && $am->exists(array('slug = "' . $this->_getParam('slug') . '"'))) {
            $article = $am->getArticleBySlug($this->_getParam('slug'));
        } elseif ($this->_hasParam('id') && $am->exists(array('id = "' . $this->_getParam('id') . '"'))) {
            $article = $am->getArticle($this->_getParam('id'));
        } else {
            throw new Zend_Controller_Action_Exception('This article does not exist', 404); 
        }

        $this->view->placeholder('pageTitle')->set($article['title']);
        $this->view->article    = $article;
        $this->view->categories = $acm->getArticleCategories($article['id'], false);

        $this->view->headTitle($article['title']);
    }
    
    /**
     * action used to display category
     * articles list at one specific page;
     * identified by slug or id
     * 
     * @since 1.0
     * @uses Fortuna_Model_Category
     * @uses Fortuna_ArticleCategory
     * @throws Zend_Controller_Action_Exception
     *  
     */
    public function categoryAction() {
        $cm  = new Fortuna_Model_Category();
        $acm = new Fortuna_Model_ArticleCategory();

        if ($this->_hasParam('slug') && $cm->exists(array('slug = "' . $this->_getParam('slug') . '"'))) {
            $category = $cm->getCategoryBySlug($this->_getParam('slug'));
        } elseif ($this->_hasParam('id') && $cm->exists(array('id = "' . $this->_getParam('id') . '"'))) {
            $category = $cm->getCategory($this->_getParam('id'));
        } else {
            throw new Zend_Controller_Action_Exception('This category does not exist', 404); 
        }

        $this->view->placeholder('pageTitle')->set($category['name']);
        $this->view->category = $category;
        $this->view->articles = $acm->getArticlesByCategoryId($category['id']);

        $this->view->headTitle($category['name']);
    }
    
    /**
     * action used for homepage,
     * to display homepage article or if not found
     * the latest article
     * 
     * @since 1.0
     * @uses Fortuna_Model_Article
     * @uses Fortuna_ArticleCategory
     *  
     */
    public function indexAction() {
        $am  = new Fortuna_Model_Article();
        $acm = new Fortuna_Model_ArticleCategory();

        $article = $am->getArticle($am->getHomeId());

        $this->view->placeholder('pageTitle')->set($article['title']);
        $this->view->article    = $article;
        $this->view->categories = $acm->getArticleCategories($article['id'], false);

        $this->view->headTitle($article['title']);

    }


}

