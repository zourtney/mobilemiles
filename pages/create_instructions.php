<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file instructions on how to create a new gas log spreadsheet.
 */
?>
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
