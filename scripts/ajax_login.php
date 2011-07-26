<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Returns the authorization state.
 */

require_once 'globals.php';

//HACK: remove this or restructure program so we're not opening the session
// more than once.
if (! session_id()) {
  session_start();
}

// Create or get handle to the authentication object.
if (! isset($_SESSION['GlApp_GlOAuth'])) {
  $auth = new GlOAuth();
  $_SESSION['GlApp_GlOAuth'] = $auth;
}
else {
  $auth = $_SESSION['GlApp_GlOAuth'];
}

// Save the next URL
if (! isset($_SESSION['next'])) {
  if (isset($_GET['next'])) {
    $_SESSION['next'] = $_GET['next'];
  }
  else {
    $_SESSION['next'] = BASE_URL;//'http://google.com';
  } 
}


if ($auth->hasLogoutToken()) {
  // Keep copy of this, since the session is going to be destroyed. We don't
  // actually need to be keeping in in $_SESSION, but the login process does, so
  // we might as well keep it consistent.
  //$next = $_SESSION['next'];
  
  // Log out
  $auth->logOut();
  
  // Go to next page.
  //header('Location: ' . urldecode($next));
  echo json_encode(array(
    'response' => 'logout_success',
    'url' => $auth->getRequestUrl()
  ));
}
else if (! $auth->isLoggedIn() && ! $auth->hasRequestToken()) {
  // Not authorized. Return response with the request URL.
  echo json_encode(array(
    'response' => 'login_unauthorized',
    'url' => $auth->getRequestUrl()
  ));
}
else {
  // Somewhere in the authentication process...look for current mode and respond
  // accordingly.
  //
  // NOTE: this is the same process as what occurs in GlOAuth::logIn(), except
  //       I added the part where the 'next' variable is unset/used.
  switch (@$_REQUEST['action']) {
    case 'request_token':
      $auth->getRequestToken();
      // Automatically redirects
      break;
    case 'access_token':
      $auth->getAccessToken($_SESSION['next']);
      // Automatically redirects
      break;
  }
  
  // Default case: it looks like we already have an access token. Get it!
  unset($_SESSION['next']);
  $auth->getExistingAccessToken();

  // Return 'logged in'
  echo json_encode(array(
    'response' => 'login_succeeded',
    'url' => $auth->getLogoutUrl()
  ));
}