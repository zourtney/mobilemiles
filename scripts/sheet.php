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
    print_r($parts);
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
    //$service = $this->doc->getApp()->getService();
    //echo "<p><code>insertRow(..., " . $this->doc->id() . ", " . $this->id . ");</code></p>";
    //$entries = $this->sheet->getContentsAsRows();
    //echo var_export($values, true);
    
    $entry = $this->service()->insertRow($values, $this->doc->id(), $this->id);
    
    //TODO: not the right command. Do I need to explicitly save?
    //$this->sheet->save();
    //print_r($entry);
    
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
    
    $ret['all']['mpg']         = $raw['G2']['value'];
    $ret['all']['price']       = $raw['G3']['value'];
    $ret['all']['distance']    = $raw['G4']['value'];
    $ret['all']['daysbetween'] = $raw['G5']['value'];
    $ret['all']['costperday']  = $raw['G6']['value'];
    $ret['all']['location']    = $raw['G7']['value'];
    
    return $ret;
  }
  
  //TODO: how do I make it auto-create rows when they're entered in the data 
  // sheet?
}