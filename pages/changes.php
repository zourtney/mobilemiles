<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This page displays important recent changes.
 *
 * TODO: load version/change data dynamically.
 */
?>
<!-- *********************************************************************** -->
<!-- Home page templates                                                     -->
<!-- *********************************************************************** -->

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