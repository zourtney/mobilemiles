<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file contains utility functions used by the Gas Log application.
 */

/**
 * Returns a green or red color based on how close $value is to $max. 
 * Additionally, you may narrow the color range by setting $dimmest and/or
 * $brightest (dec equivilent of a single hex character)
 */
function getTrendColor($value, $max, $dimmest = 0x3, $brightest = 0xe) {
    // Do a simple range conversion:
    //   $value     $hex
    //  -------- = ------
    //    $max       15
    //
    // ...then use min() and max() to make sure it falls in the desired color
    // range. NOTE: $dimmest and $brightest are defined in HEX while the
    // equation is defined in DEC. The min() and max() functions seem to handle
    // this properly. Yay!
    //
    // ...finally, convert it to HEX.
    $hex = dechex(min(max((abs($value) * 15) / $max, $dimmest), $brightest));
    
    // Use single hex value to create a 6-digit color code.
    if ($value >= 0)
      $color = "#00" . $hex . $hex . "00";
    else
      $color = "#" . $hex . $hex . "0000";
    
    // Debug...
    //echo "(|$value| * 16) / $max = $result ==> $result = $hex => <span style='color: $color'>$color</span>";
    return $color;
}

/**
 * Returns a text string based on where $value is compared to $slight and 
 * $significant. Examples:
 *   (5, 10, 50)  => "slightly better"
 *   (20, 10, 50) => "better"
 *   (60, 10, 50) => "significantly better"
 *
 * Similarly, you'll get "worse" text if $value is negative.
 */
function getThresholdText($value, $slightValue, $significantValue, 
  $better = 'better', $worse = 'worse', 
  $slightly = 'slightly', $significantly = 'significantly') {
  // Create return string
  $str = '';  

  // Add 'slightly' or 'significantly' values, if applicable
  if (abs($value) <= $slightValue && strlen($slightly) > 0)
    $str .= $slightly . ' ';
  else if (abs($value) > $significantValue && strlen($significantly) > 0) 
    $str .= $significantly . ' ';
  
  // Add 'better' or 'worse' text.
  if ($value >= 0)
    $str .= $better;
  else 
    $str .= $worse;
  
  return $str;
}

/**
 * Returns a user-friendly description of the date. For example:
 *   - last Wednesday night
 *   - yesterday evening
 *   - 2011-04-14
 */
/*function getFriendlyDatetime($datetime, $stats) {
  $str = '';
  // Do a time-independent day's between calculation (there might be a better
  // way to do this...)
  $daysBetween = ((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',$datetime))) / 86400);
  
  // Print out the day
  //echo $daysBetween;
  if ($daysBetween < 0)
    $str .= "on some future ";
  else if ($daysBetween < 1)
    $str .= "this ";
  else if ($daysBetween < 2)
    $str .= "yesterday ";
  else if ($daysBetween <= 7)
    $str .= "last " . strftime("%A", $datetime) . " ";
  else
    $str .= "on " . date(GlApp::DATE_FORMAT_FULL, strtotime($stats['last']['datetime']));
  
  // If recent, give them the time of day
  if ($daysBetween <= 7) {
    $lastHour = date("G", $datetime);
    if ($lastHour < 12)
      $str .= "morning";
    else if ($lastHour < 16)
      $str .= "afternoon";
    else if ($lastHour < 21)
      $str .= "evening";
    else
      $str .= "night";
  }
  
  return $str;
}
*/

function getFriendlyDatetime($datetime) {
  $time = strtotime($datetime);
  $daysBetween = (time() - $time) / 86400;
  
  if ($daysBetween < 0.5) {
    $secondsBetween = (time() - $time);
    
    if ($secondsBetween < 3600)
      return round($secondsBetween / 60) . ' minutes ago';
    if ($secondsBetween < 7200)
      return 'an hour ago';
    return round($secondsBetween / 3600) . ' hours ago';
  }
  if ($daysBetween < 1)
    return 'Today';
  if ($daysBetween < 2)
    return 'Yesterday';
  if ($daysBetween < 5)
    return round($daysBetween) . ' days ago';
  if ($daysBetween < 8)
    return strftime('%A', $time);
  return strftime('%m/%d/%y', $time);
}

/**
 * Returns an up or down arrow, formatted in HTML and assigned the color passed
 * in.
 */
