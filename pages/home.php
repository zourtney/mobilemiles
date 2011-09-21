<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This is the home page. It's not very useful at the moment.
 */
?>
<!-- *********************************************************************** -->
<!-- Home page templates                                                     -->
<!-- *********************************************************************** -->
<script id="tmpl-home" type="text/x-jquery-tmpl">
	<?php if (SHOW_CHANGES_MESSAGE) { ?>
	<ul class="changes" data-role="listview" data-theme="e">
		<li>
			<a href="#changes">
				<p><strong style="font-size: 1.3em">There are important changes</strong></p>
				<p>in this or recent versions. <em>Learn more...</em></p>
			</a>
		</li>
	</ul>
	<?php } ?>
	
	<p id="message">Welcome to <strong>MobileMiles</strong>, the webapp for tracking your gas station fill-up stats from your mobile device.</p>
	
	<div id="main-grid" class="ui-grid-a">
		<div class="ui-block-a"><a class="list" href="#list">Vehicle</a></div>
		<div class="ui-block-b"><a class="view" href="#view">Entries</a></div>
		<div class="ui-block-a"><a class="new" href="#new">Add Entry</a></div>
		<div class="ui-block-b"><a class="settings" href="#settings">Settings</a></div>
	</div>
	
	<p>Just getting started? Check out the <a href="#create_instructions">setup instructions</a>.</p>

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
<!-- Home page content                                                       -->
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