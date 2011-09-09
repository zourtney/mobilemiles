<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Returns a list of MobileMiles spreadsheets. If possible, it will pull the
 * list from a cookie for better performance.
 */

require_once 'globals.php';

// Start session
session_start();

// Create or get handle to the authentication object.
if (! isset($_SESSION['GlApp_GlOAuth'])) {
  $auth = new GlOAuth();
  $_SESSION['GlApp_GlOAuth'] = $auth;
}
else {
  $auth = $_SESSION['GlApp_GlOAuth'];
}

if (! $auth->isLoggedIn() && ! $auth->hasRequestToken()) {
  //TODO: handle
  echo json_encode(array(
    'response' => 'login_unauthorized'
  ));
}
else if (! $auth->logIn(@$_GET['callee'])) {
  //TODO: handle
  echo json_encode(array(
    'response' => 'login_failure'
  ));
}
else {
  // Normalize 'refresh' flag
  $refresh = false;
  if (isset($_GET['refresh']) && ($_GET['refresh'] == '1' || $_GET['refresh'] == 'true')) {
  	$refresh = true;
  }
  
  // Pull doc list from cookie
  $docs = GlCookie::getDocList();
  
  // Query Google Docs for document list
  if ($refresh || ! isset($docs)) {
  	// Create or get handle to the app object
		if (! isset($_SESSION['GlApp_App'])) {
			$app = new GlApp($auth);
			$_SESSION['GlApp_App'] = $app;
		}
		else {
			$app = $_SESSION['GlApp_App'];
		}
		
		// Get available documents
    $docs = $app->getAvailable($refresh);
	  
	  // Save list to cookie
		GlCookie::setDocList($docs);
  }
  
  echo json_encode(array(
    'response' => 'doclist_success',
    'doclist' => $docs
  ));
}