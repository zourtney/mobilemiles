<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This is the home page. It's not very useful at the moment.
 */

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>

<!-- *********************************************************************** -->
<!-- Templates                                                               -->
<!-- *********************************************************************** -->
<script id="tmpl-home" type="text/x-jquery-tmpl">
  <div data-role="content">
    <p id="message">Welcome to <strong>MobileMiles</strong>, the webapp for tracking your gas station fill-up stats from your mobile device.</p>
    <p>If you're just getting started, save a copy of the <a href="<?php echo SPREADSHEET_MASTER_URL; ?>" rel="external" target="_blank">master spreadsheet</a> to your Google Docs account with <code><?php echo FILTER_TEXT; ?></code> in the filename. Once you're ready, view your  <a href="#list">document list</a> and start entering and viewing data. </p>
  </div>
</script>

<script id="tmpl-home-unauthorized" type="text/x-jquery-tmpl">
  <p><strong>Not authorized.</strong></p>
  <p>You must first  <a href="<?php echo BASE_URL; ?>#settings">grant</a> access to your Google Docs account.</p>
</script>


<!-- *********************************************************************** -->
<!-- Primary page: home screen                                               -->
<!-- *********************************************************************** -->
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
