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

// Preload class. Needed for serialization of $_SESSION objects. Must be done
// before `session_start()`. See:
//   http://stackoverflow.com/questions/1219861/#1219874
Zend_Loader::loadClass('Zend_Http_Client_Adapter_Socket');

// So PHP doesn't complain...
date_default_timezone_set('America/Los_Angeles');

/**
 * Define a constant iff not already defined.
 */
function defndef($name, $val) {
  if (! defined($name)) {
    define($name, $val);
  }
}

// Constants
defndef('DEBUG', (strstr($_SERVER['SERVER_NAME'], 'localhost') !== false));
defndef('MIN_RELEASE', false);
defndef('APP_VERSION', 1.0);
defndef('BUILD_VERSION', '1.0.20110908');
defndef('SYSTEM_ADMIN_URI', 'mailto:zourtney@randomland.net');
defndef('SPREADSHEET_VERSION', 1.1);
defndef('SPREADSHEET_MASTER_URL', 'https://spreadsheets.google.com/ccc?key=0AnRif0EzefXxdEViXzFGdjlJLXNXYlBhdXFmUERqTnc&hl=en');

if (DEBUG) {
  // Verbose error reporting
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  // Define base paths, doc filter text, etc
  defndef('BASE', $_SERVER['DOCUMENT_ROOT'] . '/mobilemiles/');
  defndef('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/mobilemiles/');
  defndef('FILTER_TEXT', '(occlness)');
}
else {
  // No visible errors
  error_reporting(0);
  ini_set('display_errors', '0');
  
  // Define base paths, doc filter text, etc
  defndef('BASE', $_SERVER['DOCUMENT_ROOT'] . '/');
  defndef('BASE_URL', 'http://gas.randomland.net/');
  defndef('FILTER_TEXT', '(mobilemiles)');
}

defndef('SHOW_CHANGES_MESSAGE', false);

defndef('SCRIPT_BASE', BASE . 'scripts/');
defndef('SCRIPT_URL', BASE_URL . 'scripts/');
defndef('LOGIN_URL', SCRIPT_URL . 'ajax_login.php');

defndef('TEMPLATE_BASE', BASE . 'templates/');
defndef('TEMPLATE_URL', BASE_URL . 'templates/');

defndef('JS_BASE', BASE . 'js/');
defndef('JS_URL', BASE_URL . 'js/');

defndef('PAGE_BASE', BASE . 'pages/');
defndef('PAGE_URL', BASE_URL . 'pages/');

// Include interfaces
require_once SCRIPT_BASE . 'interfaces.php';

// Authentication functions
//require_once 'scripts/auth.php';
require_once SCRIPT_BASE . 'oauth_secret.php';
require_once SCRIPT_BASE . 'oauth.php';

// Application logic
require_once SCRIPT_BASE . 'utils.php';
require_once SCRIPT_BASE . 'glapp.php';
require_once SCRIPT_BASE . 'doc.php';
require_once SCRIPT_BASE . 'sheet.php';