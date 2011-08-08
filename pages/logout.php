<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the logout page.
 */
?>
<!-- *********************************************************************** -->
<!-- Logout page templates                                                   -->
<!-- *********************************************************************** -->

<script id="tmpl-logout-error" type="text/x-jquery-tmpl">
  <p><strong>Unknown error.</strong></p>
  <p>MobileMiles was unable to connect to the server. Please try again later or
  <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system admininstrator.</p>
</script>

<!-- *********************************************************************** -->
<!-- Logout page contents                                                    -->
<!-- *********************************************************************** -->
<div id="logout" data-role="page">
  <?php glHeader(array(
    'title' => 'Logout',
    'back' => false,
    'settings' => false
  )); ?>
  
  <div data-role="content">Logging out...</div>
  
  <?php glFooter(); ?>
</div>
