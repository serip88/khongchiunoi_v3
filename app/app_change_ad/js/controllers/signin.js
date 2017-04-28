'use strict';

var loginApi = {
    baseUrl: baseConfig.apiUrl,
    userLogin: 'login/login',
    userLogout: 'user/logout',
  };


/* Controllers */
  // signin controller
app.controller('SigninFormController', ['$scope', '$http', '$state', 'initData','commonService', function($scope, $http, $state ,initData , commonService) {
      $scope.user = {};
      $scope.authError = null;
      $scope.login = function() {
        $scope.authError = null;
        // Try to login
        commonService.httpPost(loginApi.userLogin, {email: $scope.user.email, password: $scope.user.password})
        .then(function(response) {
          if ( !response.status ) {
            $scope.authError = response.msg ? response.msg : 'Email or Password not is wrong!';
          }else{
            angular.copy(response.user_data, commonService.sync.user_data);    
            $state.go('app.dashboard');
          }
        }/*, function(x) {
          $scope.authError = 'Server Error';
        }*/);
      };
      
  }])
;