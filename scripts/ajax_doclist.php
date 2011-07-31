<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Returns a list of gas logs.
 */

require_once 'globals.php';

//HACK: remove this or restructure program so we're not opening the session
// more than once.
if (! session_id()) {
  session_start();
}

// Create the authentication object.
$auth = new GlOAuth();

if (! $auth->isLoggedIn() && ! $auth->hasRequestToken()) {
  //TODO: handle
  echo json_encode(array(
    'response' => 'login_unauthorized'
  ));
}
else if (! $auth->login($_GET['callee'])) {
  //TODO: handle
  echo json_encode(array(
    'response' => 'login_failure'
  ));
}
else {
  // Create instance of the app
  $app = new GlApp($auth);
  
  // Merge in pre-login vars (if any)
  $app->mergeSavedGetVars();
  
  // Get the application mode (show, new, etc)
  $docs = $app->getAvailable();
  
  //TODO: return JSON
  $retVal = array();
  
  foreach ($docs as $doc) {
    array_push($retVal, array(
      'id' => $doc->id(),
      'title' => $doc->title()
    ));
  }
  
  //array_push($retVal, array('url' => '#', 'title' => 'Just for good measure'));
  
  echo json_encode(array(
    'response' => 'doclist_success',
    'doclist' => $retVal
  ));
}