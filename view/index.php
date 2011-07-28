<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Displays information about a single document.
 */
 
/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once '../scripts/globals.php';

// Display the splash screen, authorization may take a second or so.
include(TEMPLATE_BASE . '/splash.php');
include(TEMPLATE_BASE . '/pageopen.php');
include(TEMPLATE_BASE . '/ui.php');

?>

<script id="tmpl-entrylist-loading" type="text/x-jquery-tmpl">
  <li data-role="list-divider">Loading...</li>
  <li>
    <div class="ajax-loading">
      <div class="ui-icon ui-icon-loading spin loading-img"></div>
      <div class="loading-desc">Fetching entries...</div>
    </div>
  </li>
</script>

<script id="tmpl-entrylist-noid" type="tex/x-jquery-tmpl">
  <li data-role="list-divider">Error</li>
  <li>No document given.</li>
</script>

<script id="tmpl-entrylist-show" type="text/x-jquery-tmpl">
  <li data-role="list-divider">History</li>
  {{tmpl(entries) "#tmpl-entrylist-item"}}
  <li id="entrylist-loadmore">
    {{tmpl "#tmpl-entrylist-loadmore"}}
  </li>
</script>

<script id="tmpl-entrylist-item" type="text/x-jquery-tmpl">
  <li><a href="#details">
    <h3>${location}</h3>
    <p><strong>${mpg} mpg (${mpgdelta})</strong></p>
    <p><!--style="white-space: normal;"-->Traveled ${distance} mi on ${gallons} gallons. Total cost was $<span>${pumpprice}</span>.</p>
    <p class="ui-li-aside" style="width: inherit;"><strong>${friendlydatetime}</strong></p>
  </a>
</script>

<script id="tmpl-entrylist-loadmore" type="text/x-jquery-tmpl">
  <h1 class="li-centered">Load 10 more...</h1>
</script>

<script id="tmpl-entrylist-loadmore-in-progress" type="text/x-jquery-tmpl">
  <div class="li-centered">
    <h1>Loading...</h1>
    <img src="<?php echo BASE_URL; ?>images/ajax-loader.gif" />
  </div>
</script>

<script id="tmpl-entrylist-error" type="text/x-jquery-tmpl">
  <li data-role="list-divider">Login</li>
  <li>You need to log in.</li>
</script>

<script id="tmpl-entrylist-details" type="text/x-jquery-tmpl">
  <p>Details for fill-up, <strong>${friendlydatetime.toLowerCase()}</strong> at <strong>${location}</strong>.</p>
  <div data-role="collapsible-set">
    <div data-role="collapsible">
      <h3>Fuel Ecomony</h3>
      <p>You got <strong>${mpg} mpg</strong> during this trip.<!-- This is down 1% from your all-time average of 31.49 mpg.-->
      </p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Distance and Consumption</h3>
      <p>You traveled <strong>${distance} miles</strong> on <strong>${gallons}</strong> of gasoline during this trip. <!--This is up 14 miles from your average trip distance.--></p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Time and Location</h3>
      <p>You filled up at <strong>${location}</strong> on <strong>${datetime}</strong>.</p>
    </div>
    <div data-role="collapsible" data-collapsed="true">
      <h3>Cost</h3>
      <p>You spent $<strong>${pumpprice}</strong> at $<strong>${pricepergallon}</strong>/gallon. <!--This is more than your previous fill-up, but down 5% from 6 months ago.-->
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

