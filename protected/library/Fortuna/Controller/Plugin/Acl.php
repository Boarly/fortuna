<?php

/**
 * access control list controller plugin,
 * used to allow/not allow privileges to current identity
 * 
 * @package Fortuna
 * @subpackage Fortuna_Controller_Plugin
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
    
    /**
     * instance of Zend_Acl
     * 
     * @var    Zend_Acl
     * @access private 
     */
	private $_acl = null;
    
    /**
     * initialize controller plugin at preDispatch
     * time
     *  
     * @access private
     * @since 1.0
     * @uses Zend_Acl
     */
	private function _init() {
		$this->_acl = new Zend_Acl();

		$arm  = new Fortuna_Model_AclRole();
		$arpm = new Fortuna_Model_AclRolePrivilege();
		$arom = new Fortuna_Model_AclResource();
		$apm  = new Fortuna_Model_AclPrivilege();

		foreach ($arm->getRoles() as $role) {
			$this->_acl->addRole(new Zend_Acl_Role($role['name']));
		}

		foreach ($arom->getResources() as $resource) {
			$this->_acl->add(new Zend_Acl_Resource($resource['name']));

			foreach ($apm->getResourcePrivileges($resource['id']) as $privilege) {
				$this->_acl->add(new Zend_Acl_Resource($privilege['name']), $resource['name']);
			}
		}

		foreach ($arpm->getAllRolePrivileges() as $rolePrivilege) {
			$this->_acl->allow(
			    $rolePrivilege['role'], 
			    $rolePrivilege['resource'], 
			    $rolePrivilege['privilege']
			);
		}

		Zend_Registry::set('Zend_Acl', $this->_acl);
	}
    
    /**
     * preDispatch callback function of controller plugin,
     * if pugin has been registered this method is called at each
     * construction of an controller (pre dispatch of action)
     * 
     * @param Zend_Controller_Request_Abstract $request the current request object
     * @throws Zend_Acl_Exception
     * @uses Zend_Auth
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$auth = Zend_Auth::getInstance();

		if ($request->getControllerName() != 'admin' || !$auth->hasIdentity()) {
			return;
		}

    	$this->_init();

    	$role = $auth->getIdentity()->acl_role;

		$allowed = $this->_acl->isAllowed(
    		$role, 
		    $request->getControllerName(), 
		    $request->getActionName()
		);

    	if (!$allowed) {
    		throw new Zend_Acl_Exception('You are not allowed to enter this page.');
    	}
    }

}