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
  const VERSION = APP_VERSION;
  
  const DATE_FORMAT = 'Y-m-d H:i:s';
  const DATE_FORMAT_FULL = 'l, F jS, Y';
  
  protected $auth;    /* GlAuth */
  protected $service; /* Zend_Gdata_Spreadsheets */
  protected $doc;     /* GlDoc */
  protected $docs;
  
  public function __construct($authInstance) {
    $this->auth = $authInstance;
    $this->service = $this->auth->createService();
  }
  
  public function getService() {
    return $this->service;
  }
  
  public function getDoc() {
    return $this->doc;
  }
  
  public function open($id = null, $getSheets = true) {
    // Open document using passed in parameter.
    $this->doc = new GlDoc($this, $id, $getSheets);
    return true;
  }
  
  public function getAvailable($refresh = false) {
  	if (count($this->docs) < 1 || $refresh) {
  		// Get from Google Docs
	    $feed = $this->service->getSpreadsheetFeed();
	    $this->docs = array();
	   	
	   	foreach ($feed as $doc) {
				if (stripos($doc->title->text, FILTER_TEXT) !== FALSE) {
					array_push($this->docs, new GlDoc($this, $doc, true));
				}
			}
		}
		
		return $this->docs;
  }
  
  /*public function saveGetVars() {
    $_SESSION[GlApp::SESSION_STORED_GET] = serialize($_GET);
  }
  
  public function mergeSavedGetVars() {
    // Merge $_GET parameters with stored parameters. If $_GET contains some
    // parameters stored in $preLoginState, $_GET will take precedence.
    if (isset($_SESSION[GlApp::SESSION_STORED_GET])) {
      $storedGet = unserialize($_SESSION[GlApp::SESSION_STORED_GET]);
      $_GET = array_merge($storedGet, $_GET);
      unset($_SESSION[GlApp::SESSION_STORED_GET]);
    }
  }*/
}