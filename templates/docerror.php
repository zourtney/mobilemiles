<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the screen shown when a document fails to load.
 *
 * There are no parameters passed in. You can, however, get the document ID
 * using $_GET[GlApp::GET_ID].
 */

include('header.php');
?>
<p>
  <strong>Fatal error.</strong> Cannot open given document <em><?php echo $_GET[GlApp::GET_ID]; ?></em>
<p>
<p>PHP Error given:</p>
<pre>
  <?php 
  echo GlApp::getLastError();
  GlApp::clearLastError();
  ?>
</pre>

<?php
include('footer.php');
?>