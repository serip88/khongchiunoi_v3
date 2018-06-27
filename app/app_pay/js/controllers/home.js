/**
 * Created by Rain on 23/02/2016.
 */
var productApi = {
    baseUrl: baseConfig.apiUrl,
    list: 'product/product_list',
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

  app.controller('HomeCtrl', ['$scope',  '$log', 'postService','commonService', 'SweetAlert', '$modal', function($scope, $log, postService, commonService, SweetAlert, $modal) {
    $scope.form = [];
    $scope.product_detail = [];
    $scope.openAdd = function (size) {
      modalAdd(size,[]);
    };
    console.log(">>>>>>>>>>>>>>>>>>>>>>> HomeCtrl",$scope.$id);
    var appScope = commonService.getScope('AppCtrl_1');
    console.log(">>>>>>>>>>>>>>>>>>>>>>> appScope",appScope);
    // appScope.app.tpl.page = 'page';
    // appScope.app.tpl.sidebar = 'sidebar-none';
    function productlList() {
      var params = {'keyword':$scope.keyword};
          commonService.httpGet(productApi.list,params).then(function(responseData) {
              if (responseData.status) {
                $scope.productList = responseData.rows;
              }
          });
    }
    productlList();

    $scope.modalDetail = function(item){
        $scope.product_detail = item;
        var myModal = $modal({
          scope: $scope, 
          template:  baseConfig.tplUrl +'/home/modal/view-detail.html',
          show: false,
          controller: ['$scope','commonService','$timeout',function(scope, commonService, $timeout){
              scope.ok = function(hide){
                  productService.httpPost(productApi.productInvoice,$scope.total_cart).then(function(responseData) {
                      if (responseData.status) {
                       SweetAlert.swal("Thêm đơn hàng thành công!", "", "success");
                       $scope.total_cart = [];
                       productList();
                       hide();
                      }else{
                        SweetAlert.swal({
                          title: "Thêm đơn hàng thất bại!",
                          text: responseData.msg,
                          type: "warning",
                          confirmButtonText: "Close"
                        });
                      }
                });
              };
          }]
        });
        myModal.$promise.then(myModal.show);
    }
    function modalAdd(size) {
        var modalObj = {
          templateUrl: baseConfig.tplUrl +'/post/modal/add.html',
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
    $scope.save = function(){
        commonService.httpPost(postApi.save,$scope.form).then(function(responseData) {
            if(responseData.status) {
              SweetAlert.swal("Add success!", "", "success");
              $uibModalInstance.close();
              //list();
            }
        });
    };
    function check_save() {

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