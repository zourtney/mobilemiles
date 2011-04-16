<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This psuedo-page redirects you to the main application with the document ID
 * passed in, and takes you straight to the 'new entry' form.
 */

// Global constants
require_once '../scripts/globals.php';
require_once '../scripts/glapp.php';

session_start();

// Set the "is mobile" session variable, if it's in the get params
if (isset($_GET['mobile'])) {
  $_SESSION['mobile'] = true;
}

// Redirect to main page with 'action=new' and ID set.
if (isset($_GET[GlApp::GET_ID])) {
  header('Location:' . 
    BASE_URL . '?' . 
    GlApp::GET_ID . '=' . $_GET[GlApp::GET_ID] . '&' . 
    GlApp::GET_ACTION . '=' . GlApp::GET_ACTION_NEW
  );
}
else {
  // Go back to the main page. It will check this session variable
  header('Location:' . BASE_URL);
}