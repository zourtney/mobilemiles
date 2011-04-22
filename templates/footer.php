<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file creates the HTML page footer.
 */
?>
<footer>
  <div id="footer">
    <div id="copyright">
      <a href="<?php echo BASE_URL; ?>license.txt" title="Licensed under Apache 2.0">&copy;randomland.net</a>
    </div>
    <div id="version">
      <span id="version-box">
        <?php
        if (isset($_SESSION['mobile']) && $_SESSION['mobile'] == true) {
          // Mobile version
          ?>
          <span class="desktop"><a href="<?php echo BASE_URL;?>?m=false" title="Desktop version">Desktop</a></span>
          |
          <span class="mobile active">Mobile</span>
          <?php
        }
        else {
          // Desktop version
          ?>
          <span class="desktop active">Desktop</span>
          |
          <span class="mobile"><a href="<?php echo BASE_URL;?>?m=true" title="Mobile version">Mobile</a></span>
          <?php
        }
        ?>
      </span>
    </div>
  </div>
</footer>
</body>
</html>