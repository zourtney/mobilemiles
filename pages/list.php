<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays a list of available gas logs.
 */
?>
<!-- *********************************************************************** -->
<!-- List page templates                                                     -->
<!-- *********************************************************************** -->
<script id="tmpl-list-unauthorized" type="text/x-jquery-tmpl">
  <p><strong>Not authorized.</strong></p>
  <p>You must first  <a href="#settings">grant</a> access to your Google Docs account.</p>
</script>

<script id="tmpl-list-error" type="text/x-jquery-tmpl">
  <p><strong>Unknown error.</strong></p>
  <p>MobileMiles was unable to connect to the server. Please try again later or
  <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system admininstrator.</p>
</script>

<script id="tmpl-list-header" type="text/x-jquery-tmpl">
	<li data-role="list-divider">
		<span>${title}</span>
		<!--TODO: finish
		<span class="ui-li-count list-btn-right" style="padding: 0;">
			<a href="#some-refresh-page" data-role="button" data-icon="refresh" data-iconpos="notext" data-theme="d" style="margin: 0;">Refresh</a>
		</span>-->
	</li>
</script>

<script id="tmpl-list-loading" type="text/x-jquery-tmpl">
  <ul id="ul-list" data-role="listview" data-inset="true">
    {{tmpl({title: "&nbsp;"}) "#tmpl-list-header"}}
    <li>
      <div class="ajax-loading">
        <div class="ui-icon ui-icon-loading spin loading-img"></div>
        <div class="loading-desc">Fetching documents...</div>
      </div>
    </li>
  </ul>
</script>

<script id="tmpl-list-show" type="text/x-jquery-tmpl">
  <ul id="ul-list" data-role="listview" data-inset="true">
    {{tmpl({title: "Select existing"}) "#tmpl-list-header"}}
    {{each(i, doc) docs}}
      {{if doc.version.app < <?php echo APP_VERSION; ?> || doc.version.doc < <?php echo SPREADSHEET_VERSION; ?>}}
        <li data-icon="alert"><a class="view-link" data-id="${doc.id}" href="#doc_update_instructions">${doc.title}</a></li>
      {{else}}
        <li><a class="view-link" data-id="${doc.id}" data-doc-title="${doc.title}" href="#view">${doc.title}</a></li>
      {{/if}}
    {{/each}}
  </ul>
</script>

<!-- *********************************************************************** -->
<!-- List page contents                                                      -->
<!-- *********************************************************************** -->
<div id="list" data-role="page">
  <?php glHeader(array(
    'title' => 'Document List'
  )); ?>
  
  <div data-role="content">
    <p>Select a MobileMiles document from the list below. If the list is out of date, you can manually <a id="ul-list-refresh" href="#">refresh</a> it. If you are just getting started, or wish to add a second vehicle, check out these <a href="#create_instructions">instructions</a>.</p>
        
    <div id="list-container">
    </div>
  </div>
  <?php glFooter(); ?>
</div>