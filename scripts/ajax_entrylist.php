<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Returns a list of entries for a particular gas log.
 */

require_once 'globals.php';

//HACK: remove this or restructure program so we're not opening the session
// more than once.
if (! session_id()) {
  session_start();
}

// Set the document ID (or return error if it's missing)
if (! isset($_GET['id']) || strlen($_GET['id']) < 1) {
  echo json_encode(array(
    'response' => 'entrylist_no_doc'
  ));
  exit;
}
// else:
$docId = $_GET['id'];

// Create or get handle to the authentication object.
if (! isset($_SESSION['GlApp_GlOAuth'])) {
  $auth = new GlOAuth();
  $_SESSION['GlApp_GlOAuth'] = $auth;
}
else {
  $auth = $_SESSION['GlApp_GlOAuth'];
}

// Attempt to log in
if (! $auth->logIn('#')) {
  echo json_encode(array(
    'response' => 'login_unauthorized'
  ));
}
else {
  $app = new GlApp($auth);
  
  //TODO: should probably use GlApp::open($id);
  $doc = new GlDoc($app, $docId, true);
  
  //TODO: version check?
  
  if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
    $offset = $_GET['offset'];
  }
  else {
    $offset = 0;
  }
  
  if (isset($_GET['num']) && is_numeric($_GET['offset'])) {
    $num = $_GET['num'];
  }
  else {
    $num = 5;
  }
  
  $retVal = $doc->mostRecentEntries($offset, $num);
  
  echo json_encode(array(
    'response' => 'entrylist_success',
    'entrylist' => $retVal
  ));
}