'use strict';

/* Controllers */

function clothesCtrl ($scope, $http) {
  $http.get('catJSON/Incu Men/clothes.json').success(function(data) {
    $scope.clothes = data;
  });

//  $scope.orderProp = 'age';

}


//PhoneListCtrl.$inject = ['$scope', '$http'];

function itemDetailCtrl($scope, $routeParams, $http, $timeout) {
  $http.get('json/' + $routeParams.storeId + '/' + $routeParams.itemId + '.json').success(function(data){
	$scope.item = data;
	});
	$scope.itemId = $routeParams.itemId;
	$scope.storeId = $routeParams.storeId;

	$timeout(function(){
		window.mySwipe = Swipe(document.getElementById('slider'), {
			callback: function(index, elem) {
				var clicked = document.getElementsByClassName("dot")[index];
				var current = document.getElementsByClassName("dot current")[0];
				current.className = current.className.replace(/\bcurrent\b/,'');
				clicked.className = clicked.className + " current";
			}
		});
		var firstDot = document.getElementsByClassName('firstDot')[0];
		firstDot.className = firstDot.className + " current";
	}, 1000);

	// Scroll to appropriate position based on image index and width
	$scope.scrollTo = function(index) {
		mySwipe.slide(index);
		var clicked = document.getElementsByClassName("dot")[index];
		var current = document.getElementsByClassName("dot current")[0];
		current.className = current.className.replace(/\bcurrent\b/,'');
		clicked.className = clicked.className + " current";
	} 




}



//PhoneDetailCtrl.$inject = ['$scope', '$routeParams'];

//Image Gallery Script


