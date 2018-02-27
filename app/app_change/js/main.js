(function (window, angular, $, undefined) {
'use strict';
  /* Controllers */
  app.controller('AppCtrl', ['$scope', '$translate', '$localStorage', '$window', 'commonService', '$state' , 
    function(              $scope,   $translate,   $localStorage,   $window ,  commonService,   $state) {
      console.log('main');
      console.log(">>>>>>>>>>>>>>>>>>>>>>> AppCtrl",$scope);
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
        baseConfig: baseConfig,
        tpl:{
          page: 'home',
          sidebar: 'sidebar-right'
        }
      }
      commonService.setScope($scope);
      $scope.options = {};
      $scope.options.user_data = commonService.sync.user_data;
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
      $translate.use('vi');
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
      /*init();
      function init(){
        if(helper.isEmpty($scope.options.user_data)){
          $state.go('access.signin');
        }else{
          if($state.current.name == 'access.signin'){
            $state.go('app.dashboard');
          }
        }
      }*/
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
      //B custom
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
      $scope.getHeader = function() {
        return [baseConfig.tplUrl,'/','common/header.html'].join('');
      }
      $scope.getContent = function() {
        return [baseConfig.tplUrl,'/','home/content.html'].join('');
      }
      $scope.getFooter = function() {
        return [baseConfig.tplUrl,'/','common/footer.html'].join('');
      }
      $scope.getSidebar = function() {
        if($scope.app.tpl.sidebar == 'sidebar-right'){
          return [baseConfig.tplUrl,'/','common/sidebar.html'].join('');
        }else{
          return false;
        }
      }
      $scope.isMember = function() {
        return !helper.isEmpty($scope.options.user_data);
      }
      //E custom
  }])
  .controller('headerCtrl', ['$scope', '$translate', '$modal', '$window', 'commonService', '$state', 'SweetAlert', 
    function(              $scope, $translate, $modal, $window , commonService, $state, SweetAlert) {

      $scope.showLogin = function() {
        var myModal = $modal({
          templateUrl: baseConfig.tplUrl +'/common/blocks/login.html',
          show: false,
          controller: ['$scope', 'initData', function ($scope, initData) {
            $scope.ok = function () {
              var params = {email: $scope.frm_login.email, password: $scope.frm_login.password,t:'m'};
              //commonService.options.is_alert = true;
              commonService.httpPost(commonService.api.member_login, params)
              .then(function(response) {
                if ( response.status ) {
                  angular.copy(response.user_data, commonService.sync.user_data);
                  var msg =  $translate.instant('common.LOGIN.login_success');
                  commonService.alert(msg,'s');
                  myModal.$promise.then(myModal.hide);
                }else{
                  var msg = response.msg ? response.msg : $translate.instant('common.WARNING.server_res_fail');
                  commonService.alert(msg,'w');
                }
              });
            };
          }],
          //size: 'lg',
          resolve: {
            initData: function () {
              return true;
            }
          }
        });
        myModal.$promise.then(myModal.show);
      }

      $scope.doLogout = function(params) {
        var params = {t:'m'};
        commonService.httpPost(commonService.api.member_logout, params)
          .then(function(response) {
            if ( response.status ) {
              angular.copy({}, commonService.sync.user_data);
              var msg =  $translate.instant('common.LOGOUT.logout_success');
              commonService.alert(msg,'s');
            }else{
              var msg = response.msg ? response.msg : $translate.instant('common.WARNING.server_res_fail');
              commonService.alert(msg,'w');
            }
        });
      }

      $scope.showRegister = function() {
        var myModal = $modal({
          templateUrl: baseConfig.tplUrl +'/common/blocks/register.html',
          show: false,
          controller: ['$scope', 'initData', function ($scope, initData) {
            $scope.ok = function () {
              initData.doRegister();
            };
          }],
          //size: 'lg',
          resolve: {
            initData: function () {
              return {doRegister:$scope.doRegister};
            }
          }
        });
        myModal.$promise.then(myModal.show);
      }
      $scope.doRegister = function() {
        alert('Register');
      }
      /****************
      **  DETECT WIDOW RESIZE
      *****************/
      //Handle top menu float
      angular.element(document).ready(function () {
        $(window).resize(function() {

            if (this.resizeTO)
                clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');
            }, 75);
        });

        if ($('body.fixed-header').length) {

            var top = $('#site-header').offset().top;
            if ($('body').hasClass('admin-bar')) {
                top -= 32;
            }
            $('#site-header').affix({
                top: top,
                offset: {
                    top: function() {
                        if ($('#site-header').hasClass('affix')) {
                            return top + 30;
                        } else {
                            return top + 150;
                        }
                    }
                }
            });

            $('#site-header').on('affix.bs.affix', function() {
                $('#site-header').css('width', $('#site').width());
                $('#site-body').css('padding-top', $('#site-header').outerHeight(true));
                $('body').addClass('fixed-header-fixed');
            });

            $('#site-header').on('affixed.bs.affix', function() {
                setTimeout(function() {
                    $('#site-header').addClass('affix-display');

                }, 200);

            });

            $('#site-header').on('affixed-top.bs.affix', function() {

                $('#site-header').css('width', $('#site').width());
                $('#site-body').css('padding-top', '');
                $('body').removeClass('fixed-header-fixed');
                $('#site-header').removeClass('affix-display');

            });

            if ($('#site-header').hasClass('affix')) {

                $('#site-header').css('width', $('#site').width());
                $('#site-body').css('padding-top', $('#site-header').outerHeight(true));
                $('#site-header').addClass('affix-display');
            }

            $(window).on('resize', function() {

                $('#site-header').css('width', $('#site').width());

            });
        }
      });
  }]);
})(window, window.angular, window.jQuery);