myApp.factory('galleryModel', ['$http', 'userModel', function($http, userModel){
	return {
		saveGallery: function(galerryData){
			return $http({  
				headers:{
					'Content-Type':'application/json'
				},
				url: baseUrl + 'gallery',
				method: 'POST',
				data: {
					name: galerryData.name
				}
			});
		},
		getAllGalleries: function () {
			return $http({
                headers: {
                    'Content-Type': 'application/json'
                },
                url: baseUrl + 'gallery',
                method: "GET",
                data: {
                    galleryId: userModel.getUserObject().id
                }
            });
		},
	 	getGalleryById: function(id) {
            return $http.get(baseUrl + 'gallery/' + id);
        },
     	deleteSingleImage: function(data) {
            return $http({
                headers: {
                    'Content-Type': 'application/json'
                },
                url: baseUrl + 'delete-single-image',
                method: "POST",
                data: {
                    id: data.imageId,
                    galleryId: data.galleryId
                }
            });
        }
	};
}])