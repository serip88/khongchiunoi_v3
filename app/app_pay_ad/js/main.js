'use strict';

  /* Controllers */
  app.factory("commonService", ["$http", "$q", 'SweetAlert', function ($http, $q, SweetAlert) {
    var commonObject = {};
    commonObject.sync = {user_data:{},is_requested:0,module:{}};
    commonObject.httpGet = function (path, params, block) {
        if(typeof block == 'undefined'){
            angular.element('.butterbar').removeClass('hide').addClass('active');
            block = true;
        }
        var deferred = $q.defer();
        $http.get([baseConfig.apiUrl, path].join('/'), {block: block, params: params})
            .success(function (data) {
                deferred.resolve(data);
                angular.element('.butterbar').addClass('hide').removeClass('active');
            }).error(function (data) {
                deferred.resolve(data);
                SweetAlert.swal({
                    title: "Warning",
                    text: data.msg,
                    type: "warning",
                    confirmButtonText: "Ok"
                });
                angular.element('.butterbar').addClass('hide').removeClass('active');
            });
        return deferred.promise;
    }

    commonObject.httpPost = function (path, params, block) {
        var deferred = $q.defer();
        if(typeof block == 'undefined'){
            block = httpBlockConfig;
            angular.element('.butterbar').removeClass('hide').addClass('active');
        }
        $http.post([baseConfig.apiUrl, path].join('/'), params, block)
            .success(function (data) {
                deferred.resolve(data);
                angular.element('.butterbar').addClass('hide').removeClass('active');
            }).error(function (data) {
                deferred.resolve(data);
                SweetAlert.swal({
                    title: "Warning",
                    text: data.msg,
                    type: "warning",
                    confirmButtonText: "Ok"
                });
                angular.element('.butterbar').addClass('hide').removeClass('active');
            });
        return deferred.promise;
    }

    commonObject.sup_check_file_info = function (file) {
        var stt = false;
        var valid_ext = ['jpg','jpeg','png'];
        if(file){
            var type = false; var ext = false;
            var mine = file.type.split('/');
            if(mine[0]=='image' && valid_ext.indexOf(mine[1]) != -1 ){
                stt = true;
            }
        }
        return stt;
    }
    return commonObject;
  }])
  .controller('AppCtrl', ['$rootScope','$scope', '$translate', '$localStorage', '$window', 'commonService', '$state' , 
    function(              $rootScope, $scope,   $translate,   $localStorage,   $window ,  commonService,   $state) {
      // add 'ie' classes to html
      var isIE = !!navigator.userAgent.match(/MSIE/i);
      isIE && angular.element($window.document.body).addClass('ie');
      isSmartDevice( $window ) && angular.element($window.document.body).addClass('smart');

      // config
      $scope.app = {
        name: 'Angulr',
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
          container: false
        },
        baseConfig: baseConfig
      }
      $scope.options = {};
      $scope.options.user_data = commonService.sync.user_data;
      $scope.options.module = commonService.sync.module;
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
      $scope.langs = {en:'English', vi:'Tiếng Việt'};
      $translate.use('en');
      $scope.selectLang = $scope.langs[$translate.proposedLanguage()] || "English";
      $scope.setLang = function(langKey, $event) {
        // set the current lang
        $scope.selectLang = $scope.langs[langKey];
        // You can change the language during runtime
        $translate.use(langKey);
        $scope.lang.isopen = !$scope.lang.isopen;
      };

      function isSmartDevice( $window )
      {
          // Adapted from http://www.detectmobilebrowsers.com
          var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
          // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
          return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
      }
      /** Begin */
      init();
      function init(){
        if(helper.isEmpty($scope.options.user_data)){
          $state.go('access.signin');
        }else{
          if($state.current.name == 'access.signin'){
            $state.go('app.dashboard');
          }
        }
      }
      stateChange();
      function stateChange(){
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams, options){
          if(fromState.name  != toState.name){
            if(helper.isEmpty($scope.options.user_data)){
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
      $scope.logout = function() {
        commonService.httpPost('access/logout',{t:'a'})
          .then(function(response) {
            if (response.status) {
              angular.copy({}, commonService.sync.user_data);
              $state.go('access.signin');
            }
          }
        );  
      };
  }]);