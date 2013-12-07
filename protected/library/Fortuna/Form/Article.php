<?php

/**
 * article form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_Article extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * init article form elements
     * 
     * @since 1.0
     * @uses Fortuna_Model_Category
     *  
     */
	public function init() {
		$this->addElement('text', 'title', array(
            'label'    => 'Title',
            'required' => true,
            'dimension' => 6
        ));

        $this->addElement('textarea', 'content', array(
            'label'     => 'Content',
            'required'  => true,
            'dimension' => 6
        ));

        $this->getElement('content')->id = 'element-content';

        $cm = new Fortuna_Model_Category();

        $this->addElement('multiselect', 'categories', array(
            'label'        => 'Categories',
            'size'         => 5,
            'multioptions' => $cm->getCategoriesSelect(),
            'dimension'    => 6
        ));

        $this->addElement('checkbox', 'is_home', array(
            'label'    => 'Homepage'
        ));

		$this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Save'
        ));

        $this->addElement('submit', 'cancel', array(
            'label'      => 'Cancel',
        ));

        $this->addElement('button', 'view', array(
            'label'      => 'View',
            'type'       => 'submit',
            'icon'       => 'zoom-in'
        ));

        $this->addDisplayGroup(
            array('submit', 'view', 'cancel'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );
	}

}