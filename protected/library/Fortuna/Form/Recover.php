<?php

/**
 * recover password form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_Recover extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * init recover password form elements
     * 
     * @since 1.0
     * @uses Fortuna_Model_User
     *  
     */
	public function init() {
        $um = new Fortuna_Model_User();

		$this->addElement('text', 'email', array(
            'label'       => 'Email',
            'required'    => true,
            'description' => 'An email with an link back to this site will be sent to your email address.',
            'validators'  => array(
                'db' => array(
                    'validator' => 'Db_RecordExists',
                    'options'   => array(
                        'table' => $um->getTableName(),
                        'field' => 'email'
                    )
                ),
                'email' => array(
                    'validator' => 'EmailAddress'
                )
            )
        ));

		$this->addElement('button', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Recover',
            'type'       => 'submit',
            'icon'       => 'repeat',
            'class'      => 'btn btn-large btn-primary'
        ));

        $this->addDisplayGroup(
            array('submit'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );

        $this->getElement('submit')->removeDecorator('Zend_Form_Decorator_Label');
	}

}