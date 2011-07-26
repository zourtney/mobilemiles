<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This page lets you add a new entry
 */

/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once '../scripts/globals.php';

// Display the splash screen, authorization may take a second or so.
include(TEMPLATE_BASE . '/splash.php');
include(TEMPLATE_BASE . '/pageopen.php');
include(TEMPLATE_BASE . '/ui.php');

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>

<!--<script id="tmpl-new-help-link" type="text/x-jquery-tmpl">
  <a href="${url}" data-rel="dialog" data-transition="pop" data-role="button" data-icon="info" data-iconpos="notext">Help</a>
</script>-->

<script id="tmpl-new-success" type="text/x-jquery-tmpl">
  <p><strong>Submitted successfully.</strong></p>
  
  <p>Review your latest entry or view <a href="<?php echo BASE_URL; ?>view/?id=<?php echo @$_GET['id']; ?>" rel="external">all entries</a>.</p>
  
  <div data-role="collapsible-set">
    <div data-role="collapsible">
      <h3>Fuel Ecomony</h3>
      <p>You got <strong>${mpg} mpg</strong> during this trip.<!-- This is down 1% from your all-time average of 31.49 mpg.-->
      </p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Distance</h3>
      <p>You traveled <strong>${distance} miles</strong> on approximately <strong>${gallons}</strong> of gasoline during this trip. <!--This is up 14 miles from your average trip distance.--></p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Location</h3>
      <p>You filled up at <strong>${location}</strong>.</p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Cost</h3>
      <p>You spent $<strong>${pumpprice}</strong> at $<strong>${pricepergallon}</strong>/gallon. <!--This is more than your previous fill-up, but down 5% from 6 months ago.-->
      </p>
    </div>
  </div>
</script>

