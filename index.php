<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file creates the page for the mobile version. Commonly used 'pages' are
 * stored in this file as to reduce in-app loading time.
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
    <p id="debugmessage"></p>
    <p id="message">You're home.</p>
    <p>Click to see the  <a href="<?php echo BASE_URL; ?>list/" rel="external">document list</a>.</p>
  </div>
</script>

<script id="tmpl-home-unauthorized" type="text/x-jquery-tmpl">
  <p>Not authorized</p>
  <p>Click <a href="#login">Here to log in.</p>
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
