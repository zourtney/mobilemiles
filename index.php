<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Home page for the MobileMiles webapp.
 */

/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once 'scripts/globals.php';

// Display the splash screen, authorization may take a second or so.
include(TEMPLATE_BASE . '/splash.php');
include(TEMPLATE_BASE . '/pageopen.php');
include(TEMPLATE_BASE . '/ui.php');

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>
<script id="tmpl-home" type="text/x-jquery-tmpl">
  <div data-role="content">
    <p id="message">Welcome to <strong>MobileMiles</strong>, the webapp for tracking your gas station fill-up stats from your mobile device.</p>
    <p>If you're just getting started, save a copy of the <a href="<?php echo SPREADSHEET_MASTER_URL; ?>" rel="external" target="_blank">master spreadsheet</a> to your Google Docs account with <code><?php echo FILTER_TEXT; ?></code> in the filename. Once you're ready, view your  <a href="<?php echo BASE_URL; ?>list/" rel="external">document list</a> and start entering and viewing data. </p>
  </div>
</script>

<script id="tmpl-home-unauthorized" type="text/x-jquery-tmpl">
  <p><strong>Not authorized.</strong></p>
  <p>You must first  <a href="<?php echo BASE_URL; ?>settings/" rel="external">grant</a> access to your Google Docs account.</p>
</script>

<div id="home" data-role="page">
  <?php glHeader(array(
    'title' => 'Home',
    'back' => false
  )); ?>
  <div data-role="content">
    <p>Loading...</p>
  </div>
  <?php glFooter(); ?>
</div>

<script type="text/javascript">
  $('#home').live('pageshow', function() {
    //console.log('page shown...');
    $.ajax({
      url: 'scripts/ajax_login.php',
      dataType: 'json',
      success: function(data) {
        switch (data.response) {
          case 'login_unauthorized':
          case 'login_failed':
            $('#tmpl-home-unauthorized')
              .tmpl()
              .appendTo($('#home [data-role="content"]').empty())
            ;
            break;
          case 'login_succeeded':
            //$('#message').html('You are now logged in. You may <a href="' + data.url + '">log out</a> at any time.');
            //window.close();
            $('#tmpl-home')
              .tmpl()
              .appendTo($('#home [data-role="content"]').empty())
            ;
            
            $('#pageTitle').text('Home');
            $('#btnBack').hide();
            $('#btnSettings').show();
            break;
          default:
            console.log('what? ' + data.response);
            break;
        }
      },
      error: function() {
        console.log('error!');
      }
    });
  });
</script>

<?php
/*****************************************************************************
 * End of page
 *****************************************************************************/
include(TEMPLATE_BASE . '/pageclose.php');
