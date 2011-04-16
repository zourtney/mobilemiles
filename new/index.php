<?php

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