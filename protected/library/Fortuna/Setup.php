<?php

/**
 * fortuna setup class
 * 
 * @package Fortuna
 * @copyright Copyright (c) 2012 Peter Persiel, Sven Ballay
 * @version 1.0
 * @since 1.0 
 */
class Fortuna_Setup {

	const NEEDED_PHP = '5.2.3';
    
    /**
     * needed extension
     * 
     * @var array
     * @access protected
     * @static
     */
	static protected $_extensions = array(
    	'pdo_mysql',
    	'SimpleXML',
    	'session',
    	'Reflection',
    );
    
    /**
     * paths needed to be writable
     * 
     * @var array
     * @access protected
     * @static
     */
    static protected $_writable = array(
    	'/configs/application.ini'
    );
    
    /**
     * application config
     * 
     * @var Zend_Config
     * @access protected
     */
	protected $_config = null;
    
    /**
     * callback method to sort versions
     * 
     * @param string $a
     * @param string $b
     * @since 1.0
     * @access protected
     * @uses version_compare()
     * @return int 
     */
    protected function _versionSort($a, $b) {
    	if (version_compare($a, $b, '==')) {
	        return 0;
	    }
	    return (version_compare($a, $b, '<')) ? -1 : 1;
    }
       
    /**
     * construct setup object
     * 
     * @param Zend_Config $config application config to be used
     * @since 1.0
     */
	function __construct(Zend_Config $config) {
		$this->_config = $config;
	}
    
    /**
     * if wrong php version is installed
     * 
     * @return boolean
     * @since 1.0
     * @uses version_compare()
     */
	public function wrongPHP() {
		return !(version_compare(PHP_VERSION, self::NEEDED_PHP) >= 0);
	}
    
    /**
     * if wrong php extensions are loaded
     * 
     * @return boolean
     * @since 1.0
     * @uses extension_loaded()
     */
	public function wrongExtensions() {
		foreach (self::$_extensions as $extension) {
    		if (!extension_loaded($extension)) {
    			return true;
    		}
	    }

	    return false;
	}
    
    /**
     * if not all needed paths are writable
     * 
     * @return boolean
     * @since 1.0
     */
	public function wrongWritable() {
		foreach (self::$_writable as $path) {
    		if (!is_writable(realpath(APPLICATION_PATH . $path))) {
    			return true;
    		}
	    }

	    return false;
	}
    
    /**
     * get latest schema version (e.g. 1.0)
     * 
     * @return string
     * @since 1.0
     * @uses DirectoryIterator 
     */
	public function getLatestSchema() {
    	$latest = false;

    	foreach (new DirectoryIterator($this->_config->fortuna->schema_path) as $fileInfo) {
		    if ($fileInfo->isDot() || strpos($fileInfo->getFileName(), '.sql') === false) continue;

		    $version = $fileInfo->getBasename('.sql');


			if (version_compare($version, $latest, '>=')) {
				$latest = $version;
			}
		}

		return $latest;
    }
    
    /**
     * return php version data
     * 
     * @return array
     * @since 1.0 
     */
    public function getPHPVersionData() {
    	return array(
	    	'found'     => version_compare(PHP_VERSION, self::NEEDED_PHP) >= 0,
	        'needed'    => self::NEEDED_PHP
	    );
    }
    
    /**
     * return php extension data
     * 
     * @return array
     * @since 1.0 
     */
    public function getExtensionData() {
    	$ret = array();

    	foreach (self::$_extensions as $extension) {
    		$ret[$extension] = extension_loaded($extension);
	    }

	    return $ret;
    }
    
    /**
     * get writable paths data
     * 
     * @return array
     * @since 1.0 
     */
    public function getWritableData() {
    	$ret = array();

    	foreach (self::$_writable as $path) {
    		$ret[$path] = is_writable(realpath(APPLICATION_PATH . $path));
	    }

	    return $ret;
    }
    
    /**
     * get database schema data
     * 
     * @return array
     * @since 1.0
     */
    public function getSchemaData() {
    	return array(
    		'latest'    => $this->getLatestSchema(),
    		'installed' => $this->_config->fortuna->installed_schema,
    		'update'	=> $this->schemaAvailable()
		);
    }
    
    /**
     * if a newer schema is available
     * 
     * @return boolean
     * @since 1.0 
     */
    public function schemaAvailable() {

    	$latest    = $this->getLatestSchema();
    	$installed = $this->_config->fortuna->installed_schema;

    	return version_compare($latest, $installed, '>');
    }
    
    /**
     * migrate to the latest database schema
     * 
     * @param Zend_Db_Adapter_Pdo_Mysql $db db adapter used for migration
     * @since 1.0
     * @uses Zend_Config_Ini
     * @uses Zend_Config_Writer_Ini
     * @throws Fortuna_Setup_Exception 
     */
    public function migrate(Zend_Db_Adapter_Pdo_Mysql $db) {
    	$latest    = $this->getLatestSchema();
    	$installed = $this->_config->fortuna->installed_schema;

    	if (!$this->schemaAvailable()) {
    		throw new Fortuna_Setup_Exception("There is no new database schema available");
    	}

    	$updateVersions = array();

    	foreach (new DirectoryIterator($this->_config->fortuna->schema_path) as $fileInfo) {
		    if ($fileInfo->isDot() || strpos($fileInfo->getFileName(), '.sql') === false) continue;

		    $version = $fileInfo->getBasename('.sql');

			if (version_compare($version, $installed, '>')) {
				$updateVersions[] = $version;
			}
		}

		usort($updateVersions, array($this, '_versionSort'));

		foreach ($updateVersions as $version) {
			$statements = explode(
			                ';', 
			              	file_get_contents(
          	                	$this->_config->fortuna->schema_path . 
          	                	'/' . 
          	                	$version . '.sql'
          	                )
			              );

			foreach ($statements as $stm) {
				$stm = trim($stm);

				if (empty($stm)) {
					continue;
				}

				$db->query($stm . ';');
			}
		}

		$config = new Zend_Config_Ini(
            $this->_config->fortuna->config_path,
            null,
            array('skipExtends'      => true,
                'allowModifications' => true)
        );

		$config->production->fortuna->installed_schema = $latest;

		$writer = new Zend_Config_Writer_Ini(array(
            'config'   => $config,
            'filename' => $this->_config->fortuna->config_path
        ));

        $writer->write();
    }
}