<!DOCTYPE html>
<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This simple application utilizes a Google Spreadsheet to keep track of gas
 * fill-up stats. There are two main functions right now:
 *   - printStats(): prints average mpg, cost, etc
 *   - printForm(): shows a form which allows the user to add a new entry.
 *                  Use this every time you fill up :-)
 */

// ****************************************************************************
// Include the loader script
require_once 'Zend/Loader.php';

// Load gdata modules
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');

// So PHP doesn't complain...
date_default_timezone_set('America/Los_Angeles');

// Global constants
require_once 'scripts/globals.php';

// Authentication functions
require_once 'scripts/auth.php';

// Application logic
require_once 'scripts/glapp.php';
require_once 'scripts/doc.php';
require_once 'scripts/sheet.php';

// Start the session
session_start();

// Respect mobile flag in GET parameters
if (isset($_GET['m'])) {
  if ($_GET['m'] == true) {
    $_SESSION['mobile'] = true;
  }
  else {
    $_SESSION['mobile'] = false;
  }
}

// ****************************************************************************
?>
<html>
<head>
  <title>Gas Log</title>
  
  <link rel="stylesheet" href="style.css" type="text/css" />
  <?php
    // Add mobile stylesheet. It will override a lot of what's in style.css.
    if (isset($_SESSION['mobile']) && $_SESSION['mobile'] == true) {
    ?>
      <link rel="stylesheet" href="m/style.css" type="text/css" />
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php 
    }
  ?>
  
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
  <script type="text/javascript">
    function make2Digit(val) {
      if (val < 10)
        return '0' + val;
      return val;
    }
    
    function getCurrentTimeString() {
      var now = new Date();
      return now.getFullYear() + '-' + 
        make2Digit(now.getMonth() + 1) + '-' + 
        make2Digit(now.getDate()) + ' ' +
        make2Digit(now.getHours()) + ':' + 
        make2Digit(now.getMinutes()) + ':' + 
        make2Digit(now.getSeconds())
      ;
    }
    
    // Set focus to the mileage box
    $(document).bind('autofocus_ready', function() {
      if (! ('autofocus' in document.createElement('input'))) {
        $('#mileage').focus();
      }
    });
    
    $(document).ready(function() {
      //$('#datetime').val(getCurrentTimeString());

      $('#btnNow').click(function() {
        $('#datetime').val(getCurrentTimeString());
      });
      
      $('#btnClear').click(function() {
        // Clear all input
        $('input, textarea').not('[type="submit"]')
          .val('');
        
        // Set default date/time
        $('#datetime').val(getCurrentTimeString());
      });
      
      //TODO: clear button which works on all forms (get parent form ID, etc)
    });
  </script>
