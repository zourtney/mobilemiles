<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Displays information about a single document.
 */
?>

<!-- *********************************************************************** -->
<!-- Templates                                                               -->
<!-- *********************************************************************** -->
<script id="tmpl-entrylist" type="text/x-jquery-tmpl">
  <div>
    <a class="add-link" href="#new" data-role="button">Add Entry</a>
  </div>
  <ul id="entrylist" data-role="listview" data-inset="true">
    {{html $item.html()}}
  </ul>
</script>

<script id="tmpl-entrylist-loading" type="text/x-jquery-tmpl">
  {{wrap "#tmpl-entrylist"}}
    <li data-role="list-divider">Loading...</li>
    <li>
      <div class="ajax-loading">
        <div class="ui-icon ui-icon-loading spin loading-img"></div>
        <div class="loading-desc">Fetching entries...</div>
      </div>
    </li>
  {{/wrap}}
</script>

<script id="tmpl-entrylist-no-doc" type="tex/x-jquery-tmpl">
  <p><strong>No document</strong> specified. Please select a document from the 
  <a href="#list">document list</a>.</p>
</script>

<script id="tmpl-entrylist-error" type="text/x-jquery-tmpl">
  {{wrap "#tmpl-entrylist"}}
    <li data-role="list-divider">Login</li>
    <li>Error fetching entries. Please try again later.</li>
  {{/wrap}}
</script>

<script id="tmpl-entrylist-show" type="text/x-jquery-tmpl">
  {{wrap "#tmpl-entrylist"}}
    <li data-role="list-divider">History</li>
    {{tmpl(entries) "#tmpl-entrylist-item"}}
    <li id="entrylist-loadmore">
      {{tmpl "#tmpl-entrylist-loadmore"}}
    </li>
  {{/wrap}}
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

<script id="tmpl-entrylist-details" type="text/x-jquery-tmpl">
  <p>Details for fill-up, <strong>${friendlydatetime.toLowerCase()}</strong> at <strong>${location}</strong>.</p>
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
      <h3>Time and Location</h3>
      <p>You filled up at <strong>${location}</strong> on <strong>${datetime}</strong>.</p>
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

<!-- *********************************************************************** -->
<!-- Primary page: view gas log entries                                      -->
<!-- *********************************************************************** -->
<div id="view" data-role="page">
  <?php glHeader(array(
    'title' => 'Overview',
    'back' => 'history',
    'settings' => true
  )); ?>
  
  <div data-role="content">
  </div>
  
  <script type="text/javascript">
    function populateEntryList(offset, num, callbacks) {
      var url = '<?php echo SCRIPT_URL; ?>ajax_entrylist.php';
      
      // Make AJAX call
      $.ajax({
        url: url,
        dataType: 'json',
        data: {
          callee: '<?php echo BASE_URL; ?>#view',
          id: mobileMiles.doc,
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
    
    function repopulateEntryList() {
      populateEntryList(0, 5, {
        beforeSend: function() {
          $('#tmpl-entrylist-loading')
            .tmpl()
            .appendTo($('#view div[data-role="content"]').empty())
          ;
          
          $('.add-link').button();
          $('#entrylist').listview();
        }, // end of 'beforeSend'
        error: function(xhr, status, error) {
          console.log('error: ' + status + ', ' + error);
          
          $('#tmpl-entrylist-error')
            .tmpl()
            .appendTo($('#view div[data-role="content"]').empty())
          ;
          
          $('.add-link').button();
          $('#entrylist').listview();
        }, // end of 'error'
        success: function(data) {
          if (data.response == 'entrylist_no_doc') {
            $('#tmpl-entrylist-no-doc')
              .tmpl()
              .appendTo($('#view div[data-role="content"]').empty())
            ;
          }
          else if (data.response != 'entrylist_success') {
            console.log('Invalid response ' + data.response);
            $('#tmpl-entrylist-error')
              .tmpl()
              .appendTo($('#view div[data-role="content"]').empty())
            ;
            
            $('.add-link').button();
            $('#entrylist').listview();
          }
          else {
            mobileMiles.entries = data.entrylist;
            
            $('#tmpl-entrylist-show')
              .tmpl({
                entries: mobileMiles.entries
              })
              .appendTo($('#view div[data-role="content"]').empty())
            ;
            
            $('#entrylist-loadmore').click(function() {
              appendToEntryList();
            });
            
            $('.add-link').button();
            $('#entrylist').listview();
          }
        }, // end of 'success'
      });
    }
    
    function appendToEntryList() {
      populateEntryList(mobileMiles.entries.length, 10, {
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
          //console.log('entrylist(more).success(): ' + data.range);
          if (data.response != 'entrylist_success') {
            $('#tmpl-entrylist-error')
              .tmpl()
              .appendTo($('#entrylist').empty())
              .page()
            ;
          }
          else {
            mobileMiles.entries = mobileMiles.entries.concat(data.entrylist);
            
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

    $('#view').live('pageshow', function() {
      // Load when the page is ready
      if (mobileMiles.refreshEntryList) {
        mobileMiles.entries = [];
        repopulateEntryList();
        mobileMiles.refreshEntryList = false;
      }
    });
    
    $('#entrylist li').live('click', function(e) {
      var i = $(this).index() - 1;
      
      if (! isNaN(i) && i > -1 && i < mobileMiles.entries.length) {
        mobileMiles.curEntry = mobileMiles.entries[i];
      }
      else /*TODO: if not the 'load more' button...*/ {
        e.preventDefault();
        //TODO: handle better
        //alert('No entry found');
      }
    });
    
    /*$('.add-link').live('click', function() {
      
    });*/
  </script>
  
  <?php glFooter(); ?>
</div>

<!-- *********************************************************************** -->
<!-- Secondary page: detail view for a gas log entry                         -->
<!-- *********************************************************************** -->
<div id="details" data-role="page">
  <?php glHeader(array(
    'title' => 'Details',
    'back' => 'history',
    'settings' => true
  )); ?>
  
  <div data-role="content">
    <script type="text/javascript">
      $('#details').live('pageshow', function() {
        if (mobileMiles.curEntry != null && mobileMiles.curEntry !== undefined) {
          // Display the template
          $('#tmpl-entrylist-details')
            .tmpl(mobileMiles.curEntry, {
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
