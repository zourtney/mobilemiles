<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file creates the HTML page header.
 */
?>
<!DOCTYPE html>
<html>
<head>
  <title>Gas Log</title>
  
  <link rel="stylesheet" href="css/style.css" type="text/css" />
  <link rel="shortcut icon" href="favicon.ico" />
  <?php
    // Add mobile stylesheet. It will override a lot of what's in style.css.
    if (isset($_SESSION['mobile']) && $_SESSION['mobile'] == true) {
    ?>
      <link rel="stylesheet" href="css/mobile.css" type="text/css" />
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php 
    }
  ?>
  
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
  <script type="text/javascript" src="<?PHP echo BASE_URL; ?>js/utils.js"></script>
</head>
<body>
<?php
if (DEBUG) {
  // Put something obnoxious at the top of the page :-)
  echo '<div class="debug-banner">Debug Mode -- localhost</div>';  
}
?>