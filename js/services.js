'use strict';

/* Services */

angular.module('clothesCtrlServices', ['ngResource']).
    factory('Item', function($resource){
  return $resource('json/:itemId.json', {}, {
    query: {method:'GET', params:{itemId:'clothes'}, isArray:true}
  });
});
