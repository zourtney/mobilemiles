function make2Digit(val) {
  if (val < 10)
    return '0' + val;
  return val;
}

function getCurrentTimeString() {
  var now = new Date();
  return now.getFullYear() + '-' + 
    make2Digit(now.getMonth() + 1) + '-' + 
    make2Digit(now.getDate()) + ' ' +
    make2Digit(now.getHours()) + ':' + 
    make2Digit(now.getMinutes()) + ':' + 
    make2Digit(now.getSeconds())
  ;
}

function getMoney(val) {
  return val.toFixed(2);
}