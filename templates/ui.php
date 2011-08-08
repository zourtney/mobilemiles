<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Various JavaScript-defined UI components.
 */

function glHeader($v) {
  $backHistory = true;
  $backUrl = '';
  $backTitle = 'Back';
  $backDisplay = true;
  
  //TODO: uncomplicate logic!
  if (isset($v['back'])) {
    if (is_array($v['back'])) {
      if (isset($v['back']['url']) && strlen($v['back']['url']) > 0) {
        if ($v['back']['url'] == 'history') {
          $backUrl = '';
          $backHistory = true;
        }
        else {
          $backUrl = $v['back']['url'];
          $backHistory = false;
        }
      }
      
      if (isset($v['back']['title'])) {
        $backTitle = $v['back']['title'];
      }
    }
    else if ($v['back'] === false || strlen($v['back']) === 0) {
      $backDisplay = false;
    }
    else if ($v['back'] == 'history') {
      $backHistory = true;
    }
    else {
      $backUrl = $v['back'];
      $backHistory = false;
    }
  }
  ?>
  <!-- Start of header -->
  <div data-role="header">
    <a class="back ui-btn-back" <?php
      if (! $backDisplay) {
        echo 'style="display: none;"';
      }
      else if ($backHistory) {
        echo 'data-rel="back"';
      }
      else if (strlen($backUrl) > 0) {
        echo 'href="'. $backUrl . '"';
      }
      ?> data-icon="arrow-l"><?php echo $backTitle; ?></a>
    
    <h1 id="pageTitle"><?php echo $v['title']; ?></h1>
    
    <a class="settings ui-btn-right" <?php
    if (isset($v['settings']) && ($v['settings'] === false || strlen($v['settings']) < 1)) {
      echo 'style="display: none;"';
    } ?> data-icon="gear" class="ui-btn-right" data-iconpos="right" data-inline="true" href="<?php echo BASE_URL; ?>#settings">Settings</a>
    <div data-role="navbar" class="subtitle" data-theme="d">
      <ul>
        <li class="subtitle"></li>
      </ul>
    </div>
  </div>
  <?php
    if (DEBUG) {
    ?>
      <div id="debug-banner">
        <div class="content">
          Development version
        </div>
      </div>
    <?php
    }
  ?>
  <!-- End of header -->
<?php
}

function glFooter() {
?>
  <!-- Start of footer -->
  <div id="footer">
    <div data-role="footer">
      <div class="content">
      <a href="http://github.com/zourtney/mobilemiles" rel="external" target="_blank">mobilemiles</a> by zourtney
      </div>
    </div>
  </div>
  <!-- End of footer -->
<?php
}