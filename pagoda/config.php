<?php
/**
 * Pagodabox Deployment
 * 
 * config.php
 * 
 * Deployment script to write pagoda specific
 * values to application.ini
 * 
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../protected/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'production');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/../../pagoda/application.ini.dist'
);

$application->bootstrap();

$config = new Zend_Config_Ini(
            APPLICATION_PATH . '/../../pagoda/application.ini.dist',
            null,
            array(
                'skipExtends'        => true,
                'allowModifications' => true
            )
        );

$config->production->resources->db->params->host     = $_SERVER["DB1_HOST"];
$config->production->resources->db->params->username = $_SERVER["DB1_USER"];
$config->production->resources->db->params->password = $_SERVER["DB1_PASS"];
$config->production->resources->db->params->dbname   = $_SERVER["DB1_NAME"];

$writer = new Zend_Config_Writer_Ini(array(
    'config'   => $config,
    'filename' => APPLICATION_PATH . '/../../pagoda/application.ini.dist'
));

$writer->write();