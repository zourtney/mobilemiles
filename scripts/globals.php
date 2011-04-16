<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * A few constants which will be used through the application.
 */

// Constants
define('DEBUG', (strstr($_SERVER['SERVER_NAME'], 'localhost') !== false));

if (DEBUG) {
  // Put something obnoxious at the top of the page :-)
  echo '<div style="margin: 0; padding: 1em; display: block; background: #ff7777;">Debug Mode -- localhost</div>';
  
  define('BASE_URL', 'http://localhost/gaslog/trunk/');
  define('FILTER_TEXT', '(occlness)');
}
else {
  define('BASE_URL', 'http://gas.randomland.net/');
  define('FILTER_TEXT', '(rlgaslog)');
}