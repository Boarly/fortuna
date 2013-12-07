<?php

/**
 * acl role form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0
 */
class Fortuna_Form_AclRole extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * init acl role form elements
     * 
     * @since 1.0
     * @uses Fortuna_Model_AclPrivilege
     *  
     */
	public function init() {
		$this->addElement('text', 'name', array(
            'label'    => 'Name',
            'required' => true,
            'dimension'    => 5
        ));

        $aam = new Fortuna_Model_AclPrivilege();

        $this->addElement('multiselect', 'role_privileges', array(
            'label'        => 'Actions',
            'multiOptions' => $aam->getPrivilegesSelect(),
            'required'     => true,
            'size'         => 10,
            'description'  => 'Actions that can be called',
            'dimension'    => 5
        ));

		$this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Save'
        ));

        $this->addElement('submit', 'cancel', array(
            'label'      => 'Cancel',
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