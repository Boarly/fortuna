<?php
   
/**
 * account form class
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_Account extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * initialize account form elements
     * 
     * @since 1.0
     *  
     */
	public function init() {
		$this->addElement('text', 'email', array(
            'label'    => 'Email',
            'required' => true
        ));

        $this->addElement('text', 'firstname', array(
            'label'    => 'Firstname',
            'required' => true
        ));

        $this->addElement('text', 'lastname', array(
            'label'    => 'Lastname',
            'required' => true
        ));

        $this->addDisplayGroup(
            array('email', 'firstname', 'lastname'),
            'account-group',
            array(
                'legend' => 'Account info'
            )
        );

        $this->addElement('password', 'password', array(
            'label'    => 'Password',
            'validators' => array(
                array('identical', false, array('token' => 'password_repeat'))
            )
        ));

        $this->addElement('password', 'password_repeat', array(
            'label'    => 'Password Repeat',
        ));

        $this->addDisplayGroup(
            array('password', 'password_repeat'),
            'password-group',
            array(
                'legend' => 'Password'
            )
        );

		$this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Save'
        ));

        $this->addDisplayGroup(
            array('submit'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );


        $submit = $this->getElement('submit'); 
        $submit->removeDecorator('Zend_Form_Decorator_Label');
	}

}