myApp.factory('userModel', ['$http', '$cookies', function($http, $cookies){
	var userModel = {};
	userModel.doLogin = function(data){
		return	$http({  
				headers:{
					'Content-Type':'application/json'
				},
			    method: 'POST',  
			    url: baseUrl + '/auth',
			    data:data
			}).then(
				function successCallback(response) {
	      			$cookies.put('auth', JSON.stringify(response));
			    }, 
			    function errorCallback(response) {
			       	return 'Faild';
				}
			);
		};
	userModel.doLogout = function(){
		$cookies.remove('auth');
	}

	userModel.getAuthStatus = function(){
		var status = $cookies.get('auth');
		if(status){
			return true;
		}
		return false;
	}

	userModel.getUserObject = function(){
		var userObj = angular.fromJson($cookies.get('auth')).data;
		return userObj;
	}
	
	return userModel;
}])
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
//# sourceMappingURL=models.js.map
