/**
 * Created by Rain on 23/02/2016.
 */
var postApi = {
    baseUrl: baseConfig.apiUrl,
    list: 'post/list',
    save: 'post/save',
    edit: 'post/edit',
    categories: 'category/category_list'
  };

(function(window, angular, $, undefined){
    'use strict';

  app.factory("postService", ["$http", "$q", function ($http, $q) {
    var postObject = {};
    
    return postObject;
  }]);

  app.controller('PostCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'postService','commonService', 'SweetAlert', function($scope, $uibModal, $log, openModal, postService, commonService, SweetAlert) {
    $scope.openAdd = function (size) {
      modalAdd(size,[]);
    };
    function modalAdd(size) {
        var modalObj = {
          templateUrl: baseConfig.adminTpl +'/post/modal/add.html',
          size: size,
          controller: ['$scope', '$uibModalInstance', 'dataInit', function(scope, $uibModalInstance, dataInit){
              scope.category = {};
              scope.categoryList = dataInit;
              scope.categoryList.push({id:0,path_parent_name:'[Không danh mục]'});
              scope.cancel = function(){
                $uibModalInstance.close();
              };
              scope.ok = function(invalid){
                if(!validateAddCategory() || invalid){
                  return;
                }
                scope.category.parent_id = scope.category.parent_selected?scope.category.parent_selected.id:0;
                commonService.httpPost(postApi.save).then(function(responseData) {
                    if(responseData.status) {
                      SweetAlert.swal("Add success!", "", "success");
                      $uibModalInstance.close();
                      //list();
                    }
                });
              };
              function validateAddCategory() {
                if(typeof(scope.category.name) == 'undefined'){
                  return 0;
                }else{
                  return 1;
                } 
              };
          }]
        };
        modalObj.resolve = {
          dataInit: ['commonService', function(commonService){
              return commonService.httpGet(postApi.categories).then(function(responseData) {
                  if(responseData.status) {
                    return responseData.rows.length?responseData.rows:[];
                  }else{
                    return [];
                  }
              });
          }]
      };
      $uibModal.open(modalObj);
    }
    function list() {
      commonService.httpGet(postApi.list).then(function(responseData) {
          if (responseData.status) {
            $scope.list = responseData.rows;
            $scope.category = {selected:[],roles:[],is_check_all:false};
            angular.forEach( $scope.list, function(value, key) {
              $scope.list[key]['id'] = parseInt(value.id) ;
              $scope.category.roles.push({id:value.id,name:value.name});
            });
          }
      });
    }
    //list();
  }]);

  app.controller('PostValidCtrl', ['$scope', '$uibModal', 'postService', function($scope, $uibModal, postService) {
   
    
  
  }]);
  app.controller('PostReportCtrl', ['$scope', '$uibModal', 'postService', function($scope, $uibModal, postService) {
   
    
  
  }]);


})(window, window.angular, window.jQuery);