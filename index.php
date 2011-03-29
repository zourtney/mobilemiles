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

// Constants
define('BASE_URL', 'http://localhost/gaslog/');
define('DATA_SHEET', 'Form Data');
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
    
    $(document).ready(function() {
      $('#datetime').val(getCurrentTimeString());
      
      $('input.price')
        .val('0.00')
        .click(function() {
          if ($(this).val() == '0.00') {
            $(this).val('');
          }
        })
      ;
      
      $('#btnNow').click(function() {
        $('#datetime').val(getCurrentTimeString());
      });
      
      $('#btnClear').click(function() {
        $('input, textarea').not('[type="submit"]').val('');
        $('#datetime').val(getCurrentTimeString());
        $('input.price').val('0.00');
      });
    });
  </script>
</head>
<body>
<?php
// ****************************************************************************

function getAuthSubUrl()
{
  $next = getCurrentUrl();
  $scope = 'http://spreadsheets.google.com/feeds/';
  $session = true;
  $secure = false;
  
  return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
      $session);
}

function requestUserLogin($linkText)
{
  $authSubUrl = getAuthSubUrl();
  echo "<a href=\"{$authSubUrl}\">{$linkText}</a>";
}

function getAuthSubHttpClient()
{
  global $_SESSION, $_GET;
  $client = new Zend_Gdata_HttpClient();
  
  if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
    $_SESSION['sessionToken'] =
        Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token'], $client);
  }
  
  $client->setAuthSubToken($_SESSION['sessionToken']);
  return $client;
}

function getClientLoginHttpClient($user, $pass)
{
  $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;

  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

/**
 * Entry point
 */
session_start();

if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
  requestUserLogin('Please login to your Google Account.');
}
else {
  $client = getAuthSubHttpClient();
  
  $service = new Zend_Gdata_Spreadsheets($client);
  
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
     
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
      ?>
      <header id="top">
        <hgroup>
          <!--<h1><?php echo $doc->title->text; ?></h1>
          <h2>New Entry</h2>-->
        </hgroup>
      </header>
      <article>
        <fieldset>
          <!-- Date and Time -->
          <div class="formrow required">
            <div class="label">
              <label for="datetime">Date/Time</label>
              <span class="datetimeformat-label">(YYYY-MM-DD)</span>
            </div>
            <div class="input">
              <input type="datetime-local" name="datetime" id="datetime" class="datetime" required="required" aria-required="true" />
              <button id="btnNow">Now</button>
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
                <input type="number" name="mi" id="mi" class="mileage" maxlength="6" required="required" />
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
              <input type="text" name="loc" id="loc" class="location" />
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
                <input type="number" name="ppg" id="ppg" maxlength="5" class="price ppg" required="required" />
              </span>
            </div>
            <div class="desc"><p>The price of fuel per gallon. Don't forget the extra <math><mfrac><mn>9</mn><mn>10</mn></mfrac></math>!</p>
            </div>
          </div>
          
          <!-- Gallons -->
          <div class="formrow required">
            <div class="label">
              <label for="gal">Gallons</label>
            </div>
            <div class="input">
              <span class="liquidinput inputlabel">
                <input type="number" name="gals" id="gals" maxlength="6" class="gals" required="required" />
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
                  <input type="number" name="pumpprice" id="pumpprice" maxlength="6" class="price pumpprice" />
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
              <textarea name="notes" id="notes" class="notes"></textarea>
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
      </article>
      <?php
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
}
// ****************************************************************************
?>
</body>
</html>