'use strict';

/* App Module */

angular.module('clothesApp', []).
  config(['$routeProvider', function($routeProvider) {
  $routeProvider.
      when('/json', {templateUrl: 'partials/item-select.html',   controller: clothesCtrl}).
      when('/json/:itemId', {templateUrl: 'partials/item-detail.html', controller: itemDetailCtrl}).
      otherwise({redirectTo: '/json'});
}]);