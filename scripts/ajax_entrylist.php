<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Returns a list of entries for a particular gas log.
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
else if (! isset($_GET['id']) || strlen($_GET['id']) < 1) {
	echo json_encode(array(
		'response' => 'entrylist_no_doc'
	));
}
else {
	// Normalize 'refresh' flag
  $refresh = false;
  if (isset($_GET['refresh']) && ($_GET['refresh'] == '1' || $_GET['refresh'] == 'true')) {
  	$refresh = true;
  }
  
	// Set document ID
	$docId = $_GET['id'];
	
	//echo "refresh = $refresh, docId = $docId, cookieId = " . GlCookie::getDocId() . ', equal = ' . ($docId == GlCookie::getDocId());
	
	if (! $refresh && $docId == GlCookie::getDocId()) {
		// Get cached entries
		$retVal = GlCookie::getDocEntries();
	}
	
	// Get entries
	if (! isset($retVal)) {
		// Create or get handle to the app object
		if (! isset($_SESSION['GlApp_App'])) {
			$app = new GlApp($auth);
			$_SESSION['GlApp_App'] = $app;
		}
		else {
			$app = $_SESSION['GlApp_App'];
		}
		
		// Get doc
		$doc = $app->getDoc();
		
		// Check existing against ID param
		if (! isset($doc) || $doc->id != $docId) {
			// Open it...
			try {
				$app->open($docId, true);
				$doc = $app->getDoc();
			}
			catch (Exception $ex) {
				// Die here with error
				echo json_encode(array(
					'response' => 'entrylist_open_failed'
				));
				exit;
			}
		}
		
		if (! isset($retVal)) {
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
			
			// Save data to cookie (but only for 'latest' entries)
			if ($offset == 0) {
				GlCookie::setDocId($docId);
				GlCookie::setDocEntries($retVal);
			}
		}
	}
	
	echo json_encode(array(
		'response' => 'entrylist_success',
		'entrylist' => $retVal
	));
}