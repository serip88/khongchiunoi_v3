(function (window, angular, $, undefined) {
'use strict';
  /* Controllers */
  app.controller('AppCtrl', ['$rootScope','$scope', '$translate', '$localStorage', '$window', 'commonService', '$state' , 
    function( $rootScope, $scope,   $translate,   $localStorage,   $window ,  commonService,   $state) {
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
      $rootScope.$on('$stateChangeStart', function(event) {
          alert(1);
        });
      // function stateChange(){
      //   $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams, options){
      //     if(fromState.name  != toState.name){
      //       if(helper.isEmpty($scope.options.user_data)){
      //         if(toState.name != 'access.signin'){
      //           event.preventDefault();
      //           //$state.go('access.signin');
      //         }
      //       }else{
      //         if(toState.name == 'access.signin'){
      //           event.preventDefault();
      //         }
      //       }
      //     }
      //   })
      // }
      // stateChange();
      //B custom
      $scope.handleState = function(href, callback, params) {
        if(href == 'app.add-post'){
          if(!helper.isEmpty($scope.options.user_data)){
            $scope.stateGo(href);      
          }else{
            if(isFunction(callback)) {
              callback();
            }
          }
        }
      }
      $scope.stateGo = function(href, callback, params) {
          if(isFunction(callback)) {
              $state.go(href, params).then(callback);
          }else{
              $state.go(href, params);
          }
      }
      $scope.logout = function() {
        commonService.httpPost('login/logout')
          .then(function(response) {
            if (response.status) {
              angular.copy({}, commonService.sync.user_data);  
              $state.go('access.signin');
            }
          }
        );  
      }
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
      $scope.cart = [];
      $scope.total_price = 0;
      $scope.$on('updateCart', function (event, cart) {
          $scope.cart = cart;
          $scope.calc_price_total();

      });
      $scope.calc_price_total = function () {
        $scope.total_price = 0;
        angular.forEach( $scope.cart, function(value, key) {
              $scope.total_price += (value.price * value.quantity);
        });
      }
      $scope.calc_price = function (item) {
        item.quantity = Math.ceil(Number(item.quantity));
        item.last_price = Number((item.price * item.quantity).toFixed(2));
        $scope.total_price = 0;
        angular.forEach( $scope.cart, function(value, key) {
              $scope.total_price += value.last_price;
        });
      }
      $scope.goToCart = function () {
        $scope.$broadcast('goToCart');
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
      function categories() {
        var params = { 'limit': 7};
        commonService.httpGet('category/category_list', params).then(function(res) {
            if (res.status) {
              $scope.categories = res.rows;
              angular.copy(res.rows, commonService.sync.categories);
            }
        });
      }
      categories();
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