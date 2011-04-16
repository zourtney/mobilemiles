<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This pseudo-page redirects you to the main application with the mobile 
 * styling enabled. Alternatively, you can add 'm=true' to the get parameter of
 * any page.
 */

// Global constants
require_once '../scripts/globals.php';

// Set the "is mobile" session variable
session_start();
$_SESSION['mobile'] = true;

// Go back to the main page. It will check this session variable
header('Location:' . BASE_URL);