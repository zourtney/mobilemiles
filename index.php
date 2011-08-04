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
<html>
<head>
  <title>MobileMiles</title>
  
  <!-- Styles -->
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css" />
  <link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
  
  <!-- Fix at native screen resolution -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  
  <!-- Make this iOS webapp compatibile -->
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="apple-touch-icon-precomposed" href="<?php echo BASE_URL; ?>images/pump.png" />
  <link rel="apple-touch-startup-image" href="<?php echo BASE_URL; ?>images/splash.png">
  
  <!-- JavaScript dependencies -->
  <script src="http://code.jquery.com/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>const.php"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>utils.js"></script>
  <script type="text/javascript" src="<?php echo JS_URL; ?>app.js"></script>
  <script src="http://code.jquery.com/mobile/latest/jquery.mobile.js"></script>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
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
include(PAGE_BASE . 'settings.php');
include(PAGE_BASE . 'logout.php');
include(PAGE_BASE . 'list.php');
include(PAGE_BASE . 'create_instructions.php');
include(PAGE_BASE . 'view.php');
include(PAGE_BASE . 'details.php');
include(PAGE_BASE . 'new.php');

/*****************************************************************************
 * End of page
 *****************************************************************************/
?>
</body>
</html>