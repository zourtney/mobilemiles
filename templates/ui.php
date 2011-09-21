<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Various JavaScript-defined UI components.
 */

function getDisplayValue($v, $str) {
	return (isset($v[$str])) && ($v[$str] === true || strlen($v[$str] > 0));
}

function glHeader($v) {
  //$backHistory = true;
  //$backUrl = '';
  //$backTitle = 'Back';
  $closeDisplay = false;
  $backDisplay = true;
  $settingsDisplay = false;
  $homeDisplay = true;
  
  //TODO: uncomplicate logic!
  if (isset($v['close'])&& ($v['close'] === true || strlen($v['close']) > 0)) {
  	$closeDisplay = true;
  }
  
  if (isset($v['back']) && ($v['back'] === false || strlen($v['back']) < 1)) {
    $backDisplay = false;
  }
  
  if (isset($v['settings']) && ($v['settings'] === true || strlen($v['settings']) > 0)) {
  	$settingsDisplay = true;
  }
  
  if (isset($v['home']) && ($v['home'] === false || strlen($v['home']) < 1)) {
    $homeDisplay = false;
  }
  ?>
  <!-- Start of header -->
  <div data-role="header">
  	<?php if ($closeDisplay) { ?>
  		<a class="close ui-btn-close" data-rel="back">Close</a>
  	<?php } ?>
  	
  	<?php if ($backDisplay) {	?>
  		<a class="back ui-btn-back" data-rel="back" data-icon="arrow-l">Back</a>
  	<?php } ?>
    
    <h1 id="pageTitle"><?php echo $v['title']; ?></h1>
    
    <?php if ($settingsDisplay) { ?>
	    <a class="settings ui-btn-right" data-icon="gear" data-iconpos="notext" data-inline="true" data-transition="slideup" href="<?php echo BASE_URL; ?>#settings">Settings</a>
	  <?php } ?>
	  
	  <?php if ($homeDisplay) { ?>
	  	<a class="home ui-btn-right" data-icon="home" data-iconpos="notext" data-inline="true" data-transition="reverse slide" href="<?php echo BASE_URL; ?>">Home</a>
	  <?php } ?>
    
    <!-- Subtitle -->
    <div data-role="navbar" class="subtitle">
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