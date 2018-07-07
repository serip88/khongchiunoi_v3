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
    $scope.pagination = [];
    $scope.product_detail = [];
    $scope.openAdd = function (size) {
      modalAdd(size,[]);
    };
    console.log(">>>>>>>>>>>>>>>>>>>>>>> HomeCtrl",$scope.$id);
    var appScope = commonService.getScope('AppCtrl_1');
    console.log(">>>>>>>>>>>>>>>>>>>>>>> appScope",appScope);
    // appScope.app.tpl.page = 'page';
    // appScope.app.tpl.sidebar = 'sidebar-none';
    function productlList(page) {
      var params = {'keyword':$scope.keyword,'page':page};
          commonService.httpGet(productApi.list,params).then(function(responseData) {
              if (responseData.status) {
                $scope.productList = responseData.rows;
                console.log($scope.productList,'>>>>>>>>>>>>>>>>>> product list');
                $scope.pagination = responseData.pagination;
              }
          });
    }
    productlList(1);
    $scope.toPage = function(page)
    {
        productlList(page);
    }
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
    $scope.total_cart = [];
    $scope.addToCart = function (item) {
      var index = $scope.total_cart.indexOf(item);
      if(index == -1){
        item.in_cart = true;
        $scope.total_cart.push(item);
        SweetAlert.swal("Added to cart!", "", "success");
        SweetAlert.swal({
          title: "Success",
          text: "Added to cart!",
          type: "success",
          showCancelButton: true,
          cancelButtonText: "Close",
          confirmButtonText: "Show cart"
        }, 
          function(isConfirm){ 
            if (isConfirm) {
              $scope.goToCart();  
            }
          }
        );
        $scope.$emit('updateCart', $scope.total_cart);
      }else{
        SweetAlert.swal({
          title: "Warning",
          text: "Already added to cart!",
          type: "warning",
          showCancelButton: true,
          confirmButtonText: "Show cart"
        }, 
          function(isConfirm){ 
            if (isConfirm) {
              $scope.goToCart();  
            }
          }
        );
      }
    }
    $scope.goToCart = function () {
      $scope.showCart();
    }
    $scope.showCart = function () {
        var myModal = $modal({
          scope: $scope, 
          template:  baseConfig.tplUrl +'/home/modal/show-cart.html',
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
    $scope.removeCart = function (item) {
      var index = $scope.total_cart.indexOf(item);
      if(index == -1){
        SweetAlert.swal({
          title: "Warning",
          text: "Have no item in your cart!",
          type: "warning",
          confirmButtonText: "Close"
        });
      }else{
        item.in_cart = false;
        $scope.total_cart.splice(index, 1);
        $scope.$emit('updateCart', $scope.total_cart);
        SweetAlert.swal("Removed!", "", "success");
      }
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