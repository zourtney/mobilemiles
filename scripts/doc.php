<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlDoc is an instance of a document used by GlApp.
 */

class GlDoc {
  protected $app;       /* GlApp */
  protected $doc;       /* Zend_Gdata_SpreadsheetEntry */
  protected $id;        /* string */
  protected $dataSheet; /* GlDataSheet */
  
  public static function createNew() {
    //TODO: figure out how to create a document with all of the worksheets we
    // want. Append GlDataSheet::createNew(), and others. This will be the way 
    // to create a new "log"
    $dataSheet = GlDataSheet::createNew();
    $calcSheet = GlCalcSheet::createNew();
    
    //TODO: append to new spreadsheet
    
    return null;
  }
  
  public function __construct($app, $id, $getSheets = true) {
    if (! $app instanceof GlApp) {
      throw new Exception('GlDoc::__construct(): invalid app pointer');
    }
    
    // Save GlApp pointer
    $this->app = $app;
    
    if (is_string($id)) {
      // Load from ID string
      try {
        $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
        $query->setSpreadsheetKey($id);
        $this->doc = $this->app->getService()->getSpreadsheetEntry($query);
      }
      catch (Zend_Gdata_App_Exception $e) {
        GlApp::setLastError($e);
        throw new Exception('GlDoc::__construct(): unable to create document');
      }
    }
    else if ($id instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry) {
      // Set equal to instance passed in
      $this->doc = $id;
    }
    
    // Get the document ID string...always the last param(?)
    $parts = explode('/', $this->doc->id->text);
    //print_r($parts);
    $this->id = $parts[count($parts) - 1];
    
    // You may not want to get references to the worksheets
    if ($getSheets) {
      // Get the raw-data input sheet
      $this->dataSheet = new GlDataSheet($this, $this->getSheetByTitle(GlDataSheet::SHEET_TITLE));
      
      // Get the calculations sheet
      $this->calcSheet = new GlCalcSheet($this, $this->getSheetByTitle(GlCalcSheet::SHEET_TITLE));
    }
  }
  
  protected function getSheetByTitle($title) {
    $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
    $query->setSpreadsheetKey($this->id);
    $query->setTitle($title);
    return $this->app->getService()->getWorksheetEntry($query);
  }
  
  public function insert($vals) {
    echo "<h3>I can't let you do that.</h3>";
  }
  
  public function id() {
    return $this->id;
  }
  
  public function url() {
    return BASE_URL . '?id=' . $this->id;
  }
  
  public function formUrl() {
    return BASE_URL . '?id=' . $this->id . '&action=submitnew';
  }
  
  public function newUrl() {
    return BASE_URL . '?id=' . $this->id . '&action=new';
  }
  
  public function title() {
    return $this->doc->title->text;
  }
}
