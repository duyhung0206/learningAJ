myApp.factory('errorInterceptor', ['$location', '$rootScope', '$cookies' , '$window', 
    function ($location, $rootScope, $cookies, $window) {
        return {
            responseError: function (response) {
                if (response && response.status === 501) {
                    if($location.url() != '/'){
                        $cookies.remove('auth');
                        console.log(response);
                        $window.location.href = baseUrl;
                    }
                }

            }
        };
}]);

myApp.config(function ($httpProvider) {
    $httpProvider.interceptors.push('errorInterceptor');    
});