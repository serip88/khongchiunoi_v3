// config
var app_name = 'app_pay'
var app_name_api = 'app_pay'
var baseConfig = {};
    baseConfig.apiUrl = '/index.php/'+app_name_api+'/api';
    baseConfig.tplUrl = '/app/'+app_name+'/tpl';
    baseConfig.app = '/app/'+app_name;
    baseConfig.app_common = '/app/common';
    baseConfig.home = '/';
var app =  angular.module('app')
  .config(
    ['$controllerProvider', '$compileProvider', '$filterProvider', '$provide',
    function ($controllerProvider,   $compileProvider,   $filterProvider,   $provide) {
        
        // lazy controller, directive and service
        app.controller = $controllerProvider.register;
        app.directive  = $compileProvider.directive;
        app.filter     = $filterProvider.register;
        app.factory    = $provide.factory;
        app.service    = $provide.service;
        app.constant   = $provide.constant;
        app.value      = $provide.value;
    }
  ]).config(function($popoverProvider) {
    angular.extend($popoverProvider.defaults, {
      html: true
    });
  })
  .config(['$translateProvider', function($translateProvider){
    // Register a loader for the static files
    // So, the module will search missing translation tables under the specified urls.
    // Those urls are [prefix][langKey][suffix].

    $translateProvider.useStaticFilesLoader({
      prefix: baseConfig.app+'/l10n/',
      suffix: '.js'
    });
    // Tell the module what language to use by default
    $translateProvider.preferredLanguage('vi');
    // Tell the module to store the language in the local storage
    $translateProvider.useLocalStorage();
  }]);