<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This simple application utilizes a Google Spreadsheet to keep track of gas
 * fill-up stats. There are two main functions right now:
 *   - printStats(): prints average mpg, cost, etc
 *   - printForm(): shows a form which allows the user to add a new entry.
 *                  Use this every time you fill up :-)
 */

/*****************************************************************************
 * Global constants and includes
 *****************************************************************************/
require_once 'scripts/globals.php';


/*****************************************************************************
 * Helper Functions
 *****************************************************************************/
/**
 * Prints out the HTML for the input form. Displays errors, if applicable
 */
function printForm($doc, $errors) {
  include('templates/new.php');
}

/**
 * Prints the message shown after a successful entry
 */
function printStats($doc, $message = null) {
  include('templates/stats.php');
}

/**
 * Prints out the appropriate error HTML when the document requested via GET
 * params fails to load.
 */
function printDocLoadFailed() {
  include('templates/docerror.php');
}

/**
 * Prints the list of available documents
 */
function printDocList($app, $docs) {
  include('templates/doclist.php');
}

/**
 * Prints the message telling you to log in using your Google account.
 */
function printUnauthorized($auth) {
  include('templates/unauthorized.php');
}

/**
 * Prints the message telling you the authorization failed. This shouldn't
 * actually happen.
 */
function printAuthFailed($auth) {
  include('templates/authfailed.php');
}

/**
 * Prints a message saying the application is too old for the given document.
 */
function printOldApp($min) {
  $message = '<p>The requested document requires a newer version (v' . $min . ') than the currently available v' . GlDoc::VERSION . '. Please check the requested spreadsheet has a "Version" worksheet with row "Spreadsheet version".</p>';
  
  include('templates/doclist.php');
}

/**
 * Prints a message saying the given document is too for the application.
 */
function printOldDoc($min) {
  $message = '<p>The document (v' . $min .') is too old for this application. You must use a document v' . GlDoc::VERSION . ' or greater. To fix this, get the <a href="' . GlDoc::MASTER_URL . ' title="Master Document">master document</a> and copy your data into it.</p>';
  
  include('templates/doclist.php');
}


/*****************************************************************************
 * Entry point (sorta)
 *****************************************************************************/
// Start the session
session_start();

// Respect mobile flag in GET parameters
if (isset($_GET['m'])) {
  if ($_GET['m'] === 'true') {
    $_SESSION['mobile'] = true;
  }
  else {
    $_SESSION['mobile'] = false;
  }
} 

// Create the authentication object.
$auth = new GlOAuth();

if (! $auth->isLoggedIn() && ! $auth->hasRequestToken()) {
  // Display link to login
  printUnauthorized($auth);
  
  // Save get parameters. We will reuse these upon redirect...
  $app = new GlApp($auth);
  $app->saveGetVars();
}
else if (! $auth->login()) {
  // Probably got here because you denied access from your Google account.
  printUnauthorized($auth);
}
else {
  // Create instance of the app
  $app = new GlApp($auth);
  
  // Merge in pre-login vars (if any)
  $app->mergeSavedGetVars();
  
  // Get the application mode (show, new, etc)
  $mode = $app->getMode();
  
  if ($mode == GlApp::MODE_DOCONLY ||
      $mode == GlApp::MODE_NEW || 
      $mode == GlApp::MODE_SUBMITNEW
     ) {
    // Try to open and display the document specified in the GET param
    if ($app->open()) {
      $doc = $app->getDoc();
      
      // Get version info from the 'version' worksheet. This is just a simple
      // test so we can (later) gently remind people to use the newer 
      // spreadsheet when I realize that I've messed up some calculations. :-P
      $minVersion = $doc->getVersionInfo();
      
      if ($minVersion['app'] == null || $minVersion['app'] > GlApp::VERSION) {
        // Display message saying this app is too old (shouldn't happen).
        printOldApp($minVersion['app']);
      }
      else if ($minVersion['doc'] == null || $minVersion['doc'] < GlDoc::VERSION) {
        // Display message saying the spreadsheet is out of date. This prevents
        // us from writing to old documents. Sure, it's rude to not be backwards
        // compatible, but...seriously, who else is going to be using this?
        printOldDoc($minVersion['doc']);
      }
      else if ($mode == GlApp::MODE_DOCONLY) {        
        // Shows the options available for the requested document
        printStats($doc);
      }
      else {
        // Get an associative array of errors.
        $errors = getFormErrors($mode);
        
        if ($mode == GlApp::MODE_NEW || count($errors) > 0) {
          // Display the form, with errors highlighted if necessary
          printForm($doc, $errors);
        }
        else {
          // Get the sanitized version of the $_POST vars.
          $cleanVals = sanitizeFormValues();
          
          // Insert them in to the spreadsheet.
          $doc->insert($cleanVals);
          
          // Print confirmation page
          $message = '<p>Your fill-up information has been successfully recorded. If you\'re interested, take a peek at your stats below. If not, just close this window and drive safely!</p>';
          printStats($doc, $message);
        }
      }
    }
    else {
      // Failed to display document specified in the GET param
      printDocLoadFailed();
    }
  }
  else {
    // No document specified in GET param. Show list of available docs
    $docs = $app->getAvailable();
    printDocList($app, $docs);
  }
}