<div id="view" data-role="page">
  <?php glHeader(array(
    'title' => 'Overview',
    'back' => 'history',
    'settings' => true
  )); ?>
  
  <div data-role="content">
    <div>
      <a href="<?php echo BASE_URL; ?>new/?id=<?php echo @$_GET['id']; ?>" rel="external" data-role="button">Add Entry</a>
    </div>
    
    <ul id="entrylist" data-role="listview" data-inset="true">
      <!-- Entries go here -->
    </ul>
  </div>
  
  <script type="text/javascript">
    $('#doc-view-more').click(function() {
      $('#doc-view-more > h1').text('Loading...');
    });
    
    var entries = [];
    var detailEntry;
    
    function populateEntryList(offset, num, callbacks) {
      var url = '<?php echo SCRIPT_URL; ?>ajax_entrylist.php';
      
      // Make AJAX call
      $.ajax({
        url: url,
        dataType: 'json',
        data: {
          callee: '<?php echo BASE_URL; ?>view/',
          id: '<?php echo @$_GET['id']; ?>',
          offset: offset,
          num: num
        },
        beforeSend: callbacks.beforeSend,
        error: callbacks.error,
        success: callbacks.success,
        complete: function() {
          $('#entrylist').listview('refresh');
          
          if (callbacks.complete !== undefined) {
            callbacks.complete();
          }
        }
      });
    }
    
    function firstPopulateEntryList() {
      populateEntryList(0, 5, {
        beforeSend: function() {
          $('#tmpl-entrylist-loading')
            .tmpl()
            .appendTo($('#entrylist').empty())
          ;
          
          $('#entrylist').listview('refresh');
        }, // end of 'beforeSend'
        error: function(xhr, status, error) {
          console.log('error: ' + status + ', ' + error);
          
          $('#tmpl-entrylist-error')
            .tmpl()
            .appendTo($('#entrylist').empty())
          ;
        }, // end of 'error'
        success: function(data) {
          //console.log('entrylist.success(): ' + data.response);
          
          if (data.response == 'entrylist_no_id') {
            console.log('no id given...');
            $('#tmpl-entrylist-noid')
              .tmpl()
              .appendTo($('#entrylist').empty())
            ;
          }
          else if (data.response != 'entrylist_success') {
            console.log('error, response was ' + data.response);
            $('#tmpl-entrylist-error')
              .tmpl()
              .appendTo($('#entrylist').empty())
            ;
          }
          else {
            entries = data.entrylist;
            
            $('#tmpl-entrylist-show')
              .tmpl({
                entries: entries
              })
              .appendTo($('#entrylist').empty())
            ;
            
            $('#entrylist-loadmore').click(function() {
              loadMoreIntoEntryList();
            });
          }
        }, // end of 'success'
      });
    }
    
    function loadMoreIntoEntryList() {
      //console.log('loading 10 more...(' + entries.length + '->' + (entries.length + 10) + ')');
      
      populateEntryList(entries.length, 10, {
        beforeSend: function() {
          //console.log('prepping...');
          $('#tmpl-entrylist-loadmore-in-progress')
            .tmpl()
            .appendTo($('#entrylist-loadmore').empty())
          ;
          
          $('#entrylist').listview('refresh');
        },
        error: function(xhr, status, error) {
          console.log('bad: ' + error);
        },
        success: function(data) {
          console.log('entrylist(more).success(): ' + data.range);
          
          if (data.response != 'entrylist_success') {
            $('#tmpl-entrylist-error')
              .tmpl()
              .appendTo($('#entrylist').empty())
              .page()
            ;
          }
          else {
            entries = entries.concat(data.entrylist);
            
            $('#tmpl-entrylist-item')
              .tmpl(data.entrylist)
              .insertBefore($('#entrylist li:last'))
            ;
          }
        },
        complete: function() {
          $('#tmpl-entrylist-loadmore')
            .tmpl()
            .appendTo($('#entrylist-loadmore').empty())
          ;
          
          $('#entrylist').listview('refresh');
        }
      });
    }

    var entrylistFirst = true;
    $('#view').live('pageshow', function() {
      // Load when the page is ready
      if (entrylistFirst) {
        entries = [];
        firstPopulateEntryList();
        entrylistFirst = false;
      }
    });
    
    $('#entrylist li').live('click', function(e) {
      var i = $(this).index() - 1;
      
      if (! isNaN(i) && i > -1 && i < entries.length) {
        detailEntry = entries[i];
      }
      else /*TODO: if not the 'load more' button...*/ {
        e.preventDefault();
        //TODO: handle better
        //alert('No entry found');
      }
    });
  </script>
  
  <?php glFooter(); ?>
</div>

<div id="details" data-role="page">
  <?php glHeader(array(
    'title' => 'Details',
    'back' => 'history',
    'settings' => true
  )); ?>
  
  <div data-role="content">
    <script type="text/javascript">
      $('#details').live('pageshow', function() {
        if (detailEntry != null && detailEntry !== undefined) {
          // Display the template
          $('#tmpl-entrylist-details')
            .tmpl(detailEntry, {
              toLower: function(str) {
                return str.toLowerCase();
              }
            })
            .appendTo($('#details [data-role="content"]').empty())
          ;
          
          $('div [data-role="collapsible"]').collapsible();
        }
      });
    </script>
  </div>
  
  <?php glFooter(); ?>
</div>

<?php
/*****************************************************************************
 * End of page
 *****************************************************************************/
include(TEMPLATE_BASE . '/pageclose.php');
