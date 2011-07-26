<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file contains interface definitions usend throughouh GlApp.
 */

/**
 * Authentication interface
 */
interface iGlAuth {
  public function isLoggedIn();
  public function hasRequestToken();
  public function hasLogoutToken();
  public function getRequestUrl();
  public function getLogoutUrl();
  public function logIn($nextUrl);
  public function logOut();
  public function createService();
}