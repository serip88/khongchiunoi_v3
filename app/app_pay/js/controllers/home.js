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
    $scope.categories = commonService.sync.categories;
    $scope.page = {category:'PRODUCTS', category_id: -1};
    $scope.openAdd = function (size) {
      modalAdd(size,[]);
    };
    console.log(">>>>>>>>>>>>>>>>>>>>>>> HomeCtrl",$scope.$id);
    var appScope = commonService.getScope('AppCtrl_1');
    console.log(">>>>>>>>>>>>>>>>>>>>>>> appScope",appScope);
    // appScope.app.tpl.page = 'page';
    // appScope.app.tpl.sidebar = 'sidebar-none';
    function productList(options) {
      if(!options){
        options = {};
      }
      if(!options.page){
        options.page = 1;
      }
      if(!options.limit){
        options.limit = 6;
      }
      if(!options.mode){
        options.mode = 'all_client';
      }
      if(!options.category_id){
        options.category_id = -1;
      }
      var params = {'keyword':$scope.keyword, 'page':options.page, 'limit': options.limit, 'category_id': $scope.page.category_id};
          commonService.httpGet(productApi.list,params).then(function(responseData) {
              if (responseData.status) {
                $scope.productList = responseData.rows;
                $scope.pagination = responseData.pagination;
              }else{
                $scope.productList = [];
                $scope.pagination = [];
              }
          });
    }
    productList();
    $scope.toPage = function(page){
      var options = {'page':page};
      productList(options);
    }
    discountList();
    function discountList() {
      var params = {'keyword':$scope.keyword, 'page':1, 'limit': 3, 'mode': 'discount'};
      commonService.httpGet(productApi.list,params).then(function(responseData) {
          if (responseData.status) {
            $scope.discountList = responseData.rows;
            console.log($scope.discountList,'>>>>>>>>>>>>>>>>>> discountList list');
          }
      });
    }
    $scope.$on('chooseCategory', function (event, cate) {
        $scope.chooseCategory(cate);
    });
    $scope.chooseCategory = function(item){
      var options = {'page':1};
      $scope.page.category_id = item.id;
      productList(options);
      helper.scrollTo('th', -170);
      $scope.page.category = item.name;
    }
    $scope.setCategory = function(category_id){
      var options = {'page':1};
      $scope.page.category_id = category_id;
      productList(options);
      helper.scrollTo('th', -170);
      $scope.page.category = 'Products';
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
    $scope.$on('goToCart', function (event) {
        $scope.showCart();
    });

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
  // app.controller('CheckoutCtrl', ['$scope',  'postService', function($scope, postService) {
   
  //   alert('checkout');
  
  // }]);
  app.controller('PostValidCtrl', ['$scope',  'postService', function($scope, postService) {
   
    
  
  }]);
  app.controller('PostReportCtrl', ['$scope',  'postService', function($scope,  postService) {
   
    
  
  }]);


})(window, window.angular, window.jQuery);