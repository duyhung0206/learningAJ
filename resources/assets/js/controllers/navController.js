myApp.controller('navController', ['$scope', '$location', 'userModel', function($scope, $location, userModel){
	angular.extend($scope, {
		user: userModel.getUserObject(),
	 	navUrl: [{
            link: 'Home',
            url: '/dashboard',
            subMenu: [{
                link: 'View Gallery',
                url: '/gallery/view'
            }, {
                link: 'Add Gallery',
                url: '/gallery/add'
            }]
        }, {
            link: 'View Gallery',
            url: '/gallery/view'
        }, {
            link: 'Add Gallery',
            url: '/gallery/add'
        }]
	});

	angular.extend($scope, {
		doLogout: function(){
			userModel.doLogout();
			$location.path('/');
		},
        checkActiveLink: function(routeLink){
            if($location.path() == routeLink){
                return 'make-active';
            }
        }
	});

}]);