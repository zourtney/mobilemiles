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
        $('#mi').focus();
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
          <p>You have <?php echo $numErrors; ?> in your input. Please correct these errors, then press the 'Submit' button.</p>
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
          <input type="datetime-local" name="datetime" id="datetime" form="frmNew" class="datetime" required="required" aria-required="true" value="<?php echo date('Y-m-d H:i:s'); ?>" />
         <button id="btnNow">Now</button>
        </span>
        <br />
        <span class="datetimeformat-label">(YYYY-MM-DD HH:MM:SS)</span>
      </div>
      <div class="desc"><p>The date and time of the fillup. A value should be automatically filled in for you. However, if you need to change it, do so in the field above.</p>
      </div>
    </div>
    
    <!-- Mileage -->
    <div class="formrow required<?php if (isset($errors['mi'])) echo " invalid";?>">
      <div class="label">
        <label for="mi">Mileage</label>
      </div>
      <div class="input">
        <span class="distanceinput inputlabel">
          <input type="number" name="mi" id="mi" form="frmNew"  class="mileage" maxlength="6" required="required" min="0" max="1000000" step="1" placeholder="Current mileage" autofocus <?php if (isset($_POST['mi'])) echo 'value="' . $_POST['mi'] . '"'; ?> />
          <script>$(document).trigger('autofocus_ready');</script>
        </span>
      </div>
      <div class="desc"><p>The mileage at the time of the fillup. Round to the nearest mile. Do not use puncuation.</p>
      </div>
    </div>
    
    <!-- Location -->
    <div class="formrow<?php if (isset($errors['loc'])) echo " invalid";?>">
      <div class="label">
        <label for="loc">Location</label>
      </div>
      <div class="input">
        <span class="inputlabel">
          <input type="text" name="loc" id="loc" form="frmNew" class="location" placeholder="Current location" <?php if (isset($_POST['loc'])) echo 'value="' . $_POST['loc'] . '"'; ?> />
        </span>
      </div>
      <div class="desc"><p>The location of the fillup. This will probably be the name of the gas station, but it doesn't really matter.</p>
      </div>
    </div>
    
    <!-- Price per Gallon -->
    <div class="formrow required<?php if (isset($errors['ppg'])) echo " invalid";?>">
      <div class="label">
        <label for="ppg">Price per Gallon</label>
      </div>
      <div class="input">
        <span class="currencyinput inputlabel">
          <input type="number" name="ppg" id="ppg" form="frmNew"  maxlength="5" class="price ppg" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" <?php if (isset($_POST['ppg'])) echo 'value="' . $_POST['ppg'] . '"'; ?> />
        </span>
      </div>
      <div class="desc"><p>The price of fuel per gallon. Don't forget the extra <math><mfrac><mn>9</mn><mn>10</mn></mfrac></math>!</p>
      </div>
    </div>
    
    <!-- Gallons -->
    <div class="formrow required<?php if (isset($errors['gals'])) echo " invalid";?>">
      <div class="label">
        <label for="gals">Gallons</label>
      </div>
      <div class="input">
        <span class="liquidinput inputlabel">
          <input type="number" name="gals" id="gals" form="frmNew"  maxlength="6" class="gals" required="required" min="0" max="99.999" step="0.001" placeholder="# of gallons" <?php if (isset($_POST['gals'])) echo 'value="' . $_POST['gals'] . '"'; ?> />
        </span>
        <select name="grade" id="grade" class="grade">
          <option name="reg" id="reg">Regular Unleaded</option>
          <option name="plus" id="plus">Plus</option>
          <option name="sup" id="sup">Supreme</option>
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
 * Prints out the options for the requested document
 */
