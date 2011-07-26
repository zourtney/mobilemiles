<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlOAuth contains the Google Apps authentication needed for GlApp.
 */

require_once 'Zend/Oauth/Consumer.php';
require_once 'Zend/Gdata/Query.php';

/**
 * Wrapper class for Google's OAuth implementation. In particular, this helper
 * bundles the token endpoints and manages the Google-specific parameters such
 * as the hd and scope parameter.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Gdata_OAuth_Helper extends Zend_Oauth_Consumer {
  // Google's default oauth parameters/constants.
  private $_defaultOptions = array(
      'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
      'version' => '1.0',
      'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
      'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
      'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken'
  );

  /**
   * Create Gdata_OAuth_Helper object
   *
   * @param string $consumerKey OAuth consumer key (domain).
   * @param string $consumerSecret (optional) OAuth consumer secret. Required if
   *     using HMAC-SHA1 for a signature method.
   * @param string $sigMethod (optional) The oauth_signature method to use.
   *     Defaults to HMAC-SHA1. RSA-SHA1 is also supported.
   */
  public function __construct($consumerKey, $consumerSecret=null,
                              $sigMethod='HMAC-SHA1') {
    $this->_defaultOptions['consumerKey'] = $consumerKey;
    $this->_defaultOptions['consumerSecret'] = $consumerSecret;
    $this->_defaultOptions['signatureMethod'] = $sigMethod;
    parent::__construct($this->_defaultOptions);
  }

  /**
   * Getter for the oauth options array.
   *
   * @return array
   */
  public function getOauthOptions() {
    return $this->_defaultOptions;
  }

  /**
   * Fetches a request token.
   *
   * @param string $scope The API scope or scopes separated by spaces to
   *     restrict data access to.
   * @param mixed $callback The URL to redirect the user to after they have
   *     granted access on the approval page. Either a string or
   *     Zend_Gdata_Query object.
   * @return Zend_OAuth_Token_Request|null
   */
  public function fetchRequestToken($scope, $callback) {
    if ($callback instanceof Zend_Gdata_Query) {
        $uri = $callback->getQueryUrl();
    } else {
        $uri = $callback;
    }

    $this->_defaultOptions['callbackUrl'] = $uri;
    $this->_config->setCallbackUrl($uri);
    if (!isset($_SESSION['ACCESS_TOKEN'])) {
        return parent::getRequestToken(array('scope' => $scope));
    }
    return null;
  }

  /**
   * Redirects the user to the approval page
   *
   * @param string $domain (optional) The Google Apps domain to logged users in
   *     under or 'default' for Google Accounts. Leaving this parameter off
   *     will give users the universal login to choose an account to login
   *     under.
   * @return void
   */
  public function authorizeRequestToken($domain=null) {
    $params = array();
    if ($domain != null) {
      $params = array('hd' => $domain);
    }
    $this->redirect($params);
  }

  /**
   * Upgrades an authorized request token to an access token.
   *
   * @return Zend_OAuth_Token_Access||null
   */
  public function fetchAccessToken() {
    if (! isset($_SESSION['ACCESS_TOKEN'])) {
      if (! empty($_GET) && isset($_SESSION['REQUEST_TOKEN'])) {
        return parent::getAccessToken($_GET, unserialize($_SESSION['REQUEST_TOKEN']));
      }
    }
    return null;
  }
}


/**
 * OAuth implementation of the login interface.
 */
class GlOAuth extends Gdata_OAuth_Helper implements iGlAuth {
  const COOKIE_NAME = 'rl_glapp_access_token';
  const SCOPE = 'http://spreadsheets.google.com/feeds https://spreadsheets.google.com/feeds http://docs.google.com/feeds';
  
  protected $client; /* Zend_Gdata_HttpClient */
  
  public function __construct() {
    parent::__construct(OAUTH_CONSUMER_KEY, OAUTH_SECRET);
    
    if (isset($_COOKIE[GlOAuth::COOKIE_NAME])) {
      //echo 'cookie exists as ' . $_COOKIE[GlOAuth::COOKIE_NAME];
      $_SESSION['ACCESS_TOKEN'] = $_COOKIE[GlOAuth::COOKIE_NAME];
    }
  }
  
  public function isLoggedIn() {
    return isset($_SESSION['ACCESS_TOKEN']);
  }
  
  public function hasRequestToken() {
    return isset($_GET['action']) && (
      $_GET['action'] == 'request_token' || $_GET['action'] == 'access_token'
    );
  }
  
  function hasLogoutToken() {
    return isset($_GET['action']) && $_GET['action'] == 'logout';
  }
  
  public function getRequestUrl() {
    return LOGIN_URL . '?action=request_token';
  }
  
  public function getLogoutUrl() {
    return LOGIN_URL . '?action=logout';
  }
  
  public function getRequestToken() {
    $_SESSION['REQUEST_TOKEN'] = serialize($this->fetchRequestToken(GlOAuth::SCOPE, LOGIN_URL . '?action=access_token'));
    $this->authorizeRequestToken();
  }
  
  public function getAccessToken($nextUrl) {
    $_SESSION['ACCESS_TOKEN'] = serialize($this->fetchAccessToken());
    header('Location: ' . $nextUrl);
  }
  
  public function getExistingAccessToken() {
    $accessToken = unserialize($_SESSION['ACCESS_TOKEN']);
    $this->client = $accessToken->getHttpClient($this->getOauthOptions());
    
    if (! isset($_COOKIE[GlOAuth::COOKIE_NAME])) {
      setcookie(GlOAuth::COOKIE_NAME, $_SESSION['ACCESS_TOKEN'], time() + OAUTH_COOKIE_EXPIRATION);
    }
  }
  
  public function logIn($nextUrl) {
    try {
      switch (@$_REQUEST['action']) {
        case 'request_token':
          $this->getRequestToken();
          //$_SESSION['REQUEST_TOKEN'] = serialize($this->fetchRequestToken(GlOAuth::SCOPE, LOGIN_URL . '?action=access_token'));
          //$this->authorizeRequestToken();
          //NOTE: redirects, so no return value
          break;
        case 'access_token':
          $this->getAccessToken($nextUrl);
          //$_SESSION['ACCESS_TOKEN'] = serialize($this->fetchAccessToken());
          //header('Location: ' . $nextUrl);
          //break;
          //return true;
        default:
          //$accessToken = unserialize($_SESSION['ACCESS_TOKEN']);
          //$this->client = $accessToken->getHttpClient($this->getOauthOptions());

          //setcookie(GlOAuth::COOKIE_NAME, $_SESSION['ACCESS_TOKEN'], time() + OAUTH_COOKIE_EXPIRATION);
          $this->getExistingAccessToken();

          return true;
      }
    }
    catch (Exception $ex) {
      //TODO: log?
    }
    
    return false;
  }
  
  public function logOut() {
    //TODO: fix! This does not work at all!
    /*$_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time()-42000, '/');
      unset($_COOKIE[session_name()]);
    }
    
    setcookie(GlOAuth::COOKIE_NAME, time() - 3600);
    unset($_COOKIE[GlOAuth::COOKIE_NAME]);
    
    session_destroy();
    */
    //session_start(); // initialize session
    session_destroy(); // destroy session
    
    $past = time() - 3600;
    setcookie(GlOAuth::COOKIE_NAME, null, $past);
    setcookie('PHPSESSID', '', $past, '/'); // delete session cookie 
  }
  
  public function createService() {
    return new Zend_Gdata_Spreadsheets($this->client);
  }
}