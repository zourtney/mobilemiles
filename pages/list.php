<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays a list of available gas logs.
 */

/*****************************************************************************
 * Page display logic
 *****************************************************************************/
?>

<!-- *********************************************************************** -->
<!-- Templates                                                               -->
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
  <ul id="ul-list" data-role="listview" data-inset="true">
    <li data-role="list-divider">&nbsp;</li>
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
    <li data-role="list-divider">Select existing</li>
    {{each(i, doc) docs}}<li><a class="view-link" data-id="${doc.id}" href="#view">${doc.title}</a></li>{{/each}}
  </ul>
  
  <a id='ul-list-refresh' data-role="button" data-icon="refresh" data-iconpos="top">Refresh</a>
</script>

<!-- *********************************************************************** -->
<!-- Primary page: shows a list of available documents                       -->
<!-- *********************************************************************** -->
<div id="list" data-role="page">
  <?php glHeader(array(
    'title' => 'Document List'
  )); ?>
  
  <div data-role="content">
    <p>Select a gas log from the document list below. Or you can <a href="#create_instructions">create</a> a new one.</p>
        
    <div id="list-container">
    </div>
  
    <div>
      <a id="create_instructions_btn" href="#create_instructions" data-role="button" data-transition="slideup">Create new</a>
    </div>
  </div>
  <?php glFooter(); ?>
</div>