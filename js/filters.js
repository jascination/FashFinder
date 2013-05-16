'use strict';

/* Filters */

angular.module('clothesCtrlFilters', []).filter('checkmark', function() {
  return function(input) {
    return input ? '\u2713' : '\u2718';
  };
});
