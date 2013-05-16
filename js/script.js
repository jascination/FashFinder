'use strict';

/* Controllers */

function clothesCtrl ($scope, $http) {
  $http.get('upThereAccessories.json').success(function(data) {
    $scope.clothes = data;
  });

//  $scope.orderProp = 'age';

}


//PhoneListCtrl.$inject = ['$scope', '$http'];

function itemDetailCtrl($scope, $routeParams, $http) {
  $http.get('json/' + $routeParams.itemId + '.json').success(function(data){
	$scope.item = data;
	});
	$scope.itemId = $routeParams.itemId;
	// Photo Album Controler

	$scope.init = function(){
		jQuery('.galleryScroll li:first-child, .galleryNav li:first-child').addClass('current');
	};
	
	// Scroll to appropriate position based on image index and width
	$scope.scrollTo = function(img,ind) {
		$scope.listposition = {left:(420 * ind * -1) + "px"};
		var oneInd = ind + 1;
		jQuery('.current').removeClass('current');
		jQuery('.galleryScroll li:nth-child(' + oneInd + '), .galleryNav li:nth-child(' + oneInd + ')').addClass('current');
	}



}



//PhoneDetailCtrl.$inject = ['$scope', '$routeParams'];

//Image Gallery Script


