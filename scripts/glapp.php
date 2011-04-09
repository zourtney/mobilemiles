<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Server-side logic needed to save and retrieve gas-log info from the Google
 * spreadsheet.
 */

class GlApp {
  const FILTER_TEXT = 'occlness';
  
  const GET_ID = 'id';
  const GET_ACTION = 'action';
  const GET_ACTION_NEW = 'new';
  const GET_ACTION_SUBMITNEW = 'submitnew';
  //const GET_ACTION_NEWDOC = 'newdoc';
  //const GET_ACTION_SUBMITNEWDOC = 'submitnewdoc';
  
  const MODE_NONE = 0;
  const MODE_DOCONLY = 1;
  const MODE_NEW = 2;
  const MODE_SUBMITNEW = 3;
  //const MODE_NEWDOC = 4;
  //const MODE_SUBMITNEWDOC = 5;
  
  const DATE_FORMAT = 'Y-m-d H:i:s';
  const DATE_FORMAT_FULL = 'l, F jS, Y';
  
  protected static $lastError; /* any type */
  
  protected $auth;    /* GlAuth */
  protected $service; /* Zend_Gdata_Spreadsheets */
  protected $doc;     /* GlDoc */
  
  public function __construct($authInstance) {
    $this->auth = $authInstance;
    $this->service = $this->auth->createService();
  }
  
  /**
   * Returns the current application state. Based mostly of GET and POST
   * parameters.
   */
  public function getMode() {
    // Document identifier present
    if (isset($_GET[GlApp::GET_ID])) {
      // "Action" parameter
      if (isset($_GET[GlApp::GET_ACTION])) {
        if ($_GET[GlApp::GET_ACTION] == GlApp::GET_ACTION_NEW)
          return GlApp::MODE_NEW;
        if ($_GET[GlApp::GET_ACTION] == GlApp::GET_ACTION_SUBMITNEW)
          return GlApp::MODE_SUBMITNEW;
        return GlApp::MODE_NONE;
      }
      
      // No action, but we do have an ID
      return GlApp::MODE_DOCONLY;
    }
    /*else if (isset($_GET[GlApp::GET_ACTION])) {
      // Action, but no document
      if ($_GET[GlApp::GET_ACTION] == GlApp::GET_ACTION_NEWDOC)
        return GlApp::MODE_NEWDOC;
      if ($_GET[GlApp::GET_ACTION] == GlApp::GET_ACTION_SUBMITNEWDOC)
        return GlApp::MODE_SUBMITNEWDOC;
      
      // What are you trying to do?
      return GlApp::MODE_NONE;
    }*/
    
    // No ID, no discernible mode
    return GlApp::MODE_NONE;
  }
  
  public function getDoc() {
    return $this->doc;
  }
  
  public function getService() {
    return $this->service;
  }
  
  public function open($id = null) {
    if ($id == null) {
      $id = $_GET[GlApp::GET_ID];
    }
    
    // Open document using passed in parameter.
    $this->doc = new GlDoc($this, $id);
    
    return true;
  }
  
  public function getAvailable() {
    $docs = $this->service->getSpreadsheetFeed();
    $available = array();
    
    foreach ($docs as $doc) {
      if (stripos($doc->title->text, GlApp::FILTER_TEXT) !== FALSE) {
        array_push($available, new GlDoc($this, $doc, false));
      }
    }
    
    return $available;
  }
  /*
  public function createNewDoc($name) {
    
  }
  
  public function newDocUrl() {
    return BASE_URL . '?' . GlApp::GET_ACTION . '=' . GlApp::GET_ACTION_NEWDOC;
  }
  
  public function newDocFormUrl() {
    return BASE_URL . '?' . GlApp::GET_ACTION . '=' . GlApp::GET_ACTION_SUBMITNEWDOC;
  }
  */
  public static function setLastError($e) {
    GlApp::$lastError = $e;
  }
  
  public static function getLastError() {
    return GlApp::$lastError;
  }
  
  public static function clearLastError() {
    unset(GlApp::$lastError);
  }
}