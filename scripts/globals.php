<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * A few constants and includes which will be used through the application.
 */
// Include the loader script
require_once 'Zend/Loader.php';

// Load gdata modules
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');

// So PHP doesn't complain...
date_default_timezone_set('America/Los_Angeles');

// Constants
define('DEBUG', (strstr($_SERVER['SERVER_NAME'], 'localhost') !== false));
define('APP_VERSION', 1.0);
define('SPREADSHEET_VERSION', 1.0);
define('SPREADSHEET_MASTER_URL', 'https://spreadsheets.google.com/ccc?key=0AnRif0EzefXxdEViXzFGdjlJLXNXYlBhdXFmUERqTnc&hl=en');

if (DEBUG) {
  define('BASE_URL', 'http://localhost/gaslog/trunk/');
  define('FILTER_TEXT', '(occlness)');
}
else {
  define('BASE_URL', 'http://gas.randomland.net/');
  define('FILTER_TEXT', '(rlgaslog)');
}

// Authentication functions
require_once 'scripts/auth.php';

// Application logic
require_once 'scripts/utils.php';
require_once 'scripts/glapp.php';
require_once 'scripts/doc.php';
require_once 'scripts/sheet.php';