<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Home page for the MobileMiles webapp.
 */

/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once 'scripts/globals.php';

// Display the splash screen, authorization may take a second or so.
include(TEMPLATE_BASE . 'splash.php');
include(TEMPLATE_BASE . 'pageopen.php');
include(TEMPLATE_BASE . 'ui.php');

// Include all pages (start with 'home' so jQuery Mobile gives it preference)
include(PAGE_BASE . 'home.php');
include(PAGE_BASE . 'settings.php');
include(PAGE_BASE . 'list.php');
include(PAGE_BASE . 'view.php');
include(PAGE_BASE . 'new.php');

/*****************************************************************************
 * End of page
 *****************************************************************************/
include(TEMPLATE_BASE . 'pageclose.php');
