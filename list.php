<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays a list of available gas logs.
 */

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>

<!-- *********************************************************************** -->
<!-- Templates                                                               -->
<!-- *********************************************************************** -->
<script id="list-loading" type="text/x-jquery-tmpl">
  <ul id="ul-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">&nbsp;</li>
    <li>
      <div class="ajax-loading">
        <div class="ui-icon ui-icon-loading spin loading-img"></div>
        <div class="loading-desc">Fetching documents...</div>
      </div>
    </li>
  </ul>
</script>

<script id="list-show" type="text/x-jquery-tmpl">
  <ul id="ul-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">Select existing</li>
    {{each(i, doc) docs}}<li><a class="view-link" data-id="${doc.id}" href="#view">${doc.title}</a></li>{{/each}}
  </ul>
  
  <a id='ul-list-refresh' data-role="button" data-icon="refresh" data-iconpos="top">Refresh</a>
</script>

<script id="list-error" type="text/x-jquery-tmpl">
  <ul id="ul-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">Login</li>
    <li>You need to log in.</li>
  </ul>
</script>

<!-- *********************************************************************** -->
<!-- Primary page: shows a list of available documents                       -->
<!-- *********************************************************************** -->
<div id="list" data-role="page">
  <?php glHeader(array(
    'title' => 'Document List'
  )); ?>
  
  <div data-role="content">
    <p>Select a gas log from the document list below. Or you can <a href="#create_instructions">create</a> a new one.</p>
        
    <div id="list-container">
    </div>
  
    <div>
      <a id="create_instructions_btn" href="#create_instructions" data-role="button" data-transition="slideup">Create new</a>
    </div>
    
    <script type="text/javascript">
      /**
       * Loads document list
       */
      function populateDocList() {
        var url = '<?php echo SCRIPT_URL; ?>ajax_doclist.php';
        
        // Make AJAX call
        $.ajax({
          url: url,
          dataType: 'json',
          data: {
            callee: '<?php echo BASE_URL; ?>#list'
          },
          beforeSend: function() {
            $('#list-container').empty();
            
            $('#list-loading')
              .tmpl()
              .appendTo('#list-container')
            ;
            
            $('#ul-list').listview();
          }, // end of 'beforeSend'
          error: function(xhr, status, error) {
            console.log('error: ' + status + ', ' + error);
            $('#list-container').empty();
            
            $('#list-error')
              .tmpl()
              .appendTo('#list-container')
            ;
            
            //$.mobile.changePage('#login');
          }, // end of 'error'
          success: function(data) {
            //console.log('list.success(): ' + data.response);
            $('#list-container').empty();
            
            if (data.response != 'doclist_success') {
              $('#list-error')
                .tmpl()
                .appendTo('#list-container')
              ;
            }
            else {
              $('#list-show')
                .tmpl({
                  docs: data.doclist
                })
                .appendTo('#list-container')
              ;
              
              $('#ul-list-refresh')
                .click(function() {
                  populateDocList();
                })
                .button()
              ;
            }
          }, // end of 'success'
          complete: function() {
            $('#ul-list').listview();
          } // end of 'complete'
        });
      }

      //var doclistFirst = true;
      $('#list').live('pageshow', function() {
        //console.log('showing ');
        // Load when the page is ready
        if (mobileMiles.refreshDocList) {
          populateDocList();
          mobileMiles.refreshDocList = false;
        }
      });
      
      $('.view-link').live('click', function() {
        var id = $(this).data('id');
        if (id !== undefined && id.length > 0) {
          mobileMiles.doc = id;
          mobileMiles.refreshEntryList = true;
        }
      })
    </script>
  </div>
  <?php glFooter(); ?>
</div>

<!-- *********************************************************************** -->
<!-- Secondary page: shows instructions on how to create a new document      -->
<!-- *********************************************************************** -->
<div id="create_instructions" data-role="page">
  <?php glHeader(array(
    'title' => 'Create New'
  )); ?>
  <div data-role="content">
    <p>To create a new gas log, make a copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a> via <code>File -> Make a copy..</code>. Save it with the extension <code><?php echo FILTER_TEXT; ?></code> so it shows up in your gas log list.
    </p>
  </div>
  <?php glFooter(); ?>
</div>
