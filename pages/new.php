<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This page lets you add a new entry
 */
?>

<!--<script id="tmpl-new-help-link" type="text/x-jquery-tmpl">
  <a href="${url}" data-rel="dialog" data-transition="pop" data-role="button" data-icon="info" data-iconpos="notext">Help</a>
</script>-->

<script id="tmpl-new-no-doc" type="text/x-jquery-tmpl">
  <p><strong>No document</strong> specified. Please select a document from the 
  <a href="#list">document list</a>.</p>
</script>

<script id="tmpl-new-error" type="text/x-jquery-tmpl">
  <p><strong>Submission error.</strong> Your data could not be submitted at this time. Please try again later or <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system administrator if the error persists.</p>
</script>

<script id="tmpl-new-form" type="text/x-jquery-tmpl">
  <p>Enter the fill-up information in the form below.</p>
  <div id="frmNewContainer">
    <form id="frmNew">   
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
        <input type="number" name="pricepergallon" id="pricepergallon" form="frmNew"  maxlength="5" class="price pricepergallon preloadable updateprice" required="required" min="0.0" max="9.999" step="0.01" placeholder="Price/gallon" />
      </div>
      <div data-role="fieldcontain">
        <div class="required">
          <label for="gallons">Gallons (gal)</label>
          <input type="number" name="gallons" id="gallons" form="frmNew"  maxlength="6" class="gallons updateprice" required="required" min="0" max="99.999" step="0.001" placeholder="# of gallons" />
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
        <button id="submit" type="submit" name="submit" class="submit" data-theme="a">Submit</button>
      </div>
    </form>
  </div>
</script>

<script id="tmpl-new-success" type="text/x-jquery-tmpl">
  <p><strong>Submitted successfully.</strong></p>
  
  <p>Review your latest entry or view <a class="view-link" data-id="${id}" href="#view">all entries</a>.</p>
  
  <div data-role="collapsible-set">
    <div data-role="collapsible">
      <h3>Fuel Ecomony</h3>
      <p>You got <strong>${mpg} mpg</strong> during this trip.
      </p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Distance and Consumption</h3>
      <p>You traveled <strong>${distance} miles</strong> on <strong>${gallons}</strong> gallons of gasoline during this trip.</p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Location</h3>
      <p>You filled up at <strong>${location}</strong>.</p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Cost</h3>
      <p>You spent $<strong>${pumpprice}</strong> at $<strong>${pricepergallon}</strong>/gallon.
      </p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Notes</h3>
      {{if notes}}
        <p>${notes}</p>
      {{else}}
        <p class="disabled">No notes for this fill-up</p>
      {{/if}}
    </div>
  </div>
</script>

<!-- Primary page -->
<div id="new" data-role="page">
  <?php glHeader(array(
    'title' => 'New Entry'
  )); ?>
  
  <div data-role="content">
  </div>
  
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