<?php

/**
 * settings form
 * 
 * @package Fortuna
 * @subpackage Fortuna_Form
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Form_Settings extends Twitter_Bootstrap_Form_Horizontal {
    
    /**
     * Zend_Config object
     * 
     * @var Zend_Config_Ini
     * @access protected
     */
    protected $_config = null;
    
    /**
     *
     * get all available themes in themes dir
     * 
     * @return array
     * @since 1.0
     * @access protected
     * @uses DirectoryIterator
     */
    protected function _getAllThemes() {

        $themes = array();
        $dir      = new DirectoryIterator(Zend_Registry::get('Zend_Config')->fortuna->themes_dir);

        foreach($dir as $fileInfo) {

            if ($fileInfo->isDot() || !$fileInfo->isDir()) {
                continue;
            }

            $themes[$fileInfo->__toString()] = ucfirst($fileInfo->__toString());
        
        }

        return $themes;
    }
    
    /**
     * init settings form elements
     * 
     * @since 1.0
     * @uses Zend_Config_Ini
     * @uses Zend_Registry
     *  
     */
	public function init() {
        $this->_config = new Zend_Config_Ini(
            Zend_Registry::get('Zend_Config')->fortuna->config_path,
            null,
            array(
                'skipExtends'        => true,
                'allowModifications' => true
            )
        );

		$this->addElement('select', 'theme', array(
            'label'        => 'Theme',
            'required'     => true,
            'multiOptions' => $this->_getAllThemes()
        ));

        $this->addElement('text', 'app_title', array(
            'label'    => 'App Title',
            'required' => true
        ));

        $this->addElement('text', 'db_host', array(
            'label'    => 'Host',
            'required' => true
        ));

        $this->addElement('text', 'db_username', array(
            'label'    => 'Username',
            'required' => true
        ));

        $this->addElement('text', 'db_password', array(
            'label'    => 'Password',
            'required' => true
        ));

        $this->addElement('text', 'db_name', array(
            'label'    => 'Name',
            'required' => true
        ));

        $this->addElement('text', 'db_tbl_prefix', array(
            'label'    => 'Table Prefix',
            'required' => true
        ));

        $this->addDisplayGroup(
            array('app_title', 'theme'),
            'settings-app',
            array(
                'legend' => 'Application'
            )
        );

        $this->addDisplayGroup(
            array('db_host', 'db_username', 'db_password', 'db_name', 'db_tbl_prefix'),
            'settings-databse',
            array(
                'legend' => 'Database'
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

        $data = array(
            'theme'         => $this->_config->production->fortuna->theme,
            'app_title'     => $this->_config->production->fortuna->app_title,
            'db_host'       => $this->_config->production->resources->db->params->host,
            'db_username'   => $this->_config->production->resources->db->params->username,
            'db_password'   => $this->_config->production->resources->db->params->password,
            'db_name'       => $this->_config->production->resources->db->params->dbname,
            'db_tbl_prefix' => $this->_config->production->fortuna->table_prefix
        );

        $this->setDefaults($data);
	}
    
    /**
     * write config values
     * of this form to config_path
     * 
     * @since 1.0
     * @uses Zend_Config_Writer_Ini
     * @uses Zend_Registry
     *  
     */
    public function writeConfig() {
        $this->_config->production->fortuna->theme                  = $this->getValue('theme');
        $this->_config->production->fortuna->app_title              = $this->getValue('app_title');
        $this->_config->production->resources->db->params->host     = $this->getValue('db_host');
        $this->_config->production->resources->db->params->username = $this->getValue('db_username');
        $this->_config->production->resources->db->params->password = $this->getValue('db_password');
        $this->_config->production->resources->db->params->dbname   = $this->getValue('db_name');
        $this->_config->production->fortuna->table_prefix           = $this->getValue('db_tbl_prefix');

        $writer = new Zend_Config_Writer_Ini(array(
            'config'   => $this->_config,
            'filename' => Zend_Registry::get('Zend_Config')->fortuna->config_path
        ));

        $writer->write();
    }

}