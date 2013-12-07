<?php

/**
 * category form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_Category extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * init category form elements
     * 
     * @since 1.0
     *  
     */
	public function init() {
		$this->addElement('text', 'name', array(
            'label'    => 'Name',
            'required' => true
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