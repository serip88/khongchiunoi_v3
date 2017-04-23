'use strict';

var loginApi = {
    baseUrl: baseConfig.apiUrl,
    userLogin: 'login/login',
    userLogout: 'user/logout',
  };


/* Controllers */
  // signin controller
app.controller('SigninFormController', ['$scope', '$http', '$state', 'loginService', 'commonService', function($scope, $http, $state, loginService, commonService) {
      $scope.user = {};
      $scope.authError = null;
      $scope.login = function() {
        $scope.authError = null;
        // Try to login
        loginService.httpPost(loginApi.userLogin, {email: $scope.user.email, password: $scope.user.password})
        .then(function(response) {
          if ( !response.status ) {
            $scope.authError = response.msg ? response.msg : 'Email or Password not is wrong!';
          }else{          
            $state.go('app.dashboard');
          }
        }/*, function(x) {
          $scope.authError = 'Server Error';
        }*/);
      };
      
  }])
;