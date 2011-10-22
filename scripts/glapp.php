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
  
  public function getAvailable() {
		$feed = $this->service->getSpreadsheetFeed();
		$this->docs = array();
		
		foreach ($feed as $doc) {
			if (stripos($doc->title->text, FILTER_TEXT) !== FALSE) {
				array_push($this->docs, new GlDoc($this, $doc, true));
			}
		}
	
		return $this->docs;
  }
}