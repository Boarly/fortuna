<?php

/**
 * user form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_User extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * init user form elements
     * 
     * @since 1.0
     * @uses Fortuna_Model_AclRole
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

        $arm = new Fortuna_Model_AclRole();

        $this->addElement('select', 'acl_role_id', array(
            'label'        => 'Role',
            'required'     => true,
            'multiOptions' => $arm->getRolesSelect()
        ));

		$this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Save'
        ));

        $this->addElement('submit', 'cancel', array(
            'label'      => 'Cancel'
        ));

        $this->addDisplayGroup(
            array('submit', 'cancel'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );
	}

}