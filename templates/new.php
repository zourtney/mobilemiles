<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the form used when creating a new entry.
 *
 * The following variables are available to you:
 *   - $doc: the PHP class wrapping the spreadsheet reference
 *   - $errors: array of form validation errors
 */

include('header.php');
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
          
          <script type="text/javascript">
            $('#gallons').change(function() {
              var estCost = $('#pricepergallon').val() * $('#gallons').val();
              $('#pumpprice').val(getMoney(estCost));
            });
          </script>
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

<script type="text/javascript">
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

<?php
include('footer.php');
?>