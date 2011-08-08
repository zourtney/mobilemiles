<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the login/settings page.
 */
?>
<!-- *********************************************************************** -->
<!-- Settings page templates                                                 -->
<!-- *********************************************************************** -->
<script id="tmpl-settings-request" type="text/x-jquery-tmpl">
  <p>Before continuing, you must <a href="${url}" rel="external">grant</a> access to your Google Docs account.</p>
  <a id="create_instructions_btn" href="${url}" data-role="button" rel="external">Grant Access</a>
</script>

<script id="tmpl-settings-success" type="text/x-jquery-tmpl">
  <p>You have authorized this application to access your Google Docs account.</p>
  <p>Go to your <a href="<?php echo BASE_URL; ?>#list">document list</a> to enter or view data.</p>
  <a href="#logout" data-role="button" data-theme="f">Revoke</a>
</script>

<script id="tmpl-settings-error" type="text/x-jquery-tmpl">
  <p><strong>Unknown error.</strong></p>
  <p>MobileMiles was unable to connect to the server. Please try again later or
  <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system admininstrator.</p>
</script>


<!-- *********************************************************************** -->
<!-- Settings pages content                                                  -->
<!-- *********************************************************************** -->
<div id="settings" data-role="page">
  <?php glHeader(array(
    'title' => 'Settings',
    'back' => 'history',
    'settings' => false
  )); ?>
  
  <div data-role="content">
  </div>
  
  <?php glFooter(); ?>
</div>