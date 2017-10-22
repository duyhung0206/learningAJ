myApp.controller('userController', ['$scope', '$location', 'userModel', function($scope, $location, userModel){
	angular.extend($scope, {
		login:{
			username: 'hades@trueplus.vn',
			password: '123'
		}
	})
	angular.extend($scope,{
		doLogin: function(loginForm){
			var data = {	
					email: $scope.login.username,
					password: $scope.login.password
				};
			userModel.doLogin(data).then(
				function() {
	      			$location.path('/dashboard');
			    }
			);
		},
		logout: function(){
			userModel.doLogout();
			$location.path('/');
		}
	});
}]);
myApp.controller('globalController', ['$scope', function($scope){
	$scope.global = {};
	$scope.global.navUrl = 'templates/partials/nav.html';
}]);
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
myApp.controller('galleryController', ['$scope', '$location', 'galleryModel', '$timeout', '$routeParams', 'Lightbox', 'data',
	function($scope, $location, galleryModel, $timeout, $routeParams, Lightbox, data){
	
	/*Getting all the galleries*/
    if (data && data.galleries != undefined) {
        data.galleries.then(function (response) {
			$timeout(function(){
				$scope.galleries = response.data;
				$scope.showGallery = true;
			}, 1000);
		});
    }

    /*Fetch a single gallery on the view gallery page*/
    if (data && data.singleGallery != undefined) {
        data.singleGallery.then(function(response) {
			$scope.singleGallery = response.data;
	    });
    }

	$scope.$on('imageAdded', function(event, args){
		$scope.singleGallery = args;
		$scope.$apply();
	});

	/*variables*/
	angular.extend($scope, {
		newGallery: {},
		errorDiv: false,
		errorMessage: [],
		singleGallery: {},
		dropzoneConfig:{
			'options': { // passed into the Dropzone constructor
      			'url': baseUrl + 'upload-image'
		    },
		    'eventHandlers': {
		      	'sending': function (file, xhr, formData) {
		      		formData.append('_token', csrfToken);
		      		formData.append('galleryId', $routeParams.id);
		      	},
		      	'success': function (file, response) {
		      		$scope.singleGallery.images.push(response);
		      		$scope.$emit('imageAdded', $scope.singleGallery);
		      	}
		    }
		}
	});

	/*functions*/
	angular.extend($scope, {
		saveNewGallery: function (addGalleryForm) {
			if(addGalleryForm.$valid){
				$scope.formSubmitted = false;
				galleryModel.saveGallery($scope.newGallery).then(
				function() {
	      			$location.path('gallery/view');
			    });
			}else{
				$scope.formSubmitted = true;
			}
		},

		viewGallery: function (id) {
			$location.path('gallery/view/'+ id);
		},
		openLightboxModal: function (index) {
		    Lightbox.openModal($scope.singleGallery.images, index);
	  	},
  	 	deleteImage: function(imageId) {
            var data = {
                imageId: imageId,
                galleryId: $routeParams.id
            };
            galleryModel.deleteSingleImage(data).then(function(response) {
                $scope.singleGallery = response.data;
            });
        }	
	});
}])
//# sourceMappingURL=controllers.js.map
