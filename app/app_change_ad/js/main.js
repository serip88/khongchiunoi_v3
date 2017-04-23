'use strict';

/* Controllers */

app.factory("loginService", ["$http", "$q", "$state", function ($http, $q, $state) {
    var userObject = {};    
    userObject.syn = {user_data:{},is_requested:0};
    userObject.httpGet = function (path, params, block) {
        if(typeof block == 'undefined'){
            block = true;
        }
        var deferred = $q.defer();
        $http.get([baseConfig.apiUrl, path].join('/'), {block: block, params: params})
            .success(function (data) {
                deferred.resolve(data);
            }).error(function (data) {
                deferred.resolve(data);
            });
        return deferred.promise;
    }

    userObject.httpPost = function (path, params, block) {
        var deferred = $q.defer();
        if(typeof block == 'undefined'){
            block = httpBlockConfig;
        }
        $http.post([baseConfig.apiUrl, path].join('/'), params, block)
            .success(function (data) {
                deferred.resolve(data);
                if(typeof(data.user_data)){
                  angular.copy(data.user_data, userObject.syn.user_data);
                }
                
            }).error(function (data) {
                deferred.resolve(data);
            });
        return deferred.promise;
    }

    return userObject;
  }])
  .controller('AppCtrl', ['$scope', '$rootScope', '$translate', '$localStorage', '$window', '$state', 'initData', 'loginService', 'commonService' ,
    function($scope, $rootScope,  $translate,   $localStorage, $window, $state, initData, loginService, commonService ) {
      init();
      // add 'ie' classes to html
      var isIE = !!navigator.userAgent.match(/MSIE/i);
      isIE && angular.element($window.document.body).addClass('ie');
      isSmartDevice( $window ) && angular.element($window.document.body).addClass('smart');

      // config
      $scope.app = {
        name: '',//Your Site Name
        version: '1.3.3',
        // for chart colors
        color: {
          primary: '#7266ba',
          info:    '#23b7e5',
          success: '#27c24c',
          warning: '#fad733',
          danger:  '#f05050',
          light:   '#e8eff0',
          dark:    '#3a3f51',
          black:   '#1c2b36'
        },
        settings: {
          themeID: 1,
          navbarHeaderColor: 'bg-black',
          navbarCollapseColor: 'bg-white-only',
          asideColor: 'bg-black',
          headerFixed: true,
          asideFixed: false,
          asideFolded: false,
          asideDock: false,
          container: false,
          appUrl: baseConfig.app,
          adminTpl: baseConfig.adminTpl,
        },
        user_data: initData.user_data,
        api:{
          main:{base_info:'main/base_info'},
        }
      }
      //get site info
      commonService.httpGet($scope.app.api.main.base_info)
      .then(function(response) {
          if (response.status) {
            $scope.app.name = response.data.site_name;
            $scope.app.version = response.data.site_version;
          }
      });
      function init(){
        if(!initData.data.status){
          $state.go('access.signin');
        }else{
          if($state.current.name == 'access.signin'){
            $state.go('app.dashboard');
          }
        }
      }
      /*function getUserInfor(init){
        loginService.httpGet('user/user_ss').then(function(response) {
            if(init==1){
                stateChange();
            }
            if (response.status) {
              //$urlRouterProvider.otherwise('/app/dashboard');
              angular.copy(response.user_data, loginService.syn.user_data);  
              if($state.current.name == 'access.signin'){
                $state.go('app.dashboard');
              }
            }else{
               $state.go('access.signin');
            }
          });
      }
      getUserInfor(1);*/
      function stateChange(){
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams, options){
          if(fromState.name  != toState.name){
            if(isEmpty(initData.user_data)){
              if(toState.name != 'access.signin'){
                event.preventDefault();
                //$state.go('access.signin');
              }
            }else{
              if(toState.name == 'access.signin'){
                event.preventDefault();
              }
            }
          }
        })
      }
      // save settings to local storage
      if ( angular.isDefined($localStorage.settings) ) {
        $scope.app.settings = $localStorage.settings;
      } else {
        $localStorage.settings = $scope.app.settings;
      }
      $scope.$watch('app.settings', function(){
        if( $scope.app.settings.asideDock  &&  $scope.app.settings.asideFixed ){
          // aside dock and fixed must set the header fixed.
          $scope.app.settings.headerFixed = true;
        }
        // save to local storage
        $localStorage.settings = $scope.app.settings;
      }, true);

      // angular translate
      $scope.lang = { isopen: false };
      $scope.langs = {en:'English', de_DE:'German', it_IT:'Italian'};
      $scope.selectLang = $scope.langs[$translate.proposedLanguage()] || "English";
      $scope.setLang = function(langKey, $event) {
        // set the current lang
        $scope.selectLang = $scope.langs[langKey];
        // You can change the language during runtime
        $translate.use(langKey);
        $scope.lang.isopen = !$scope.lang.isopen;
      };

      $scope.logout = function() {
        commonService.httpPost('login/logout')
          .then(function(response) {
            if (response.status) {
              angular.copy({}, commonService.sync.user_data);  
              $state.go('access.signin');
            }
          }
        );  
      };

      function isSmartDevice( $window )
      {
          // Adapted from http://www.detectmobilebrowsers.com
          var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
          // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
          return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
      }
      function isEmpty(obj) {

          // null and undefined are "empty"
          if (obj == null) return true;

          // Assume if it has a length property with a non-zero value
          // that that property is correct.
          if (obj.length > 0)    return false;
          if (obj.length === 0)  return true;

          // Otherwise, does it have any properties of its own?
          // Note that this doesn't handle
          // toString and valueOf enumeration bugs in IE < 9
          for (var key in obj) {
              if (hasOwnProperty.call(obj, key)) return false;
          }

          return true;
      }

  }]);