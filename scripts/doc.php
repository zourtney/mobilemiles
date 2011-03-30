<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlDoc is an instance of a document used by GlApp.
 */

class GlDoc {
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
  
  public function __construct($val) {
    if (! $val instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry) {
      throw new Exception('GlDoc::__construct(): invalid document');
    }
    
    // Save document instance
    $this->doc = $val;
    
    // Get the document ID string
    $this->id = explode('/', $this->doc->id->text)[5];
    
    // Get the raw-data input sheet
    $this->dataSheet = new GlDataSheet($this->getSheetByTitle(GlDataSheet::SHEET_TITLE));
    
  }
  
  protected function getSheetByTitle($title) [
    $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
    $query->setSpreadsheetKey($this->id);
    $query->setTitle($title);
    return $this->doc->getWorksheetEntry($query);
  }
  
  public function id() {
    return $this->id;
  }
  
  public function title() {
    return $this->doc->title->text;
  }
}
