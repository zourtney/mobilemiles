/**
 * jQuery plugin to set cursor position or select a range in a text box.
 *   http://stackoverflow.com/questions/499126/841121#841121
 */
$.fn.selectRange = function(start, end) {
  return this.each(function() {
    if (this.setSelectionRange) {
      this.focus();
      this.setSelectionRange(start, end);
    } else if (this.createTextRange) {
      var range = this.createTextRange();
      range.collapse(true);
      range.moveEnd('character', end);
      range.moveStart('character', start);
      range.select();
    }
  });
};


/**
 * Returns a 2-character string of the number passed in. This should be used to
 * prepend 0's on dates less than 10.
 */
function make2Digit(val) {
  if (val < 10)
    return '0' + val;
  return val;
}

/**
 * Returns a formatted string of the current date/time.
 */
function getCurrentTimeString() {
  var now = new Date();
  return now.getFullYear() + '-' + 
    make2Digit(now.getMonth() + 1) + '-' + 
    make2Digit(now.getDate()) + 'T' +
    make2Digit(now.getHours()) + ':' + 
    make2Digit(now.getMinutes()) + ':' + 
    make2Digit(now.getSeconds()) + '.000'
  ;
}

/**
 * Returns a currency-formatted copy of the number passed in.
 */
function getMoney(val) {
  return val.toFixed(2);
}

/**
 * Allows you to get $_GET variables from JavaScript
 */
var $_GET = {};
document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
  function decode(s) {
      return decodeURIComponent(s.split("+").join(" "));
  }

  $_GET[decode(arguments[1])] = decode(arguments[2]);
});