<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * GlAuth contains the Google Apps authentication needed for GlApp.
 */

class GlAuth {
  const SESSION_TOKEN = 'sessionToken';
  const GET_TOKEN = 'token';
  const NEXT = BASE_URL;//'http://gas.randomland.net';
  const SCOPE = 'http://spreadsheets.google.com/feeds https://spreadsheets.google.com/feeds http://docs.google.com/feeds';
  
  protected $client; /* Zend_Gdata_HttpClient */
  
  public function __construct() {
    
  }
  
  public function isLoggedIn() {
    global $_SESSION;
    return isset($_SESSION[GlAuth::SESSION_TOKEN]);
  }
  
  public function hasGetToken() {
    global $_GET;
    return isset($_GET[GlAuth::GET_TOKEN]);
  }
  
  public function getUrl() {
    $next = GlAuth::NEXT;
    $scope = GlAuth::SCOPE;
    $session = true;
    $secure = false;
    
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session);
  }
  
  public function login() {
    global $_SESSION, $_GET;
    $this->client = new Zend_Gdata_HttpClient();
    
    if (! $this->isLoggedIn() && $this->hasGetToken()) {
      $_SESSION[GlAuth::SESSION_TOKEN] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET[GlAuth::GET_TOKEN], $this->client);
    }
    
    $this->client->setAuthSubToken($_SESSION[GlAuth::SESSION_TOKEN]);
    return true;
  }
  
  public function createService() {
    return new Zend_Gdata_Spreadsheets($this->client);
  }
}