<!-- Primary page -->
<div id="new" data-role="page">
  <?php glHeader(array(
    'title' => 'New Entry'
  )); ?>
  
  <div data-role="content">
    <p>Enter the fill-up information in the form below.</p>
    
    <div id="frmNewContainer">
      <form id="frmNew">   
        <input type="hidden" name="id" value="<?php echo @$_GET['id']; ?>" />
        <div data-role="fieldcontain" class="required">
          <label for="datetime">Date/Time</label>
          <input type="datetime-local" name="datetime" id="datetime" form="frmNew" required="required" aria-required="true" />
        </div>
        <div data-role="fieldcontain" class="required">
          <label for="mileage">Mileage (mi)</label>
          <input type="number" name="mileage" id="mileage" form="frmNew" class="mileage preloadable" required="required" aria-required="true" autofocus />
        </div>
        <div data-role="fieldcontain">
          <label for="location">Location</label>
          <input type="text" name="location" id="location" form="frmNew" class="location preloadable" aria-required="false" />
        </div>
        <div data-role="fieldcontain">
          <label for="pricepergallon" class="required">Price per Gallon ($)</label>
          <input type="number" name="pricepergallon" id="pricepergallon" form="frmNew"  maxlength="5" class="price pricepergallon preloadable" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" />
        </div>
        <div data-role="fieldcontain">
          <div class="required">
            <label for="gallons">Gallons (gal)</label>
            <input type="number" name="gallons" id="gallons" form="frmNew"  maxlength="6" class="gallons" required="required" min="0" max="99.999" step="0.001" placeholder="# of gallons" />
          </div>
          <div>
            <label for="grade"></label>
            <select name="grade" id="grade" form="frmNew" class="grade">
              <option name="reg" id="reg" value="0">Regular Unleaded</option>
              <option name="plus" id="plus" value="1">Plus</option>
              <option name="sup" id="sup" value="2">Supreme</option>
              <option name="dno2" id="dno2" value="3">Diesel (No. 2)</option>
            </select>
          </div>
        </div>
        <div data-role="fieldcontain">
          <label for="pumpprice">Pump Price ($)</label>
          <input type="number" name="pumpprice" id="pumpprice" form="frmNew" maxlength="6" class="price pumpprice" min="0" max="999.99" step="0.01" placeholder="Price paid" />
        </div>
        <div data-role="fieldcontain">
          <label for="notes">Notes</label>
          <textarea name="notes" id="notes" form="frmNew" class="notes" placeholder="Any additional notes"></textarea>
        </div>
        <div data-role="fieldcontain">
          <button id="submit" type="submit" name="submit" data-theme="a">Submit</button>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    /**
     * Page show
     */
    $('#new').live('pageshow', function() {
      // Get default values for some of the fields
      $.ajax({
        url: '<?php echo SCRIPT_URL; ?>ajax_new.php',
        dataType: 'json',
        data: {
          id: '<?php echo $_GET['id']; ?>',
          action: 'defaults'
        },
        cache: false,
        beforeSend: function() {
          // Autofill the date and nothing else
          fillForm();
          
          // Add preloader symbols to textboxes which are getting default 
          // values.
          $('.preloadable').addClass('ajax-textbox');
        },
        success: function(data) {
          // Process response...
          if (data.response == 'new_defaults') {
            // Fill form with default values
            fillForm({
              values: data.values
            });
          }
          else {
            //TODO: error message!
          }
        },
        complete: function() {
          // Set focus to the mileage box
          // BUG: this should work in iOS, but it does not.
          // SEE: http://jsfiddle.net/DLV2F/2/
          var $mileage = $('#mileage');
          var len = $mileage.val().length;
          $mileage
            .focus()
            .selectRange(len, len)
          ;
          
          // Remove default-value preloaders
          $('.preloadable').removeClass('ajax-textbox');
        }
      });
    });
    
    /**
     * Fills the form with the values passed in. The `errors` object contains
     * the IDs of invalid fields; they will be highlighted.
     */
    function fillForm(data) {
      // Start by setting the current time
      $('#datetime').val(getCurrentTimeString());
      
      // Make sure we were given field data...
      if (data === undefined || data.values === undefined) {
        return;
      }
      
      // Loop through all given fields (overwriting datetime, if needed)
      var $firstInvalid = null;
      
      for (var e in data.values) {
        var $e = $('#' + e);
        
        // Re-fill all values (in case PHP script cleaned them)
        $e.val(data.values[e]);
        
        // Mark all invalid items
        if (data.errors !== undefined && data.errors.hasOwnProperty(e)) {
          //console.log('error on ' + e + ': ' + data.values[e] + ' is invalid.');
          $e.parent().addClass('invalid');
          
          if ($firstInvalid == null) {
            $firstInvalid = $e;
          }
        }
        else {
          $e.parent().removeClass('invalid');
        }
      }
      
      return $firstInvalid;
    }
    
    /**
     * Enable or disable form
     */
    function enableForm(val) {
      if (val === undefined || val == true) {
        $('#frmNew input, textarea, select').removeAttr('disabled');
        $('#frmNew label').removeClass('disabled');
        $('#submit')
          .button('enable')
          .parent().find('.ui-btn-text').text('Submit')
        ;
      }
      else {
        $('#frmNew input, textarea, select').attr('disabled', 'disabled');
        $('#frmNew label').addClass('disabled');
        $('#submit')
          .button('disable')
          .parent().find('.ui-btn-text').text('Submitting...')
        ;
      }
    }
    
    /**
     * Document loaded
     */
    $(document).ready(function() {
      // Click listener for form 'Submit' button. The default way jQuery mobile
      // deals with forms is to AJAX redirect to the current page...we don't
      // want that. We'll just handle the operation ourself, sending the data
      // via AJAX.
      $('#submit').click(function() {
        var formData = $('#frmNew').serializeArray();
        
        // HACK ------------------------------------------>
        // jQuery not serializing datetime-local field (as of 1.6.2)
        //   https://gist.github.com/1101324
        //TODO: check for updates periodically
        var hasDatetime = false;
        for (var i in formData) {
          if (i == 'datetime') {
            hasDatetime = true;
            break;
          }
        }
        
        if (! hasDatetime) {
          formData.push({name: 'datetime', value: $('#datetime').val()});
        }
        
        console.log('sending ' + formData);
        //END HACK <---------------------------------------
        
        $.ajax({
          url: '<?php echo SCRIPT_URL; ?>ajax_new.php',
          dataType: 'json',
          data: formData,
          cache: false,
          beforeSend: function() {
            // Disable the form
            enableForm(false);
          },
          success: function(data) {
            if (data.response == 'new_validation_error') {
              var $firstInvalid = fillForm({
                values: data.values,
                errors: data.errors
              });
              
              // Get scroll position of first invalid element
              var pos = 0;
              console.log('first error: ' + $firstInvalid);
              if ($firstInvalid != null) {
                var $p = $firstInvalid.parents('[data-role="fieldcontain"]');
                console.log('parent: ' + $p.length);
                if ($p.length) {
                  pos = $p.offset().top;
                  console.log('set position as ' + pos);
                }
              }
              
              $('html, body').animate({scrollTop: pos}, 100);
            }
            else if (data.response == 'new_success') {
              // Display the stats screen
              $ ('#tmpl-new-success')
                .tmpl(data.stats)
                .appendTo($('#new > div[data-role="content"]').empty())
              ;
              
              $('div [data-role="collapsible"]').collapsible();
              
              // Scroll to top of page
              $('html, body').animate({scrollTop: 0}, 100);
            }
            else {
              console.log('What? ' + data.response);
            }
          },
          error: function(data) {
            console.log('bad: ' + data);
          },
          complete: function() {
            // Re-enable the form
            enableForm();
          }
        });
        
        return false;
      });
      
      // Update price estimate when number of gallons is changed
      $('#gallons, #pricepergallon').change(function() {
        var estCost = $('#pricepergallon').val() * $('#gallons').val();
        
        if (! isNaN(estCost)) {
          $('#pumpprice').val(getMoney(estCost));
        }
      });
    });
    
    
    /*var helpText = {
      datetime: '<p>This field is for the date and time of the fillup. A value should be automatically filled in for you. However, if do you need to change it, you may edit the value manually.</p><p>Use date format <code><?php echo GlApp::DATE_FORMAT; ?></code>, e.g. "<?php echo date(GlApp::DATE_FORMAT); ?>"</p>',
      mileage: '<p>This field is for the odometer reading at the time of the fill-up.</p>
    };
    
    $('#new form label').each(function() {
      $('#tmpl-new-help-link')
        .tmpl({
          url: $(this).attr('for')
        })
        .appendTo(this)
      ;
      
      $(this).find('a').click(function() {
        currentHelp
      });
    });*/
  </script>
  <?php glFooter(); ?>
</div>

<!--<div id="datetime-help" data-role="page">
  <div data-role="header" data-theme="d" data-position="inline">
    <h1>Help</h1>
  </div>
  <div data-role="content" data-theme="c">
    
    <a href="<?php echo BASE_URL; ?>new/" data-role="button" data-rel="back" data-theme="c">Close</a>    
	</div>
</div>-->


<?php
/*****************************************************************************
 * End of page
 *****************************************************************************/
include(TEMPLATE_BASE . '/pageclose.php');
