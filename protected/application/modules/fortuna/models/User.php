<?php

/**
 * model class for users table
 * 
 * @package Fortuna
 * @subpackage Fortuna_Model
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Model_User extends Fortuna_Db_Table_Abstract {
    
    /**
     * name of table
     * 
     * @var    string
     * @access protected
     */
	protected $_name    = 'user';
    
    /**
     * name of primary key
     * 
     * @var    string
     * @access protected 
     */
	protected $_primary = 'id';
        
    /**
     * get eight characters password
     * 
     * @return string
     * @since 1.0 
     */
    protected function _getPassword() {
		return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',8)),0,8);
	}
    
    /**
     * send password mail to $user with
     * $rawPassword
     * 
     * @param  string $rawPassword new raw password for user
     * @param  array  $user        user data
     * @since  1.0
     * @access protected
     */
	protected function _sendPassowrdMail($rawPassword, array $user) {
		$view 			   = new Zend_View();
		$view->setScriptPath(Zend_Registry::get('Zend_Config')->fortuna->emails_path);
		$view->user        = $user;
		$view->rawPassword = $rawPassword;
		$view->app_title   = Zend_Registry::get('Zend_Config')->fortuna->app_title;

		$mail = new Zend_Mail();
		$mail->setBodyHtml($view->render('new-user.phtml'));
		$mail->addTo($user['email']);
		$mail->setSubject('New User at your Fortuna CMS');
		$mail->send();
	}
    
    /**
     * get all users
     * 
     * @return array
     * @since  1.0
     * @uses   Fortuna_Model_AclRole
     */
	public function getUsers() {
		$arm = new Fortuna_Model_AclRole();

		$select = $this
				  ->select()
				  ->setIntegrityCheck(false)
				  ->from($this->_name . ' AS u', array('id', 'firstname', 'lastname'))
				  ->join($arm->getTableName() . ' AS r', 'r.id = u.acl_role_id', array('acl_role' => 'name'));


		return $this->fetchAll($select)->toArray();
	}
    
    /**
     * get user by $id
     * 
     * @param  int $id id of the user
     * @return array
     * @since 1.0
     */
	public function getUser($id) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS u', array('*'))
				  ->where('u.id = ?', $id);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();

			unset($data['password']);

			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * get user data by $email
     * 
     * @param string $email email address of user
     * @return array
     * @since 1.0
     */
	public function getUserByEmail($email) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS u', array('*'))
				  ->where('u.email = ?', $email);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();

			unset($data['password']);

			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * get user by $passwordLink
     * 
     * @param string $passwordLink value of field "password_link" int the db
     * @return array
     * @since 1.0
     */
	public function getUserByPasswordLink($passwordLink) {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS u', array('*'))
				  ->where('u.password_link = ?', $passwordLink);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			$data = $res->toArray();

			unset($data['password']);

			return $data;
		} else {
			return array();
		}
	}
    
    /**
     * recover user (reset password) with email address
     * $email
     * 
     * @param string $email email address of user
     * @since 1.0
     * @uses Zend_View
     * @uses Zend_Mail
     * @uses Zend_Registry
     */
	public function recover($email) {
		$user = $this->getUserByEmail($email);

		$passwordLink = null;

		do {
			$passwordLink = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',64)),0,64);
		} while ($this->exists(array('password_link = "' . $passwordLink .'"')));

		$this->update(array('password_link' => $passwordLink), 'id = ' . $user['id']);
		

		$view = new Zend_View();
		$view->setScriptPath(Zend_Registry::get('Zend_Config')->fortuna->emails_path);
		$view->user         = $user;
		$view->passwordLink = $passwordLink;
		$view->app_title    = Zend_Registry::get('Zend_Config')->fortuna->app_title;

		$mail = new Zend_Mail();
		$mail->setBodyHtml($view->render('recover.phtml'));
		$mail->addTo($user['email']);
		$mail->setSubject('Password Recover at your Fortuna CMS');
		$mail->send();

	}
    
    /**
     * recover password for user with
     * $passwordLink
     * 
     * @param string $passwordLink value identifying user via db field password_link
     * @since 1.0
     */
	public function recoverPassword($passwordLink) {
		$user = $this->getUserByPasswordLink($passwordLink);

		$rawPassword = $this->_getPassword();

		$data = array(
			'password_link' => null,
			'password'		=> md5($rawPassword)
    	);

		if ($this->update($data, 'id = ' . $user['id'])) {
			$this->_sendPassowrdMail($rawPassword, $user);
		}
	}
    
    /**
     * save a user by inserting or updating
     * 
     * @param  array    $input user data to save
     * @param  int|null $id    id of user to save $input at
     * @since 1.0
     * @return mixed 
     */
	public function saveUser(array $input, $id = null) {
		$data = array(
			'firstname' => $input['firstname'],
			'lastname'	=> $input['lastname'],
			'email'		=> $input['email']
		);

		if (isset($input['password'])) {
			$data['password'] = md5($input['password']);
		}

		if (isset($input['acl_role_id'])) {
			$data['acl_role_id'] = $input['acl_role_id'];
		}

		if (empty($id)) {
			$rawPassword = $this->_getPassword();

			$data['password'] = md5($rawPassword);

			$id = $this->insert($data);

			$this->_sendPassowrdMail($rawPassword, $data);

			return $id;

		} else {
			return $this->update($data, 'id = ' . $id);
		}
	}
    
    /**
     * get number of all users in the system
     * 
     * @return int
     * @since 1.0
     */
	public function getUsersCount() {
		$select = $this
				  ->select()
				  ->from($this->_name . ' AS u', array('count' => 'COUNT(*)'))
				  ->limit(1);

		$res = $this->fetchRow($select);

		if ($res instanceof Zend_Db_Table_Row) {
			return $res->count;
		}

		return 0;
	}

}