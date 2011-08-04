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
  <p>You must first  <a href="#settings">grant</a> access to your Google Docs account.</p>
</script>

<script id="tmpl-home-error" type="text/x-jquery-tmpl">
  <p><strong>Unknown error.</strong></p>
  <p>MobileMiles was unable to connect to the server. Please try again later or
  <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system admininstrator.</p>
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