function getArrowHtml($color, $value) {
  $str = '<span class="arrow" style="color: ' . $color . ';">';
  
  if ($value >= 0)
    $str .= '&uarr;';
  else
    $str .= '&darr;';
  
  return $str . '</span>'; 
}

/**
 * Returns a copy of the value passed in, formatted for MPG display.
 * NOTE: does not apend 'mpg'. This is done using CSS.
 */
function getMpg($value) {
  return round($value, 2);
}

/**
 * Returns a copy of the value passed in, formatted for distance display.
 * NOTE: does not append 'mi'. This is done using CSS.
 */
function getMiles($value) {
  return (int)$value;
}

/**
 * Returns a copy of the value passed in, formatted for monetary display.
 * NOTE: does not prepend '$'. This is done using CSS.
 */
function getMoney($value) {
  return sprintf("%01.2f", round($value, 2));
}

/**
 * Returns a copy of the value passed in, formatted for price-per-gallon 
 * display.
 * NOTE: does not prepend '$'. This is done using CSS.
 */
function getGasMoney($value) {
  return sprintf("%01.3f", round($value, 3));
}

/**
 * Returns a copy of the value passed in, formatted for percentage display.
 * NOTE: does not append '%'. This is done using CSS.
 */
function getPercent($value) {
  return sprintf("%01.2f", round($value, 2));
}

/**
 * Returns an associative array of entry-form input errors.
 */
/*function getFormErrors($mode) {
  $errors = array();
        
  if ($mode != GlApp::MODE_SUBMITNEW) {
    return $errors;
  }
  
  // Check input validity
  if (! isset($_POST['datetime']) || strtotime($_POST['datetime']) === FALSE) {
    $errors['datetime'] = true;
  }
  
  if (! isset($_POST['mileage']) || ! is_numeric($_POST['mileage'])) {
    $errors['mileage'] = true;
  }
  
  if (! isset($_POST['pricepergallon']) || ! is_numeric($_POST['pricepergallon'])) {
    $errors['pricepergallon'] = true;
  }
  
  if (! isset($_POST['gallons']) || ! is_numeric($_POST['gallons'])) {
    $errors['gallons'] = true;
  }
  
  if (! isset($_POST['grade']) || 
      ($_POST['grade'] != 0  && $_POST['grade'] != 1 && $_POST['grade'] != 2)
     ) {
    $errors['grade'] = true;
  }
  
  if (isset($_POST['pumpprice']) && $_POST['pumpprice'] != '' && ! is_numeric($_POST['pumpprice'])) {
    $errors['pumpprice'] = true;
  }
  
  //print_r($_POST);
    //echo "You have " . count($errors) . " errors";
    //print_r($errors);
  return $errors;
}

/**
 * Returns an array of form values (taken from $_POST) and sanitized for input
 * in the spreadsheet.
 */
/*function sanitizeFormValues() {
  //TODO: actually clean the values. Could probably put it in the 
  // case above...
  //
  // Also, this is probably overkill when we could just copy the
  // entirety of $_POST...not that we can guarantee people won't
  // jam malicious data into it.
  $cleanVals = array();
  $cleanVals['datetime'] = date(GlApp::DATE_FORMAT, strtotime($_POST['datetime']));
  $cleanVals['mileage'] = $_POST['mileage'];
  $cleanVals['location'] = $_POST['location'];
  $cleanVals['pricepergallon'] = $_POST['pricepergallon'];
  $cleanVals['gallons'] = $_POST['gallons'];
  $cleanVals['grade'] = $_POST['grade'];
  $cleanVals['pumpprice'] = $_POST['pumpprice'];
  $cleanVals['notes'] = htmlspecialchars($_POST['notes']);
  
  return $cleanVals;
}

/**
 * 
 */
/*function printHeader($h1, $h2 = '', $back = null, $showAbout = true) {
  $_SESSION['header_h1'] = $h1;
  
  if (strlen($h2) > 0) {
    $_SESSION['header_h2'] = $h2;
  }
  
  if ($back != null && count($back) > 0) {
    $_SESSION['header_back'] = $back;
  }
  
  //if ($showAbout) {
    $_SESSION['header_showabout'] = $showAbout;
  //}
  
  include(TEMPLATE_BASE . '/header.php');
}

/*
 *
 */
/*function printFooter() {
  include(TEMPLATE_BASE . '/footer.php');
}*/