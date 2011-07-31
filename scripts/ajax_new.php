<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Adds a new entry to a gas log spreadsheet.
 */

require_once 'globals.php';

/**
 * Returns the default values used for convenience of filling out the form 
 * faster.
 */
function getDefaults($app) {
  // Get stats object
  $stats = $app->getDoc()->stats();
  
  // Return some defaults
  return array(
     'mileage' =>  (isset($stats['last']['mileage']) && isset($stats['all']['tripdistance'])) ? getMiles($stats['last']['mileage'] + $stats['all']['tripdistance']) : '',
     'location' => (isset($stats['all']['location']) ? $stats['all']['location'] : ''),
     'pricepergallon' => (isset($stats['last']['pricepergallon'])) ? getGasMoney($stats['last']['pricepergallon']) : ''
     //TODO: 'favorite' grade
  );
}

/**
 * Returns an associative array of entry-form input errors.
 */
function getErrors() {
  $errors = array();
  
  // Check input validity
  if (! isset($_REQUEST['datetime']) || strtotime($_REQUEST['datetime']) === FALSE) {
    $errors['datetime'] = true;
  }
  
  if (! isset($_REQUEST['mileage']) || ! is_numeric($_REQUEST['mileage'])) {
    $errors['mileage'] = true;
  }
  
  if (! isset($_REQUEST['pricepergallon']) || ! is_numeric($_REQUEST['pricepergallon'])) {
    $errors['pricepergallon'] = true;
  }
  
  if (! isset($_REQUEST['gallons']) || ! is_numeric($_REQUEST['gallons'])) {
    $errors['gallons'] = true;
  }
  
  if (! isset($_REQUEST['grade']) || 
      ($_REQUEST['grade'] != 0  && $_REQUEST['grade'] != 1 && $_REQUEST['grade'] != 2)
     ) {
    $errors['grade'] = true;
  }
  
  if (isset($_REQUEST['pumpprice']) && $_REQUEST['pumpprice'] != '' && ! is_numeric($_REQUEST['pumpprice'])) {
    $errors['pumpprice'] = true;
  }
  
  return $errors;
}

/**
 * Return the straight input values
 */
function getInputValues() {
  $vals = array();
  
  $vals['datetime'] = @$_REQUEST['datetime'];
  $vals['mileage'] = @$_REQUEST['mileage'];
  $vals['location'] = @$_REQUEST['location'];
  $vals['pricepergallon'] = @$_REQUEST['pricepergallon'];
  $vals['gallons'] = @$_REQUEST['gallons'];
  $vals['grade'] = @$_REQUEST['grade'];
  $vals['pumpprice'] = @$_REQUEST['pumpprice'];
  $vals['notes'] = @$_REQUEST['notes'];
  
  return $vals;
}

/**
 * Returns an array of form values (taken from $_POST) and sanitized for input
 * in the spreadsheet.
 */
function getSanitizedValues() {
  //TODO: actually clean the values. Could probably put it in the 
  // case above...
  //
  //TDOD: consider using $_POST instead of $_GET!
  //
  // Also, this is probably overkill when we could just copy the
  // entirety of $_POST...not that we can guarantee people won't
  // jam malicious data into it.
  $cleanVals = array();
  $cleanVals['datetime'] = date(GlApp::DATE_FORMAT, strtotime($_REQUEST['datetime']));
  $cleanVals['mileage'] = $_REQUEST['mileage'];
  $cleanVals['location'] = $_REQUEST['location'];
  $cleanVals['pricepergallon'] = $_REQUEST['pricepergallon'];
  $cleanVals['gallons'] = $_REQUEST['gallons'];
  $cleanVals['grade'] = $_REQUEST['grade'];
  $cleanVals['pumpprice'] = $_REQUEST['pumpprice'];
  $cleanVals['notes'] = htmlspecialchars($_REQUEST['notes']);
  
  return $cleanVals;
}

//
// Entry point
//
//HACK: remove this or restructure program so we're not opening the session
// more than once.
if (! session_id()) {
  session_start();
}

// Set the document ID (or return error if it's missing)
if (! isset($_GET['doc']) || strlen($_GET['doc']) < 1) {
  echo json_encode(array(
    'response' => 'new_no_doc',
    'values' => getInputValues()
  ));
  exit;
}
// else:
$docId = $_GET['doc'];

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
  // Create app
  $app = new GlApp($auth);
  
  // Get document
  if (! $app->open($docId)) {
    echo json_encode(array(
      'response' => 'new_bad_document',
      'message' => $docID . ' is not a valid document'
    ));
  }
  else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'defaults') {
    // Return array of priming data (for example, last milage + previous trip
    // length, 'favorite' location, last price per gallon)
    echo json_encode(array(
      'response' => 'new_defaults',
      'values' => getDefaults($app)
    ));
  }
  else {  
    // Check for errors in input
    $errors = getErrors();
    
    if (count($errors) > 0) {
      // Validation errors. Return list of invalid fields along with the
      // original input.
      echo json_encode(array(
        'response' => 'new_validation_error',
        'errors' => $errors,
        'values' => getInputValues()
      ));
    }
    else {
      // Fields are valid. Submit it.
      $doc = $app->getDoc();
      $cleanVals = getSanitizedValues();
      $doc->insert($cleanVals);
      $entries = $app->getDoc()->mostRecentEntries(0, 1);
      
      echo json_encode(array(
        'response' => 'new_success',
        'errors' => array(),
        'values' => $cleanVals,
        'stats' => $entries[0]
      ));
    }
  }
}