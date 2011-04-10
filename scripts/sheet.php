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
  protected $id;    /* string */
  
  public function __construct($d, $s) {
    if (! isset($d) || ! ($d instanceof GlDoc)) {
      throw new Exception('GlSheet::__construct(): not a valid spreadsheet');
    }
    
    if (! isset($s) || ! ($s instanceof Zend_Gdata_Spreadsheets_WorksheetEntry)) {
      throw new Exception('GlSheet::__construct(): not a valid worksheet');
    }
    
    $this->doc = $d;
    $this->sheet = $s;
    
    $parts = explode('/', $this->sheet->id->text);
    //print_r($parts);
    $this->id = $parts[8];
  }
  
  protected function service() {
    return $this->doc->getApp()->getService();
  }
}

/**
 * GlDataSheet is an instance of a raw-data input sheet used by GlDoc
 */
class GlDataSheet extends GlSheet {
  const SHEET_TITLE = 'Form Data';
  
  /*public static function createNew() {
    //TODO: figure out how to create new worksheet with proper columns, etc.
    return null;
  }*/
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlDataSheet::SHEET_TITLE) {
      throw new Exception('GlDataSheet::__construct(): wrong sheet ');
    }
  }
  
  public function insert($values) {
    $entry = $this->service()->insertRow($values, $this->doc->id(), $this->id);
    return $values;
  }
}

/**
 * GlCalcSheet is an instance of a calculation worksheet used by GlDoc
 */
class GlStatSheet extends GlSheet {
  const SHEET_TITLE = 'Stats';
  
  /*public static function createNew() {
    //TODO: figure out how to create columns and set formulas.
    return null;
  }*/
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlStatSheet::SHEET_TITLE) {
      throw new Exception('GlStatSheet::__construct(): wrong sheet');
      // "'" . this->sheet->title->text . "'"
    }
  }
  
  public function getStats() {
    //TODO: figure out how to use "range" parameter so we only query for what
    // we need.
    $raw = $this->sheet->getContentsAsCells();
    $ret = array();
    
    $cols = array(
      'last' => 'B', 
      'previous' => 'C',
      'month' => 'D',
      'all' => 'G'
    );
    
    foreach ($cols as $colName => $colLetter) {
      $ret[$colName]['datetime']     = (isset($raw[$colLetter . '2']) ? $raw[$colLetter . '2']['value'] : null);
      $ret[$colName]['mpg']          = $raw[$colLetter . '3']['value'];
      $ret[$colName]['cost']         = $raw[$colLetter . '4']['value'];
      $ret[$colName]['tripdistance'] = $raw[$colLetter . '5']['value'];
      $ret[$colName]['daysbetween']  = $raw[$colLetter . '6']['value'];
      $ret[$colName]['costperday']   = $raw[$colLetter . '7']['value'];
      $ret[$colName]['costpermile']  = $raw[$colLetter . '8']['value'];
      $ret[$colName]['location']     = $raw[$colLetter . '9']['value'];
    }
    
    return $ret;
  }
  
  //TODO: how do I make it auto-create rows when they're entered in the data 
  // sheet?
}