</head>
<body>
<?php
// ****************************************************************************

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
function getFriendlyDatetime($datetime) {
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
    $str .= "on " . $stats['last']['datetime'] . " ";
  
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
 * Prints out the HTML for the input form. Displays errors, if applicable
 */
function printForm($doc, $errors) {
?>
<header id="top">
  <hgroup>
    <h1><a href="<?php echo BASE_URL . '?' . GlApp::GET_ID . '=' . $_GET[GlApp::GET_ID]; ?>"><?php echo $doc->title(); ?></a></h1>
    <h2>New Entry</h2>
  </hgroup>
</header>
<article>
  <fieldset>
    
    <!-- Error notification box -->
    <?php
    $numErrors = count($errors);
    if ($numErrors > 0) {
    ?>
    <div class="errornotice">
      <?php 
        if ($numErrors == 1) {
          ?>
          <p>You have one error in your input. Please correct it and press 'Submit'again.</p>
          <?php
        }
        else {
          ?>
          <p>You have <?php echo $numErrors; ?> errors in your input. Please correct these errors, then press the 'Submit' button.</p>
          <?php
            /*echo '<ul>';
            foreach ($errors as $error) {
              echo '<li><a href="#' . $error . '">Error</a></li>';
            }
            echo '</ul>';
            */
          ?>
          <?php
        }
      ?>
    </div>
    <?php
    }
    
    // Get 'stats' so we can fill in some default values
    $stats = $doc->stats();
    ?>
    
    <form id="frmNew" method="post" action="<?php echo $doc->formUrl(); ?>">    
      <!-- Date and Time -->
      <div class="formrow required<?php if (isset($errors['datetime'])) echo " invalid";?>">
        <div class="label">
          <label for="datetime">Date/Time</label>
        </div>
        <div class="input">
          <span class="inputlabel">
            <input type="datetime-local" name="datetime" id="datetime" form="frmNew" class="datetime" required="required" aria-required="true" value="<?php echo date(GlApp::DATE_FORMAT); ?>" />
           <a id="btnNow" class="link-button">Now</a>
          </span>
          <br />
          <span class="datetimeformat-label">(YYYY-MM-DD HH:MM:SS)</span>
        </div>
        <div class="desc"><p>The date and time of the fillup. A value should be automatically filled in for you. However, if you need to change it, do so in the field above.</p>
        </div>
      </div>
      
      <!-- Mileage -->
      <div class="formrow required<?php if (isset($errors['mileage'])) echo " invalid";?>">
        <div class="label">
          <label for="mileage">Mileage</label>
        </div>
        <div class="input">
          <span class="distanceinput inputlabel">
            <input type="number" name="mileage" id="mileage" form="frmNew"  class="mileage" maxlength="6" required="required" min="0" max="1000000" step="1" placeholder="Current mileage" autofocus <?php 
              if (isset($_POST['mileage']))
                echo 'value="' . $_POST['mileage'] . '"';
              else if (isset($stats['last']['mileage']) && isset($stats['all']['tripdistance']))
                echo 'value="' . getMiles($stats['last']['mileage'] + $stats['all']['tripdistance']) . '"';
            ?> />
            <script>$(document).trigger('autofocus_ready');</script>
          </span>
        </div>
        <div class="desc"><p>The mileage at the time of the fillup. Round to the nearest mile. Do not use puncuation.</p>
        </div>
      </div>
      
      <!-- Location -->
      <div class="formrow<?php if (isset($errors['location'])) echo " invalid";?>">
        <div class="label">
          <label for="location">Location</label>
        </div>
        <div class="input">
          <span class="inputlabel">
            <input type="text" name="location" id="location" form="frmNew" class="location" placeholder="Current location" <?php 
            if (isset($_POST['location']))
              echo 'value="' . $_POST['location'] . '"';
            else if (isset($stats['all']['location']))
              echo 'value="' . $stats['all']['location'] . '"';
            ?> />
          </span>
        </div>
        <div class="desc"><p>The location of the fillup. This will probably be the name of the gas station, but it doesn't really matter.</p>
        </div>
      </div>
      
      <!-- Price per Gallon -->
      <div class="formrow required<?php if (isset($errors['pricepergallon'])) echo " invalid";?>">
        <div class="label">
          <label for="pricepergallon">Price per Gallon</label>
        </div>
        <div class="input">
          <span class="currencyinput inputlabel">
            <input type="number" name="pricepergallon" id="pricepergallon" form="frmNew"  maxlength="5" class="price pricepergallon" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" <?php
              if (isset($_POST['pricepergallon'])) 
                echo 'value="' . $_POST['pricepergallon'] . '"';
              else if (isset($stats['last']['pricepergallon']))
                echo 'value="' . getGasMoney($stats['last']['pricepergallon']) . '"';
              ?> />
          </span>
        </div>
        <div class="desc"><p>The price of fuel per gallon. Don't forget the extra <math><mfrac><mn>9</mn><mn>10</mn></mfrac></math>!</p>
        </div>
      </div>
      
      <!-- Gallons -->
      <div class="formrow required<?php if (isset($errors['gallons'])) echo " invalid";?>">
        <div class="label">
          <label for="gallons">Gallons</label>
        </div>
        <div class="input">
          <span class="liquidinput inputlabel">
            <input type="number" name="gallons" id="gallons" form="frmNew"  maxlength="6" class="gallons" required="required" min="0" max="99.999" step="0.001" placeholder="# of gallons" <?php if (isset($_POST['gallons'])) echo 'value="' . $_POST['gallons'] . '"'; ?> />
          </span>
          <select name="grade" id="grade" form="frmNew" class="grade">
            <option name="reg" id="reg" value="0">Regular Unleaded</option>
            <option name="plus" id="plus" value="1">Plus</option>
            <option name="sup" id="sup" value="2">Supreme</option>
          </select>
        </div>
        <div class="desc"><p>The number of gallons added during the fillup. For the most meaningful statistical results, always fill the tank completely.</p>
        </div>
      </div>
      
      <!-- Pump Price -->
      <div class="formrow<?php if (isset($errors['pumpprice'])) echo " invalid";?>">
        <div class="label">
          <label for="pumpprice">Pump Price</label>
        </div>
        <div class="input">
            <span class="currencyinput inputlabel">
              <input type="number" name="pumpprice" id="pumpprice" form="frmNew" maxlength="6" class="price pumpprice" min="0" max="999.99" step="0.01" placeholder="Price paid" <?php if (isset($_POST['pumpprice'])) echo 'value="' . $_POST['pumpprice'] . '"'; ?> />
            </span>
        </div>
        <div class="desc">The total price paid at the pump. Recording this number is not necessary since it ought to be extremely close to the calculated <math><mi>gallons</mi><mo>*</mo><mi>price_per_gallon</mi></math>. For those tin-foil hat days, it may be an interesting fact to track.
        </div>
      </div>
      
      <!-- Notes -->
      <div class="formrow<?php if (isset($errors['notes'])) echo " invalid";?>">
        <div class="label">
          <label for="notes">Notes</label>
        </div>
        <div class="input">
          <span class="inputlabel">
            <textarea name="notes" id="notes" form="frmNew" class="notes" placeholder="Any additional notes"><?php if (isset($_POST['notes'])) echo $_POST['notes']; ?></textarea>
          </span>
        </div>
        <div class="desc"><p>Any additional notes you wish to put here. This could be justification for terrible gas mileage, the primary mode of driving during the past tank of gas, etc. Anything you desire!</p>
        </div>
      </div>
      
      <!-- Form Buttons -->
      <div class="formrow">
        <div class="submit">
          <span id="btnClear" class="link-button">Clear</span>
          &nbsp;
          <input type="submit" name="submit" value="Submit" />
        </div>
      </div>
    </form>
  </fieldset>
</article>
<?php
}

/**
 * Prints the message shown after a successful entry
 */
function printStats($doc, $message = null) {
?>
<header id="top">
  <hgroup>
    <h1><a href="<?php echo BASE_URL . '?' . GlApp::GET_ID . '=' . $_GET[GlApp::GET_ID]; ?>"><?php echo $doc->title(); ?></a></h1>
    <h2>Stats</h2>
  </hgroup>
</header>
<article>
  <?php
  if ($message == null) {
    $message = "At the gas station now? Add a <a href=" . $doc->newUrl() . ">new entry</a> to the log.";
  }
  
  // Get the 'stats' table from the current document
  $stats = $doc->stats();
  ?>
  
  <!-- Strange rendering issue where there will be a large gap between article
       and header backgrounds...This empty paragraph seems to fix it.
  -->
  <p></p>
  
  <fieldset class="stats">
    <div class="message">
      <p><?php echo $message; ?></p>
    </div>
    <?php
      $lastDatetime = strtotime($stats['last']['datetime']);
      $dateStr = date(GlApp::DATE_FORMAT_FULL, $lastDatetime);
    ?>
    <legend>Most Recent Fill-up, <span class="datetime" title="<?php echo date(GlApp::DATE_FORMAT, $lastDatetime); ?>"><?php echo $dateStr; ?></span></legend>
    
    <div class="statdesc">
      <p>The following stats are based on your most recent fill-up at 
      <?php
        if ($stats['last']['location'] == $stats['all']['location'])
          echo 'your <span class="location" title="' . $stats['all']['location'] . '">favorite station</span> ';
        else
          echo $stats['last']['location'] . ' ';
        
        $dateStr = getFriendlyDatetime($lastDatetime);
        echo '<span class="datetime" title="' . date(GlApp::DATE_FORMAT, $lastDatetime) . '">' . $dateStr . '</span>';
      ?>.
      </p>
    </div>
    
    <!-- Gas mileage -->
    <div class="statrow mpg">
      <?php
      $change = $stats['last']['mpg'] - $stats['all']['mpg'];
      
      // These are only used to change the text description. 0.25mpg and below
      // is a "slight" change, more than 2 is a "significant" change.
      $thresholdText = getThresholdText($change, 0.25, 2, 'increased', 'dropped');
      
      // Determine the brightness of the color. A change of > 0.75mpg will be
      // visually apparent.
      $color = getTrendColor($change, 0.75);
      
      ?>
      <div class="content">
        <div class="value mpg" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['mpg']; ?>">
          <?php echo getMpg($stats['last']['mpg']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, your mileage <span class="<?php echo ($change >= 0) ? 'better' : 'worse'; ?>"><?php echo $thresholdText; ?></span>, by <span class="mpg" title="<?php echo abs($change); ?>"><?php echo getMpg(abs($change)); ?></span>. This is
          <?php
            $change = (($stats['last']['mpg'] * 100) / $stats['all']['mpg']) - 100;
            
            if ($change >= 0)
              echo '<span class="better">';
            else
              echo '<span class="worse">';
            
            echo '<span class="percent" title="' . abs($change) . '">' . abs(getPercent($change)) . '</span>';
            
            if ($change >= 0)
              echo ' better';
            else
              echo ' worse';
            echo '</span>';
            ?>
            than your all-time average <span class="mpg" title="<?php echo $stats['all']['mpg']; ?>"><?php echo getMpg($stats['all']['mpg']); ?></span>. 
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div>
    
    <!-- Trip distance -->
    <div class="statrow tripdistance">
      <?php
      $change = $stats['last']['tripdistance'] - $stats['all']['tripdistance'];
      
      // These are only used to change the text description. 10 miles and below
      // is a "slight" change, more than 50 miles is a "significant" change.
      $thresholdText = getThresholdText($change, 10, 50, 'farther', 'less distance');
      
      // Determine the brightness of the color. A change of > 50mi will be
      // visually apparent.
      $color = getTrendColor($change, 50);
      
      ?>
      <div class="content">
        <div class="value distance" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['tripdistance']; ?>">
          <?php echo getMiles($stats['last']['tripdistance']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you went <span class="<?php echo ($change >= 0) ? 'better' : 'worse'; ?>"><?php echo $thresholdText; ?></span> between fill-ups. Your trip distance was
          <?php
            if ($change >= 0)
              echo '<span class="better">';
            else
              echo '<span class="worse">';
          ?>
          <span class="distance" title="<?php echo abs($change); ?>"><?php echo getMiles(abs($change)); ?></span>
          <?php
            if ($change >= 0)
              echo ' farther</span>';
            else
              echo ' shorter</span>';
          ?> than your average trip distance of <span class="distance" title="<?php echo $stats['all']['tripdistance']; ?>"><?php echo getMiles($stats['all']['tripdistance']); ?></span>.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div>
    
    <!-- Pump price -->
    <div class="statrow cost">
      <?php
      $change = $stats['last']['cost'] - $stats['month']['cost'];
      
      // These are only used to change the text description. $3 and below is a 
      // "slight" change, more than $10 is a "significant" change.
      $thresholdText = getThresholdText($change, 3, 10, 'more', 'less', '', '');
      
      // Determine the brightness of the color. A change of > $7.50 will be
      // visually apparent.
      $color = getTrendColor(-$change, 7.5);
      
      ?>
      <div class="content">
        <div class=" value cost" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['cost']; ?>">
          <?php echo getMoney($stats['last']['cost']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you spent <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> than your one-month average of <span class="cost" title="<?php echo $stats['month']['cost']; ?>"><?php echo getMoney($stats['month']['cost']); ?></span>.
          This is
          <?php 
            $change = $stats['last']['cost'] - $stats['previous']['cost'];
          ?>
          <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo $change; ?>"><?php
            if ($change >= 0)
              echo getMoney(abs($change)) . '</span> more</span>';
            else
              echo getMoney(abs($change)) . '</span> less</span>';
          ?></span> than your previous fill-up.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php echo getArrowHtml($color, $change); ?>
      </div>
    </div> <!-- end statrow -->
  
    <!-- Cost per day -->
    <div class="statrow costperday">
      <?php
      $change = $stats['last']['costperday'] - $stats['month']['costperday'];
      
      // These are only used to change the text description. $1 and below is a
      // "slight" change, more than $3 is a "significant" change.
      $thresholdText = getThresholdText($change, 1, 3, 'more', 'less', '', '');
      
      // Determine the brightness of the color. A change of > $3 will be
      // visually apparent.
      $color = getTrendColor(-$change, 3);
      
      ?>
      <div class="content">
        <div class="value cost" style="color: <?php echo $color; ?>" title="<?php echo $stats['last']['costperday']; ?>">
          <?php echo getMoney($stats['last']['costperday']); ?>
        </div>
        <div class="desc">
          <p>
          During your last tank of gas, you spent <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> per day than your one-month average of <span class="cost" title="<?php echo $stats['month']['costperday']; ?>"><?php echo getMoney($stats['month']['costperday']); ?></span>.
          You spent 
          <?php 
            $change = $stats['last']['costperday'] - $stats['previous']['costperday'];
            $threshold = getThresholdText($change, 1, 3, 'more', 'less', '', '');
          ?>
          <span class="<?php echo ($change >= 0) ? 'worse' : 'better'; ?>"><span class="cost" title="<?php echo abs($change); ?>"><?php echo getMoney(abs($change)); ?></span> <?php echo $thresholdText; ?></span> per day than during your previous trip.
          </p>
          <p>For this tank, you spent <span class="cost"><?php echo getMoney($stats['last']['costpermile']); ?></span> per mile, which is
          <?php
            // Get change as a percentage
            $change = (($stats['last']['costpermile'] * 100) / $stats['month']['costpermile']) - 100;
            
            if ($change >= 0)
              echo '<span class="worse">up ';
            else
              echo '<span class="better">down ';
            
            echo '<span class="percent">' . abs(getPercent($change)) . '</span></span>';
            ?>
            from last month.
          </p>
        </div>
      </div>
      <div class="arrow">
        <?php
        // Have to set this again...
        $change = $stats['last']['costperday'] - $stats['month']['costperday'];
        echo getArrowHtml($color, $change);
        ?>
      </div>
    </div>
  </fieldset>
</arcticle>
<?php
}

/**
 * Prints out the options for the requested document
 */
/*function printDocOptions($doc) {
?>
<header id="top">
  <hgroup>
    <h1><?php echo $doc->title(); ?></h1>
    <h2>Options</h2>
  </hgroup>
</header>
<article>
  <ul>
    <li><a href="<?php echo $doc->newUrl(); ?>">Add Entry</a></li>
  </ul>
</article>
<?php
}*/

/**
 * Prints out the appropriate error HTML when the document requested via GET
 * params fails to load.
 */
function printDocLoadFailed() {
?>
<p>
  <strong>Fatal error.</strong> Cannot open given document <em><?php echo $_GET[GlApp::GET_ID]; ?></em>
<p>
<p>PHP Error given:</p>
<pre>
  <?php 
  echo GlApp::getLastError();
  GlApp::clearLastError();
  ?>
</pre>
<?php
}

/**
 * Prints the list of available documents
 */
function printDocList($app, $docs) {
?>
<header id="top">
  <hgroup>
    <h1>Gas Logs</h1>
    <?php /*<h2>Create New or Select Existing</h2>*/ ?>
    <h2>Select an existing document</h2>
  </hgroup>
</header>
<article>
  <?php
  /*<p>You can create a <a href="<?php echo $app->newDocUrl(); ?>">new document</a>, or select from one of the existing documents listed below.
  </p>
  */
  ?>
  <p>Select from the list of documents below. If you wish to add a new gas log, make a copy of the <a href="https://spreadsheets.google.com/ccc?key=0AnRif0EzefXxdEViXzFGdjlJLXNXYlBhdXFmUERqTnc&hl=en" title="Master document" target="_blank">master document</a> via <code>File -> Make a copy..</code>. Save it with the extension <code><?php echo FILTER_TEXT; ?></code> so it shows up in the list below.
  </p>
  <ul>
<?php
  foreach ($docs as $doc) {
    echo '<li><a href="' . $doc->url() . '">' . $doc->title() . '</a></li>';
  }
?>
  </ul>
</article>
<?php
}

/**
 * Entry point (sorta)
 */
$auth = new GlAuth();

if (! $auth->isLoggedIn() && ! $auth->hasGetToken()) {
  // Display link to login
  ?>
  <header id="top">
    <hgroup>
      <h1>Login</h1>
      <h2>Please log in using your Google account</h2>
    </hgroup>
  </header>
  <p>Before we can continue, you must <a href="<?php echo $auth->getUrl(); ?>" title="Authorize your Google account">authorize</a> access to your Google account.
  </p>
  <?php
  
  // Save get parameters. We will reuse these upon redirect...
  $app = new GlApp($auth);
  $app->saveGetVars();
}
else if (! $auth->login()) {
  // I don't know how you got here.
  ?>
  <strong>Fatal error.</strong> Login not authenticated.
  <?php
}
else {
  // Create instance of the app
  $app = new GlApp($auth);
  
  // Merge in pre-login vars (if any)
  $app->mergeSavedGetVars();
  
  // Get the application mode (show, new, etc)
  $mode = $app->getMode();
  
  if ($mode == GlApp::MODE_DOCONLY ||
           $mode == GlApp::MODE_NEW || 
           $mode == GlApp::MODE_SUBMITNEW
          ) {
    // Try to open and display the document specified in the GET param
    if ($app->open()) {
      $doc = $app->getDoc();
      
      if ($mode == GlApp::MODE_DOCONLY) {        
        // Shows the options available for the requested document
        printStats($doc);
      }
      else {
        $errors = array();
        
        if ($mode == GlApp::MODE_SUBMITNEW) {
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
        }
        
        if ($mode == GlApp::MODE_NEW || count($errors) > 0) {
          // Display the form, with errors highlighted if necessary
          printForm($doc, $errors );
        }
        else {
          // Submit the form input
          
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
          
          $doc->insert($cleanVals);
          
          // Print confirmation page
          $message = '<p>Your fill-up information has been successfully recorded. If you\'re interested, take a peek at your stats below. If not, just close this window and drive safely!</p>';
          printStats($doc, $message);
        }
      }
    }
    else {
      // Failed to display document specified in the GET param
      printDocLoadFailed();
    }
  }
  else {
    // No document specified in GET param. Show list of available docs
    $docs = $app->getAvailable();
    printDocList($app, $docs);
  }
}

// ****************************************************************************
?>
</body>
</html>