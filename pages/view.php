<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Displays information about a single document.
 */
?>
<!-- *********************************************************************** -->
<!-- View page templates                                                     -->
<!-- *********************************************************************** -->
<script id="tmpl-view-unauthorized" type="text/x-jquery-tmpl">
  <p><strong>Not authorized.</strong></p>
  <p>You must first <a href="#settings">grant</a> access to your Google Docs account.</p>
</script>

<script id="tmpl-view-error" type="text/x-jquery-tmpl">
  <p><strong>Unknown error.</strong></p>
  <p>MobileMiles was unable to connect to the server. Please try again later or
  <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system admininstrator.</p>
</script>

<script id="tmpl-view" type="text/x-jquery-tmpl">
  <div>
    <a class="add-link" href="#new" data-role="button">Add Entry</a>
  </div>
  <ul id="entrylist" data-role="listview" data-inset="true">
    {{html $item.html()}}
  </ul>
</script>

<script id="tmpl-view-loading" type="text/x-jquery-tmpl">
  {{wrap "#tmpl-view"}}
    <li data-role="list-divider">Loading...</li>
    <li>
      <div class="ajax-loading">
        <div class="ui-icon ui-icon-loading spin loading-img"></div>
        <div class="loading-desc">Fetching entries...</div>
      </div>
    </li>
  {{/wrap}}
</script>

<script id="tmpl-view-no-doc" type="tex/x-jquery-tmpl">
  <p><strong>No document</strong> specified. Please select a document from the 
  <a href="#list">document list</a>.</p>
</script>

<script id="tmpl-view-show" type="text/x-jquery-tmpl">
  {{wrap "#tmpl-view"}}
    <li data-role="list-divider">History</li>
    {{tmpl(entries) "#tmpl-view-item"}}
    <li id="entrylist-loadmore">
      {{tmpl "#tmpl-view-loadmore"}}
    </li>
  {{/wrap}}
</script>

<script id="tmpl-view-item" type="text/x-jquery-tmpl">
  <li><a href="#details">
    <h3>${location}</h3>
    <p><strong>${mpg} mpg (${mpgdelta})</strong></p>
    <p><!--style="white-space: normal;"-->Traveled ${distance} mi on ${gallons} gallons. Total cost was $<span>${pumpprice}</span>.</p>
    <p class="ui-li-aside" style="width: inherit;"><abbr class="timeago" title="${datetime}">${datetime}</span></p>
  </a>
</script>

<script id="tmpl-view-loadmore" type="text/x-jquery-tmpl">
  <h1 class="li-centered">Load 10 more...</h1>
</script>

<script id="tmpl-view-loadmore-in-progress" type="text/x-jquery-tmpl">
  <div class="li-centered">
    <h1>Loading...</h1>
    <img src="<?php echo BASE_URL; ?>images/ajax-loader.gif" />
  </div>
</script>

<!-- *********************************************************************** -->
<!-- View page contents                                                      -->
<!-- *********************************************************************** -->
<div id="view" data-role="page">
  <?php glHeader(array(
    'title' => 'Overview',
    'back' => 'history',
    'settings' => true
  )); ?>
  
  <div data-role="content">
    <div id="view-container"></div>
  </div>
  
  <?php glFooter(); ?>
</div>