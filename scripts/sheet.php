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
  
  public function mostRecentEntries($offset = 0, $num = -1) {
    //TODO: filter so we're only fetching the rows we want!
    
    $query = new Zend_Gdata_Spreadsheets_ListQuery();
    $query->setSpreadsheetKey($this->doc->id);
    $query->setWorksheetId($this->id);
    
    $feed = $this->service()->getListFeed($query);
    $ret = array();
    
    foreach ($feed->entries as $entry) {
      $row = $entry->getCustom();
      
      $cellData = array();
      foreach($row as $cell) {
        array_push($cellData, $cell->getText());
      }
      
      array_push($ret, $cellData);
    }
    
    return array_splice(array_reverse($ret), $offset, $num);
  }
}

/**
 * GlDataSheet is an instance of a raw-data input sheet used by GlDoc
 */
class GlDataSheet extends GlSheet {
  const SHEET_TITLE = 'Form Data';
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlDataSheet::SHEET_TITLE) {
      throw new Exception('GlDataSheet::__construct(): wrong sheet ');
    }
  }
  
  public function insert($values) {
    $entry = $this->service()->insertRow($values, $this->doc->id, $this->id);
    return $values;
  }
}

/**
 * GlCalcSheet is an instance of a calculations worksheet used by GlDoc
 */
class GlCalcSheet extends GlSheet {
  const SHEET_TITLE = 'Calculations';
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlCalcSheet::SHEET_TITLE) {
      throw new Exception('GlStatSheet::__construct(): wrong sheet');
    }
  }
}

/**
 * GlCalcSheet is an instance of a statistical worksheet used by GlDoc
 */
class GlStatSheet extends GlSheet {
  const SHEET_TITLE = 'Stats';
  
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
      $ret[$colName]['mileage']      = (isset($raw[$colLetter . '10']['value']) ? $raw[$colLetter . '10']['value'] : null);
      $ret[$colName]['pricepergallon'] = $raw[$colLetter . '11']['value'];
    }
    
    return $ret;
  }
  
  //TODO: how do I make it auto-create rows when they're entered in the data 
  // sheet?
}

class GlVersionSheet extends GlSheet {
  const SHEET_TITLE = 'Version';
  
  public function __construct($d, $s) {
    parent::__construct($d, $s);
    
    if ($this->sheet->title->text !== GlVersionSheet::SHEET_TITLE) {
      throw new Exception('GlVersionSheet::__construct(): wrong sheet');
    }
  }
  
  public function getVersionInfo() {
    //TODO: figure out how to use "range" parameter so we only query for what
    // we need.
    $raw = $this->sheet->getContentsAsCells();
    $ret = array();
    
    $ret['app'] = (isset($raw['B2']['value']) && is_numeric($raw['B2']['value'])) ? $raw['B2']['value'] : null;
    
    $ret['doc'] = (isset($raw['B3']['value']) && is_numeric($raw['B3']['value'])) ? $raw['B3']['value'] : null;
    
    return $ret;
  }
}