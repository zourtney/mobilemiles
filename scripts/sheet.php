<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlSheet is an instance of a spreadsheet worksheet used by GlDoc
 */

class GlSheet {
  protected $doc;   /* GlDoc */
  protected $sheet; /* Zend_Gdata_Spreadsheets_WorksheetEntry */
  
  public function __construct($d, $s) {
    if (! isset($d) || ! ($d instanceof GlDoc)) {
      throw new Exception('GlSheet::__construct(): not a valid spreadsheet');
    }
    
    if (! isset($s) || ! ($s instanceof Zend_Gdata_Spreadsheets_WorksheetEntry)) {
      throw new Exception('GlSheet::__construct(): not a valid worksheet');
    }
    
    $this->doc = $d;
    $this->sheet = $s;
  }
}

/**
 * GlDataSheet is an instance of a raw-data input sheet used by GlDoc
 */
class GlDataSheet extends GlSheet {
  const SHEET_TITLE = 'Form Data';
  
  public static function createNew() {
    //TODO: figure out how to create new worksheet with proper columns, etc.
    return null;
  }
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlDataSheet::SHEET_TITLE) {
      throw new Exception('GlDataSheet::__construct(): wrong sheet ');
    }
  }
  
  public function insert($values) {
    /* array(datetime, 
             mileage,
             location,
             pricePerGallon,
             gallons,
             pumpPrice,
             notes
       );
    */
    // Get appropriate row based on timestamp (or can we set a column sort?)
    
    // Insert new values
    //$this->sheet->insertRow($values, $key, $this->sheet->id->text);
  }
}

/**
 * GlCalcSheet is an instance of a calculation worksheet used by GlDoc
 */
class GlCalcSheet extends GlSheet {
  const SHEET_TITLE = 'Calculations';
  
  public static function createNew() {
    //TODO: figure out how to create columns and set formulas.
    return null;
  }
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlCalcSheet::SHEET_TITLE) {
      throw new Exception('GlCalcSheet::__construct(): wrong sheet');
      // "'" . this->sheet->title->text . "'"
    }
  }
  
  //TODO: how do I make it auto-create rows when they're entered in the data 
  // sheet?
}