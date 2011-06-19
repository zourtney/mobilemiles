<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file displays the screen shown when the user is not authorized.
 *
 * The following variable is available:
 *   - $auth: the GlAuth instance
 */

include('header.php');
?>
<header id="top">
  <hgroup>
    <h1>Login</h1>
    <h2>Please log in using your Google account</h2>
  </hgroup>
</header>
<p>Before we can continue, you must <a href="<?php echo $auth->getRequestUrl(); ?>" title="Authorize your Google account">authorize</a> access to your Google account.
</p>

<?php
include('footer.php');
?>