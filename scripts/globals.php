<?php

// Constants
define('DEBUG', (strstr($_SERVER['SERVER_NAME'], 'localhost') !== false));

if (DEBUG) {
  // Put something obnoxious at the top of the page :-)
  echo '<div style="margin: 0; padding: 1em; display: block; background: #ff7777;">Debug Mode -- localhost</div>';
  define('BASE_URL', 'http://localhost/gaslog/trunk/');
}
else {
  define('BASE_URL', 'http://gas.randomland.net/');
}