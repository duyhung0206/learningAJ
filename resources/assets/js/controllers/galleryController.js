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