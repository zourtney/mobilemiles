<!DOCTYPE html>
<?php
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

// Constants
define('BASE_URL', 'http://localhost/gaslog/trunk/');
define('DATA_SHEET', 'Form Data');

// Authentication functions
require_once 'scripts/auth.php';

// Application logic
require_once 'scripts/glapp.php';
require_once 'scripts/doc.php';
require_once 'scripts/sheet.php';

// ****************************************************************************
?>
<html>
<head>
  <title>RandomDoc Test</title>
  <link rel="stylesheet" href="style.css" type="text/css" />
  
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
 * Prints out the HTML for the input form. Displays errors, if applicable
 */
function printForm($doc, $errors) {
?>
<header id="top">
  <hgroup>
    <h1><?php echo $doc->title(); ?></h1>
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
    ?>
    
    <!-- Date and Time -->
    <div class="formrow required<?php if (isset($errors['datetime'])) echo " invalid";?>">
      <div class="label">
        <label for="datetime">Date/Time</label>
      </div>
      <div class="input">
        <span class="inputlabel">
          <input type="datetime-local" name="datetime" id="datetime" form="frmNew" class="datetime" required="required" aria-required="true" value="<?php echo date(GlApp::DATE_FORMAT); ?>" />
         <button id="btnNow">Now</button>
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
          <input type="number" name="mileage" id="mileage" form="frmNew"  class="mileage" maxlength="6" required="required" min="0" max="1000000" step="1" placeholder="Current mileage" autofocus <?php if (isset($_POST['mileage'])) echo 'value="' . $_POST['mileage'] . '"'; ?> />
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
          <input type="text" name="location" id="location" form="frmNew" class="location" placeholder="Current location" <?php if (isset($_POST['location'])) echo 'value="' . $_POST['location'] . '"'; ?> />
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
          <input type="number" name="pricepergallon" id="pricepergallon" form="frmNew"  maxlength="5" class="price pricepergallon" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" <?php if (isset($_POST['pricepergallon'])) echo 'value="' . $_POST['pricepergallon'] . '"'; ?> />
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
  <form id="frmNew" method="post" action="<?php echo $doc->formUrl(); ?>">
    <div class="formrow">
      <div class="submit">
        <span id="btnClear" class="link-button">Clear</span>
        &nbsp;
        <input type="submit" name="submit" value="Submit" />
      </div>
    </div>
  </fieldset>
</form>
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
    <h1><?php //echo $doc->title(); ?></h1>
    <h2>Stats</h2>
  </hgroup>
</header>
<article>
  <?php
  //$message = "testing the message box";
  if ($message != null) {
    ?>
    <p>&nbsp;</p>
    <div class="message">
    <?php
      echo $message;
    ?>
    </div>
    <?php
  }
  ?>
  
  <p>At the gas station now? Add a <a href="<?php echo $doc->newUrl(); ?>">new entry</a> to the log.
  </p>
  
  <?php
    $stats = $doc->stats();
    
    //IDEA: screw the words. Just put the number and a big up or down arrow to
    // indicate trends based on 2 month, 6 month, and all-time values. Make
    // varying shades of green and red.
  ?>
  
  <fieldset class="stats">
    <legend>Stats & Trends</legend>
    
    <div class="statrow">
      <div class="value">
        <p>You average <span class="mpg"><?php echo round($stats['all']['mpg'], 2); ?></span> mpg.</p>
      </div>
      <div class="desc">
      </div>
    </div>
  <?php
    
    //print_r($doc->stats());
  ?>
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
    <h1>Available Documents</h1>
    <?php /*<h2>Create New or Select Existing</h2>*/ ?>
    <h2>Select an existing gas log</h2>
  </hgroup>
</header>
<article>
  <?php
  /*<p>You can create a <a href="<?php echo $app->newDocUrl(); ?>">new document</a>, or select from one of the existing documents listed below.
  </p>
  */
  ?>
  <p>Select from the list of existing gas logs. If you do not have any, create a copy of the master document and store it in your Google Docs.
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
 * Prints the form which allows you to enter the name of a new document.
 */
/*function printNewDocForm($app, $errors) {
?>
<header id="top">
  <hgroup>
    <h1>New Document</h1>
    <h2>Enter the information about your new gas log</h2>
  </hgroup>
</header>
<article>
  <fieldset>
    <!-- Mileage -->
    <div class="formrow required<?php if (isset($errors['docname'])) echo " invalid";?>">
      <div class="label">
        <label for="mi">Vehicle Name</label>
      </div>
      <div class="input">
        <span class="inputlabel">
          <input type="text" name="docname" id="docname" form="frmNewDoc"  class="docname" required="required" placeholder="General Lee" autofocus  <?php if (isset($_POST['docname'])) echo 'value="' . $_POST['docname'] . '"'; ?> />
          <script>$(document).trigger('autofocus_ready');</script>
        </span>
      </div>
      <div class="desc"><p>Enter a uniquely identifying name for the vehicle. This will become part of the spreadsheet filename. For example, entering <code>General Lee</code> will result in the spreadsheet name <code>General Lee Gas Log</code>.</p>
      </div>
    </div>
    <form id="frmNewDoc" method="post" action="<?php echo $app->newDocFormUrl(); ?>">
    <div class="formrow">
      <div class="submit">
        <span id="btnClear" class="link-button">Clear</span>
        &nbsp;
        <input type="submit" name="submit" value="Submit" />
      </div>
    </div>
  </fieldset>
</article>
<?php
}
*/



/**
 * Entry point
 */
session_start();
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
  $mode = $app->getMode();
  echo "mode=$mode";
  
  /*if ($mode == GlApp::MODE_NEWDOC ||
      $mode == GlApp::MODE_SUBMITNEWDOC
     ) {
    // Creating a new document (or doing server-side input verification)
    $errors = array();
    
    if ($mode == GlApp::MODE_SUBMITNEWDOC) {
      // Not really anything to validate (yet...)
      if (! isset($_POST['docname']) || strlen($_POST['docname']) < 1) {
        $errors['docname'] = true;
      }
    }
    
    if ($mode == GlApp::MODE_NEWDOC || count($errors) > 0) {
      // We are creating a new spreadsheet
      printNewDocForm($app, $errors);
    }
    else {
      // Ready to submit...
      $cleanVals = array();
      
      $cleanVals['docname'] = htmlspecialchars($_POST['docname']);
      echo "Creating new document " . $cleanVals['docname'];
    }
  }
  else*/
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
          
          print_r($_POST);
          echo "You have " . count($errors) . " errors";
          print_r($errors);
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