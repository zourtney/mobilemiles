<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This JavaScript file defines constants used by the MobileMiles webapp.
 *
 * TODO: merge into single namespace
 */
 
require_once '../scripts/globals.php';
header('Content-type: text/javascript');
?>

var MobileMilesConst = {
  BASE_URL : '<?php echo BASE_URL; ?>',
  SCRIPT_URL : '<?php echo SCRIPT_URL; ?>'
};