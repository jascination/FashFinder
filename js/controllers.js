'use strict';

/* Controllers */

function clothesCtrl($scope, Item) {
  $scope.clothes = Item.query();
}

//PhoneListCtrl.$inject = ['$scope', 'Phone'];



function clothesCtrl($scope, $routeParams, Item) {
  $scope.item = Item.get({itemId: $routeParams.itemId}, function(item) {
    $scope.mainImageUrl = item.mainImage;
  });

  $scope.setImage = function(imageUrl) {
    $scope.mainImageUrl = imageUrl;
  }
}

//PhoneDetailCtrl.$inject = ['$scope', '$routeParams', 'Phone'];


function itemDetailCtrl($scope, $routeParams) {
  $scope.itemId = $routeParams.itemId;
}

//PhoneDetailCtrl.$inject = ['$scope', '$routeParams'];