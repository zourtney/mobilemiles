<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Server-side of AJAX call to get the name of a location as stored in a 
 * spreadsheet.
 */

// Start session
session_start();

// Make sure we're authorized
$auth = new GlAuth();

if ($auth->login()) {
  // Make sure they've provided the proper parameters
  if (isset($_POST['id'] &&
      isset($_POST['long']) && is_numeric($_POST['long']) &&
      isset($_POST['lat']) && is_numeric($_POST['lat'])) {
    
    // Store them in local variables
    $id = $_POST['id'];
    $long = $_POST['long'];
    $lat = $_POST['lat'];
    
    // Create app
    
    // Create document
    
    // Get location sheet
    
    // Find match
  }
}

json_encode(array("name"=>"John","time"=>"2pm"));