'use strict';

/* App Module */

angular.module('clothesApp', []).
  config(['$routeProvider', function($routeProvider) {
  $routeProvider.
      when('/', {
			templateUrl: 'partials/item-select.html',
			controller: clothesCtrl
			}).
      when('/json/:storeId/:itemId', {
			templateUrl: 'partials/item-detail.html', 
			controller: itemDetailCtrl,

			}).
      otherwise({redirectTo: '/'});
}]);

