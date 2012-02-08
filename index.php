<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Entry point for the MobileMiles webapp.
 */

/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once 'scripts/globals.php';
require_once TEMPLATE_BASE . 'ui.php';

/*****************************************************************************
 * Start of page
 *****************************************************************************/
?>
<!DOCTYPE html>
<html<?php if (! DEBUG) echo ' manifest="cache.manifest"'; ?>>
<head>
  <title>MobileMiles</title>
  
  <!-- Styles -->
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/mobilemiles.css" type="text/css" />
  <?php if (MIN_RELEASE) { ?>
  <link rel="stylesheet" href="<?php BASE_URL; ?>css/jquery.mobile.min.css" type="text/css" />
  <?php } else { ?>
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" type="text/css" />
  <?php } ?>
  
  <!-- Fix at native screen resolution -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  
  <!-- Make this iOS webapp compatibile -->
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>images/pump.png" />
  <link rel="apple-touch-startup-image" href="<?php echo BASE_URL; ?>images/splash.png" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black" />
  
  <!-- JavaScript dependencies -->
  <script type="text/javascript">
    var MobileMilesConst = {
      BASE_URL : '<?php echo BASE_URL; ?>',
      SCRIPT_URL : '<?php echo SCRIPT_URL; ?>'
    };
  </script>
  <?php if (MIN_RELEASE) { ?>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery-latest.min.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>mobilemiles.min.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery.mobile.min.js"></script>
  <?php } else { ?>
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery.livequery/jquery.livequery.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery.timeago/jquery.timeago.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery.store/json.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>jquery.store/jquery.store.js"></script>
  <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>utils.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>app.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js"></script>
  <?php } ?>
  
  <script type="text/javascript">
  	// Make all timestamps dynamically update via $.timeago. Also make it so
  	// click/tapping them toggles the full date and the friendly date.
    $('abbr.timeago').livequery(function() {
    	var $this = $(this);
    	
    	// Add friendly date/time formatting to this element
    	$this.timeago($.timeago.settingsRelativeDay);
      
      // Toggle friendly name and actual value on click/tap
      $this.bind('click', function(event) {
      	var title = $this.attr('title');
      	$this.attr('title', $this.text());
      	$this.text(title);
      });
    });
    
    // Make the subtitle document name clickable, sending you to the document
    // list page.
    $('div[data-role="navbar"].subtitle').live('click', function() {
    	$.mobile.changePage('#list');
    });
  </script>
</head>
<body>
<?php
/*****************************************************************************
 * Include all child pages.
 *
 * Start with including `home.php` because jQuery Mobile makes the first-
 * occuring `<div data-role="page">` the application's default page.
 *****************************************************************************/
include(PAGE_BASE . 'home.php');
include(PAGE_BASE . 'help.php');
include(PAGE_BASE . 'settings.php');
include(PAGE_BASE . 'logout.php');
include(PAGE_BASE . 'list.php');
include(PAGE_BASE . 'view.php');
include(PAGE_BASE . 'details.php');
include(PAGE_BASE . 'new.php');

/*****************************************************************************
 * End of page
 *****************************************************************************/
?>
</body>
</html>