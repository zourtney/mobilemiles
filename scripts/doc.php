<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlDoc is an instance of a document used by GlApp.
 */

class GlDoc {
  const VERSION = SPREADSHEET_VERSION;
  const MASTER_URL = SPREADSHEET_MASTER_URL;
  
  protected $app;          /* GlApp */
  protected $doc;          /* Zend_Gdata_SpreadsheetEntry */
  protected $id;           /* string */
  protected $dataSheet;    /* GlDataSheet */
  protected $calcSheet;    /* GlCalcSheet */
  protected $statSheet;    /* GlStatSheet */
  protected $versionSheet; /* GlVersionSheet */
  
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
        echo $e;
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
    //TODO: change so you specify the sheets you want, or just get them when you
    // need them.
    if ($getSheets) {
      // Get the raw-data input sheet
      $this->dataSheet = new GlDataSheet($this, $this->getSheetByTitle(GlDataSheet::SHEET_TITLE));
      
      // Get the calculations sheet
      $this->calcSheet = new GlCalcSheet($this, $this->getSheetByTitle(GlCalcSheet::SHEET_TITLE));
      
      // Get the stats sheet
      $this->statSheet = new GlStatSheet($this, $this->getSheetByTitle(GlStatSheet::SHEET_TITLE));
      
      // Get the version sheet
      $this->versionSheet = new GlVersionSheet($this, $this->getSheetByTitle(GlVersionSheet::SHEET_TITLE));
    }
  }
  
  public function getApp() {
    return $this->app;
  }
  
  protected function getSheetByTitle($title) {
    $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
    $query->setSpreadsheetKey($this->id);
    $query->setTitle($title);
    return $this->app->getService()->getWorksheetEntry($query);
  }
  
  public function insert($values) {
    // Insert into the 'Form Data' sheet
    $entry = $this->dataSheet->insert($values);
  }
  
  public function id() {
    return $this->id;
  }
  
  /*public function url() {
    return BASE_URL . '?id=' . $this->id;
  }
  
  public function formUrl() {
    return BASE_URL . '?id=' . $this->id . '&action=submitnew';
  }
  
  public function newUrl() {
    return BASE_URL . '?id=' . $this->id . '&action=new';
  }*/
  
  public function title() {
    return trim(str_replace(FILTER_TEXT, '', $this->doc->title->text));
  }
  
  public function stats() {
    $stats = $this->statSheet->getStats();
    return $stats;
  }
  
  public function mostRecentEntries($offset = 0, $num = -1) {
    //TODO: merge calcs.MPG column with location, etc on FormData sheet.
    // (or create yet another worksheet)
    $dataRows = $this->dataSheet->mostRecentEntries($offset, $num);
    $calcRows = $this->calcSheet->mostRecentEntries($offset, $num);
    $stats = $this->statSheet->getStats();
    
    $ret = array();
    
    for ($i = 0; $i < count($dataRows); $i++) {
      $dr = $dataRows[$i];
      $cr = $calcRows[$i];
      
      array_push($ret, array(
        'datetime' => $dr[0],
        'friendlydatetime' => getFriendlyDatetime($dr[0]),
        'mileage' => getMiles($dr[1]),
        'location' => $dr[2],
        'gallons' => $dr[5],
        'distance' => getMiles($cr[1]),
        'mpg' => getMpg($cr[2]),
        'mpgdelta' => getMpg($cr[2] - $stats['all']['mpg']),
        'pumpprice' => getMoney($dr[6]),
        'pricepergallon' => getGasMoney($dr[3])
      ));
    }
    
    return $ret;
  }
  
  public function getVersionInfo() {
    $version = $this->versionSheet->getVersionInfo();
    return $version;
  }
}
