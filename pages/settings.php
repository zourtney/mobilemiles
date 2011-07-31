<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the login/settings page.
 */

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>

<script id="settings_request" type="text/x-jquery-tmpl">
  <p>Before continuing, you must <a href="${url}" rel="external">grant</a> access to your Google Docs account.</p>
  <a id="create_instructions_btn" href="${url}" data-role="button" rel="external">Grant Access</a>
</script>

<script id="settings_success" type="text/x-jquery-tmpl">
  <p>You have authorized this application to access your Google Docs account.</p>
  <p>Go to your <a href="<?php echo BASE_URL; ?>#list">document list</a> to enter or view data.</p>
  <a href="#logout" data-role="button" data-theme="f">Revoke</a>
</script>

<!-- Primary page -->
<div id="settings" data-role="page">
  <?php glHeader(array(
    'title' => 'Settings',
    'back' => 'history',
    'settings' => false
  )); ?>
  
  <div data-role="content">
  </div>
  
  <script type="text/javascript">
    $('#settings').live('pageshow', function() {    
      $.ajax({
        url: '<?php echo SCRIPT_URL; ?>ajax_login.php',
        dataType: 'json',
        data: {
          next: '<?php echo BASE_URL; ?>#settings'
        },
        success: function(data) {
          switch (data.response) {
            case 'login_unauthorized':
            case 'login_failed':
              $('#settings_request')
                .tmpl({
                  url: data.url
                })
                .appendTo($('#settings > [data-role="content"]').empty())
              ;
              
              // Suckiness: have to explicitly reconstruct compontents. I tried
              // using $(#settings [data-role="button"]).page()...and it work the
              // first time, but kills the styling when revisiting a cached page.
              // Hopefully this gets fixed in future versions of jQuery Mobile...
              $('#settings [data-role="button"]').button();
              
              //TODO: fix unintuitive 'Back' functionality after authorization.
              break;
            case 'login_succeeded':
              //console.log('succeeded');
              $('#settings_success')
                .tmpl()
                .appendTo($('#settings > [data-role="content"]').empty())
              ;
              
              // See complaint above.
              $('#settings [data-role="button"]').button();
              break;
            default:
              console.log('what is ' + data.response + '?');
              break;
          }
        },
        error: function(a, b, c) {
          console.log(b);
        }
      });
    });
  </script>
  <?php glFooter(); ?>
</div>

<div id="logout" data-role="page">
  <div data-role="content">Logging out...</div>
</div>

<script type="text/javascript">
  $('#logout').live('pageshow', function() {
    console.log('showing logout page');
    $.ajax({
      url: '<?php echo SCRIPT_URL; ?>ajax_login.php?action=logout',
      dataType: 'json',
      success: function(data) {
        console.log(data.response + ', ' + data.url);
        
        if (data.response == 'logout_success') {
          $.mobile.changePage('#settings');
        }
      }
    });
  });
</script>
