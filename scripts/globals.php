<?php

// Constants
define('DEBUG', true);

if (DEBUG) {
  define('BASE_URL', 'http://localhost/gaslog/trunk/');
}
else {
  define('BASE_URL', 'http://gas.randomland.net/');
}