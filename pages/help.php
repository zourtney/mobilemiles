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
    'title' => 'Changes'
  )); ?>
  <div data-role="content">
    <p>On 2011-08-08, the document filter text changed from <code>(rlgaslog)</code> to <code><?php echo FILTER_TEXT; ?></code>. Spreadsheets containing the old filter text will <em>not</em> show up in your MobileMiles Document List. To edit the document title(s), visit your <a href="http://docs.google.com">Google Docs</a> page.</p>
  </div>
  <?php glFooter() ?>
</div>

<!-- *********************************************************************** -->
<!-- Create instructions page content                                        -->
<!-- *********************************************************************** -->
<div id="create_instructions" data-role="page">
  <?php glHeader(array(
    'title' => 'Create New'
  )); ?>
  <div data-role="content">
    <p>To create a new gas log, make a copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a> via <code>File -> Make a copy..</code>. Save it with the extension <code><?php echo FILTER_TEXT; ?></code> so it shows up in your gas log list.
    </p>
  </div>
  <?php glFooter(); ?>
</div>

<!-- *********************************************************************** -->
<!-- Document update instructions page content                               -->
<!-- *********************************************************************** -->
<div id="doc_update_instructions" data-role="page">
  <?php glHeader(array(
    'title' => 'Document Upgrade'
  )); ?>
  <div data-role="content">
    <p>The requested document is <strong>out of date</strong>. This happens when there is a significant change to the spreadsheet and/or MobileMiles application. Minimum application version is <strong><?php echo APP_VERSION; ?></strong>; minimum spreadsheet version is <strong><?php echo SPREADSHEET_VERSION; ?></strong>.</p>
    <p>There is currently no automatic upgrade path. To proceed, to create a new copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a>. Then copy all <code>Form Data</code> sheet data from the old document to the new one. Data on all other sheets will be generated automatically.</p>
    <p>If you are experiencing difficulties, <a href="<?php echo SYSTEM_ADMIN_URI; ?>" rel="external">contact</a> the system administrator.</p>
    </p>
  </div>
  <?php glFooter(); ?>
</div>