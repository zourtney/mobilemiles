<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file creates the opening HTML for the page.
 */
?>
<!DOCTYPE html>
<html>
<head>
  <title>Gas Log</title>
  
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css" />
  <link rel="shortcut icon" href="favicon.ico" />
      
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    
    <script src="http://code.jquery.com/jquery.min.js"></script>
    
    <script type="text/javascript" src="<?php echo JS_URL . 'utils.js'; ?>"></script>
    
    <link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
    <script src="http://code.jquery.com/mobile/latest/jquery.mobile.js"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
</head>
<body>
<?php
if (DEBUG) {
  // Put something obnoxious at the top of the page :-)
  //echo '<div class="debug-banner">Debug Mode -- localhost</div>';  
}
?>