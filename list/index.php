<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays a list of available gas logs.
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

<script id="doclist-loading" type="text/x-jquery-tmpl">
  <ul id="ul-doc-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">&nbsp;</li>
    <li>
      <div class="ajax-loading">
        <div class="ui-icon ui-icon-loading spin loading-img"></div>
        <div class="loading-desc">Fetching documents...</div>
      </div>
    </li>
  </ul>
</script>

<script id="doclist-show" type="text/x-jquery-tmpl">
  <ul id="ul-doc-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">Select existing</li>
    {{each(i, doc) docs}}<li><a href="${doc.url}" rel="external">${doc.title}</a></li>{{/each}}
  </ul>
  
  <a id='ul-doc-list-refresh' data-role="button" data-icon="refresh" data-iconpos="top">Refresh</a>
</script>

<script id="doclist-error" type="text/x-jquery-tmpl">
  <ul id="ul-doc-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">Login</li>
    <li>You need to log in.</li>
  </ul>
</script>

<!-- Primary page: shows a list of available documents -->
<div id="doclist" data-role="page">
  <?php glHeader(array(
    'title' => 'Document List'
  )); ?>
  
  <div data-role="content">
    <p>Select a gas log from the document list below. Or you can <a href="#create_instructions">create</a> a new one.</p>
        
    <div id="doclist-container">
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
            callee: '<?php echo BASE_URL; ?>list/'
          },
          beforeSend: function() {
            $('#doclist-container').empty();
            
            $('#doclist-loading')
              .tmpl()
              .appendTo('#doclist-container')
            ;
            
            $('#ul-doc-list').listview();
          }, // end of 'beforeSend'
          error: function(xhr, status, error) {
            console.log('error: ' + status + ', ' + error);
            $('#doclist-container').empty();
            
            $('#doclist-error')
              .tmpl()
              .appendTo('#doclist-container')
            ;
            
            //$.mobile.changePage('#login');
          }, // end of 'error'
          success: function(data) {
            console.log('doclist.success(): ' + data.response);
            $('#doclist-container').empty();
            
            if (data.response != 'doclist_success') {
              $('#doclist-error')
                .tmpl()
                .appendTo('#doclist-container')
              ;
            }
            else {
              $('#doclist-show')
                .tmpl({
                  docs: data.doclist
                })
                .appendTo('#doclist-container')
              ;
              
              $('#ul-doc-list-refresh')
                .click(function() {
                  populateDocList();
                })
                .button()
              ;
            }
          }, // end of 'success'
          complete: function() {
            $('#ul-doc-list').listview();
          } // end of 'complete'
        });
      }

      var doclistFirst = true;
      $('#doclist').live('pageshow', function() {
        // Load when the page is ready
        if (doclistFirst) {
          populateDocList();
          doclistFirst = false;
        }
      });
    </script>
  </div>
  <?php glFooter(); ?>
</div>

<!-- Secondary page: shows instructions on how to create a new document -->
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

<?php
/*****************************************************************************
 * End of page
 *****************************************************************************/
include(TEMPLATE_BASE . '/pageclose.php');
