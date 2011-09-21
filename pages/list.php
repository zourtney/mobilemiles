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

<script id="tmpl-list-loading" type="text/x-jquery-tmpl">  
	<div class="ajax-loading">
		<div class="ui-icon ui-icon-loading spin loading-img"></div>
		<div class="loading-desc">Fetching documents...</div>
	</div>  
</script>

<script id="tmpl-list-show" type="text/x-jquery-tmpl">
	<div data-role="fieldcontain">
		<fieldset data-role="controlgroup">
			{{each(i, doc) docs}}
				{{if ! doc.valid}}
					<a class="list-chk-disabled" href="#doc_update_instructions"><input type="radio" name="list-chk-group" name="list-chk-${doc.id}" id="list-chk-${doc.id}" class="list-chk" value="${doc.id}" data-doc-title="${doc.title}" disabled="disabled" />
					<label for="list-chk-${doc.id}" class="list-label" data-icon="alert">${doc.title} <span class="list-chk-incompatible">(incompatible)</span></label></a>
				{{else}}
					<input type="radio" name="list-chk-group" name="list-chk-${doc.id}" id="list-chk-${doc.id}" class="list-chk" value="${doc.id}" data-doc-title="${doc.title}" />
					<label for="list-chk-${doc.id}"
					class="list-label">${doc.title}</label>
				{{/if}}
			{{/each}}
		</fieldset>
	</div>
	
	{{if hasInvalid}}
		<div class="warning-explanation">
			<div class="ui-icon ui-icon-alert"></div>
			<p>One or more documents are incompatible with this version. Learn how to <a href="#doc_update_instructions">upgrade</a> your document.</p>
		</div>
	{{/if}}
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