<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * Cookie manager class for accessing cached lists (doc, details, etc)
 */

class GlCookie {
	const LIST_CACHE = 'mobilemiles_list_cache';
	const ENTRY_CACHE = 'mobilemiles_entry_cache';
	const EXPIRE = 2419200; // one month
	
	protected static function getCookieProperty($cookieName, $name) {
		$cookie = @$_COOKIE[$cookieName];
		
		if (isset($cookie)) {
			// Parse out and return doc list
			$obj = json_decode($cookie);
			
			if (isset($obj->{$name})) {
				return $obj->{$name};
			}
			// else: unset return value
		}
		// else: unset return value
	}
	
	protected static function setCookieProperty($cookieName, $name, $val) {
		$cookie = @$_COOKIE[$cookieName];
		
		if (isset($cookie)) {
			// Get cookie object
			$obj = json_decode($cookie);
		}
		
		// Set object property
		$obj->{$name} = $val;
		
		// Save to cookie
		setcookie($cookieName, json_encode($obj), time() + self::EXPIRE);
	}
	
	public static function deleteCookie() {
		setcookie(self::LIST_CACHE, '', time() - 3600);
	}
	
	public static function getDocList() {
		return self::getCookieProperty(self::LIST_CACHE, 'doclist');
	}
	
	public static function setDocList($val) {
		self::setCookieProperty(self::LIST_CACHE, 'doclist', $val);
	}
	
	public static function getDocId() {
		return self::getCookieProperty(self::LIST_CACHE, 'docid');
	}
	
	public static function setDocId($val) {
		self::setCookieProperty(self::LIST_CACHE, 'docid', $val);
	}
	
	public static function getDocEntries() {
		return self::getCookieProperty(self::ENTRY_CACHE, 'docentries');
	}
	
	public static function setDocEntries($val) {
		self::setCookieProperty(self::ENTRY_CACHE, 'docentries', $val);
	}
}