<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays a list of available gas logs.
 *
 * The following variables are available to you:
 *   - $app: the GlApp instance
 *   - $docs: array of GlDoc objects holding valid gas logs
 */

include('header.php');
?>
<header id="top">
  <hgroup>
    <h1>Gas Logs</h1>
    <?php /*<h2>Create New or Select Existing</h2>*/ ?>
    <h2>Select an existing document</h2>
  </hgroup>
</header>
<?php
  if (isset($message)) {
  ?>
    <div class="message">
      <p><?php echo $message; ?></p>
    </div>
  <?php
  }
?>
<article>
  <p>Select from the list of documents below. If you wish to add a new gas log, make a copy of the <a href="<?php echo GlDoc::MASTER_URL; ?>" title="Master document" target="_blank">master document</a> via <code>File -> Make a copy..</code>. Save it with the extension <code><?php echo FILTER_TEXT; ?></code> so it shows up in the list below.
  </p>
  <ul>
<?php
  foreach ($docs as $doc) {
    echo '<li><a href="' . $doc->url() . '">' . $doc->title() . '</a></li>';
  }
?>
  </ul>
</article>

<?php
include('footer.php');
?>