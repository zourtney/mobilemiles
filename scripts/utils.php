<?php
/**
 * @copyright: Copyright 2011 randomland.net.
 * @license:   Apache 2.0; see `license.txt`
 * @author:    zourtney@randomland.net
 * 
 * This file contains utility functions used by the MobileMiles.
 */

/**
 * Returns a copy of the value passed in, formatted for MPG display.
 * NOTE: does not apend 'mpg'. This is done using CSS.
 */
function getMpg($value) {
  return round($value, 2);
}

/**
 * Returns a copy of the value passed in, formatted for distance display.
 * NOTE: does not append 'mi'. This is done using CSS.
 */
function getMiles($value) {
  return (int)$value;
}

/**
 * Returns a copy of the value passed in, formatted for monetary display.
 * NOTE: does not prepend '$'. This is done using CSS.
 */
function getMoney($value) {
  return sprintf("%01.2f", round($value, 2));
}

/**
 * Returns a copy of the value passed in, formatted for price-per-gallon 
 * display.
 * NOTE: does not prepend '$'. This is done using CSS.
 */
function getGasMoney($value) {
  return sprintf("%01.3f", round($value, 3));
}

/**
 * Returns a copy of the value passed in, formatted for percentage display.
 * NOTE: does not append '%'. This is done using CSS.
 */
function getPercent($value) {
  return sprintf("%01.2f", round($value, 2));
}