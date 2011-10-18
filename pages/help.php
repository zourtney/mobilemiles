<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file simple help file pages.
 */
?>
<!-- *********************************************************************** -->
<!-- Changes page content                                                    -->
<!-- *********************************************************************** -->
<div id="changes" data-role="page">
  <?php glHeader(array(
    'title' => 'Changes',
    'subtitle' => false
  )); ?>
  <div data-role="content">
    <p>From <abbr class="timeago" title="2011-08-08 00:00:00">2011-08-08</abbr> onward, the vehicle document filter text shall be <code><?php echo FILTER_TEXT; ?></code>. Spreadsheets containing the old filter text <code>(rlgaslog)</code> will <em>not</em> show up in your MobileMiles Vehicle List. To edit the document title(s), visit your <a href="http://docs.google.com">Google Docs</a> page.</p>
  </div>
  <?php glFooter() ?>
</div>

<!-- *********************************************************************** -->
<!-- Create instructions page content                                        -->
<!-- *********************************************************************** -->
<div id="create_instructions" data-role="page">
  <?php glHeader(array(
    'title' => 'Create New',
    'subtitle' => false
  )); ?>
  <div data-role="content">
    <p>To start logging information on a new vehicle, make a copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a> via <code>File -> Make a copy..</code>. Save it with a descriptive name, followed by <code><?php echo FILTER_TEXT; ?></code>. This text string is what MobileMiles looks for in your Google Docs list.</p>
    <p>Once you've created a new document for your vehicle, select it from the <a href="#list">vehicle list</a> page. Then, you can <a href="#new">add</a> or <a href="#view">view</a> entries from the <a href="#home">home</a> screen.</p>
  </div>
  <?php glFooter(); ?>
</div>

<!-- *********************************************************************** -->
<!-- Document update instructions page content                               -->
<!-- *********************************************************************** -->
<div id="doc_update_instructions" data-role="page">
  <?php glHeader(array(
    'title' => 'Upgrade',
    'subtitle' => false
  )); ?>
  <div data-role="content">
    <p>The requested vehicle document is <strong>out of date</strong>. This happens when there is a significant change to the spreadsheet and/or MobileMiles application. Minimum application version is <strong><?php echo APP_VERSION; ?></strong>; minimum spreadsheet version is <strong><?php echo SPREADSHEET_VERSION; ?></strong>.</p>
    <p>There is currently no automatic upgrade path. To proceed, to create a new copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a>. Then copy all <code>Form Data</code> sheet data from the old document to the new one. Data on all other sheets will be generated automatically.</p>
    <p>If you are experiencing difficulties, <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system administrator.</p>
    </p>
  </div>
  <?php glFooter(); ?>
</div>