function printDocOptions($doc) {
?>
<header id="top">
  <hgroup>
    <h1><?php echo $doc->title(); ?></h1>
    <h2>Options</h2>
  </hgroup>
  <article>
    <ul>
      <li><a href="<?php echo $doc->newUrl(); ?>">Add Entry</a></li>
    </ul>
  </article>
</header>
<?php
}

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
function printDocList($docs) {
?>
<ul>
<?php
  foreach ($docs as $doc) {
    echo '<li><a href="' . $doc->url() . '">' . $doc->title() . '</a></li>';
  }
?>
</ul>
<?php
}

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
  
  if ($mode == GlApp::MODE_DOCONLY ||
      $mode == GlApp::MODE_NEW || 
      $mode == GlApp::MODE_SUBMITNEW
     ) {
    // Try to open and display the document specified in the GET param
    if ($app->open()) {
      $doc = $app->getDoc();
      
      if ($mode == GlApp::MODE_DOCONLY) {        
        // Shows the options available for the requested document
        printDocOptions($doc);
      }
      else {
        $errors = array();
        
        if ($mode == GlApp::MODE_SUBMITNEW) {
          // Check input validity
          if (! isset($_POST['datetime']) || strtotime($_POST['datetime']) === FALSE) {
            $errors['datetime'] = true;
          }
          
          if (! isset($_POST['mi']) || ! is_numeric($_POST['mi'])) {
            $errors['mi'] = true;
          }
          
          if (! isset($_POST['ppg']) || ! is_numeric($_POST['ppg'])) {
            $errors['ppg'] = true;
          }
          
          if (! isset($_POST['gals']) || ! is_numeric($_POST['gals'])) {
            $errors['gals'] = true;
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
          echo "I'll submitya";
          print_r($_POST);
          
          //TODO: actually clean the values. Could probably put it in the 
          // case above...
          $cleanVals = array();
          $cleanVals['datetime'] = $_POST['datetime'];
          $cleanVals['mi'] = $_POST['mi'];
          $cleanVals['loc'] = $_POST['loc'];
          $cleanVals['ppg'] = $_POST['ppg'];
          $cleanVals['gals'] = $_POST['gals'];
          $cleanVals['pumpprice'] = $_POST['pumpprice'];
          $cleanVals['notes'] = htmlspecialchars($_POST['notes']);
          
          $doc->insert($cleanVals);
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
    printDocList($docs);
  }
}


//TODO: login using userid? How does the current thing do it?
/*if (! isset($_SESSION['sessionToken']) && ! isset($_GET['token'])) {
  requestUserLogin('Please login to your Google Account.');
}
else {
  $client = getAuthSubHttpClient();
  //TOOD: um, where did my check go?
  
  $service = new Zend_Gdata_Spreadsheets($client);
  
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    //TODO: make session variable
    // --> if different from session variable, need to start over
    //     regardless of what's in GET or POST
    
    // Find the document being requsted
    try {
      $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
      $query->setSpreadsheetKey($id);
      $doc = $service->getSpreadsheetEntry($query);
    }
    catch (Zend_Gdata_App_Exception $e) {
      ?>
      <p>Error: Could not find document <strong><?php echo $id; ?></strong></p> 
      <?php
      echo $e;
      exit;
    }
    
    // Find the raw data input sheet
    try {
      $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
      $query->setSpreadsheetKey($id);
      $query->setTitle(DATA_SHEET);
      $sheet = $service->getWorksheetEntry($query);
    }
    catch (Zend_Gdata_App_Exception $e) {
      ?>
      <p>Error: Could not find worksheet <strong><?php echo DATA_SHEET; ?></strong></p> 
      <?php
      echo $e;
      exit;
    }
    
    // Define behavior based on "action" parameter
    if (isset($_GET['action']) && $_GET['action'] == 'new') {
      
      if (count($_POST) > 0) {
        $errors = array();
        if (! isset($_POST['datetime']) || strtotime($_POST['datetime']) === FALSE) {
          $errors['datetime'] = true;
        }
        
        if (! isset($_POST['mi']) || ! is_numeric($_POST['mi'])) {
          $errors['mi'] = true;
        }
        
        if (! isset($_POST['ppg']) || ! is_numeric($_POST['ppg'])) {
          $errors['ppg'] = true;
        }
        
        if (! isset($_POST['gals']) || ! is_numeric($_POST['gals'])) {
          $errors['gals'] = true;
        }
        
        print_r($_POST);
        echo "You have " . count($errors) . " errors";
        print_r($errors);
      }
      else {
      ?>
      <header id="top">
        <hgroup>
          <h1><?php echo $doc->title->text; ?></h1>
          <h2>New Entry</h2>
        </hgroup>
      </header>
      <article>
        <form method="post" action="<?php echo BASE_URL; ?>?id=<?php echo $id; ?>&action=new">
          <fieldset>
            <!-- Date and Time -->
            <div class="formrow required">
              <div class="label">
                <label for="datetime">Date/Time</label>
                <span class="datetimeformat-label">(YYYY-MM-DD)</span>
              </div>
              <div class="input">
                <span class="inputlabel">
                  <input type="datetime-local" name="datetime" id="datetime" class="datetime" required="required" aria-required="true" />
                 <button id="btnNow">Now</button>
                </span>
              </div>
              <div class="desc"><p>The date and time of the fillup. A value should be automatically filled in for you. However, if you need to change it, do so in the field above.</p>
              </div>
            </div>
            
            <!-- Mileage -->
            <div class="formrow required">
              <div class="label">
                <label for="mi">Mileage</label>
              </div>
              <div class="input">
                <span class="distanceinput inputlabel">
                  <input type="number" name="mi" id="mi" class="mileage" maxlength="6" required="required" min="0" max="1000000" step="1" placeholder="Current mileage" autofocus />
                  <script>$(document).trigger('autofocus_ready');</script>
                </span>
              </div>
              <div class="desc"><p>The mileage at the time of the fillup. Round to the nearest mile. Do not use puncuation.</p>
              </div>
            </div>
            
            <!-- Location -->
            <div class="formrow">
              <div class="label">
                <label for="loc">Location</label>
              </div>
              <div class="input">
                <span class="inputlabel">
                  <input type="text" name="loc" id="loc" class="location" placeholder="Current location" />
                </span>
              </div>
              <div class="desc"><p>The location of the fillup. This will probably be the name of the gas station, but it doesn't really matter.</p>
              </div>
            </div>
            
            <!-- Price per Gallon -->
            <div class="formrow required">
              <div class="label">
                <label for="ppg">Price per Gallon</label>
              </div>
              <div class="input">
                <span class="currencyinput inputlabel">
                  <input type="number" name="ppg" id="ppg" maxlength="5" class="price ppg" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" />
                </span>
              </div>
              <div class="desc"><p>The price of fuel per gallon. Don't forget the extra <math><mfrac><mn>9</mn><mn>10</mn></mfrac></math>!</p>
              </div>
            </div>
            
            <!-- Gallons -->
            <div class="formrow required">
              <div class="label">
                <label for="gals">Gallons</label>
              </div>
              <div class="input">
                <span class="liquidinput inputlabel">
                  <input type="number" name="gals" id="gals" maxlength="6" class="gals" required="required" min="0" max="99.999" step="0.001" placeholder="# of gallons"/>
                </span>
                <select name="grade" id="grade" class="grade">
                  <option name="reg" id="reg">Regular Unleaded</option>
                  <option name="plus" id="plus">Plus</option>
                  <option name="sup" id="sup">Supreme</option>
                </select>
              </div>
              <div class="desc"><p>The number of gallons added during the fillup. For the most meaningful statistical results, always fill the tank completely.</p>
              </div>
            </div>
            
            <!-- Pump Price -->
            <div class="formrow">
              <div class="label">
                <label for="pumpprice">Pump Price</label>
              </div>
              <div class="input">
                  <span class="currencyinput inputlabel">
                    <input type="number" name="pumpprice" id="pumpprice" maxlength="6" class="price pumpprice" min="0" max="999.99" step="0.01" placeholder="Price paid" />
                  </span>
              </div>
              <div class="desc">The total price paid at the pump. Recording this number is not necessary since it ought to be extremely close to the calculated <math><mi>gallons</mi><mo>*</mo><mi>price_per_gallon</mi></math>. For those tin-foil hat days, it may be an interesting fact to track.
              </div>
            </div>
            
            <!-- Notes -->
            <div class="formrow">
              <div class="label">
                <label for="notes">Notes</label>
              </div>
              <div class="input">
                <span class="inputlabel">
                  <textarea name="notes" id="notes" class="notes" placeholder="Any additional notes"></textarea>
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
          </fieldset>
        </form>
      </article>
      <?php
      }
    }
    else {
      ?>
      <header id="top">
        <hgroup>
          <h1><?php echo $doc->title->text; ?></h1>
          <h2><?php echo $sheet->title->text; ?></h2>
        </hgroup>
      </header>
      
      <p>This will be improved in the future to show historical data.</p>
      <p>For now, just create a <a href="<?php echo BASE_URL . '?action=new&id=' . $id; ?>">new entry</a>.</p>
      <?php
    }
  }
  else {
    ?>
    <h1>Gas Logs</h1>
    <ul>
  <?php
    $docs = $service->getSpreadsheetFeed();
    
    foreach ($docs as $doc) {
      if (stripos($doc->title->text, 'gas log') !== FALSE) {
        $id = explode('/', $doc->id->text);
        $url = BASE_URL . '?id=' . $id[5];
        $title = $doc->title->text;
        echo "<li><a href=\"$url\">$title</a></li>\n";
        //echo '<pre>' . print_r($doc, TRUE) . '</pre>';
      }
    }
    echo "</ul>";
  }
}*/
// ****************************************************************************
?>
</body>
</html>