/**
 * Created by Rain on 23/02/2016.
 */
 var categoryApi = {
    baseUrl: baseConfig.apiUrl,
    categorySave: 'category/save',
    categoryEdit: 'category/edit',
    categoryList: 'category/category_list',
    categoryDelete: 'category/delete'
};
(function(window, angular, $, undefined){
    'use strict';

    app.factory("categoryService", ["$http", "$q", function ($http, $q) {
	    var categoryObject = {};
	    
	    categoryObject.httpGet = function (path, params, block) {
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

	    categoryObject.httpPost = function (path, params, block) {
	        var deferred = $q.defer();
	        if(typeof block == 'undefined'){
	            block = httpBlockConfig;
	        }
	        $http.post([baseConfig.apiUrl, path].join('/'), params, block)
	            .success(function (data) {
	                deferred.resolve(data);
	            }).error(function (data) {
	                deferred.resolve(data);
	            });
	        return deferred.promise;
	    }
	    return categoryObject;
	  }]);

	app.controller('CategoryCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'SweetAlert', 'categoryService', function($scope, $uibModal, $log, openModal, SweetAlert, categoryService) {

		function categoryList() {
	        categoryService.httpGet(categoryApi.categoryList).then(function(responseData) {
	            if (responseData.status) {
	              $scope.categoryList = responseData.rows;
	              $scope.category = {selected:[],roles:[],is_check_all:false};
	              angular.forEach( $scope.categoryList, function(value, key) {
	                $scope.categoryList[key]['id'] = parseInt(value.id) ;
	                //$scope.user.roles[value.user_id]= value.username ;
	                $scope.category.roles.push({id:value.id,name:value.name});
	              });
	            }
	        });
	    }
	    categoryList();

	    $scope.checkAll = function() {
	    	$scope.category.selected = $scope.category.roles.map(function(item) { return item.id; });
	    };
	    $scope.uncheckAll = function() {
	      	$scope.category.selected = [];
	    };
	    $scope.isCheckAll = function() {
	      	$scope.category.is_check_all = !$scope.category.is_check_all;
	      	if($scope.category.is_check_all){
	        	$scope.checkAll();
	      	}else{
	        	$scope.uncheckAll();
	      	}
	    };  
		$scope.openAddCategory = function (size) {
			categoryService.httpGet(categoryApi.categoryList).then(function(responseData) {
	            if (responseData.status) {
	      			modalAddCategory(size,responseData.rows);
	      		}
	        });
	      
	    };
	    function modalAddCategory(size,category_list) {
	        var modalObj = {
		        templateUrl: baseConfig.adminTpl +'/catalog/category/add_category.html',
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
			            categoryService.httpPost(categoryApi.categorySave,scope.category).then(function(responseData) {
			                if(responseData.status) {
			                 	SweetAlert.swal("Add success!", "", "success");
			                 	$uibModalInstance.close();
			                 	categoryList();
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
		        dataInit: function(){
		            return category_list;
		        }
		    };
	      	openModal.custom(modalObj);
	    }
	    $scope.categoryEdit = function (item) {
	      categoryService.httpGet(categoryApi.categoryList).then(function(responseData) {
	          if (responseData.status) {
	            modalEditCategory('lg',responseData.rows,item);
	          }
	      });
	    }
	    function modalEditCategory(size,category_list,item) {
	    var modalObj = {
	      templateUrl: baseConfig.adminTpl +'/catalog/category/add_category.html',
	      size: size,
	      controller: ['$scope', '$uibModalInstance','dataInit', function(scope, $uibModalInstance, dataInit){
	        scope.category = item;
	        scope.categoryList = dataInit;
	        scope.categoryList.push({id:0,path_parent_name:'[Không danh mục]'});
	        scope.category.parent_selected = {id:item.parent_id};
	        scope.cancel = function(){
	          $uibModalInstance.close();
	        };
	        scope.ok = function(invalid){
	        	if(!validateAddCategory() || invalid){
	              return;
	            }
	          	scope.category.parent_id = scope.category.parent_selected?scope.category.parent_selected.id:0;;
	          	categoryService.httpPost(categoryApi.categoryEdit,scope.category).then(function(responseData) {
	              if (responseData.status) {
	               SweetAlert.swal("Edit Category success!", "", "success");
	               $uibModalInstance.close();
	               categoryList();
	              }else{
	                SweetAlert.swal({
	                  title: "Edit Category False!",
	                  text: "",
	                  type: "warning",
	                  confirmButtonText: "Close"
	                });
	              }
	          });
	        };
	        function validateAddCategory() {
	            if(typeof(scope.category.name) == 'undefined' ){
	              return 0;
	            }else{
	              return 1;
	            } 
          	};
	        }]
	    };
	    modalObj.resolve = {
	        dataInit: function(){
	            return category_list;
	        }
	    };
	    openModal.custom(modalObj);
	}

	$scope.deleteCategory = function () {
	    if($scope.category.selected.length){
	      SweetAlert.swal({
	         title: "Are you sure?",
	         text: 'Your will not be able to recover this category!',
	         html: true,
	         type: "warning",
	         showCancelButton: true,
	         confirmButtonColor: "#DD6B55",
	         confirmButtonText: "Yes, delete it!",
	         closeOnConfirm: false
	     	}, 
	      function(isConfirm){ 
	          if(isConfirm){
	            deleteCategoryAction();
	          }
	      });
	    }else{
	      SweetAlert.swal({
	         title: "Please select group!",
	         text: "",
	         type: "warning",
	         confirmButtonText: "Ok"
	       });
	    }
	}
	function deleteCategoryAction(){
      	categoryService.httpPost(categoryApi.categoryDelete,{'category_delete':$scope.category.selected} ).then(function(responseData) {
          	if(responseData.status) {
           		SweetAlert.swal("Delete success!", "", "success");
           		//categoryList();
          	}else{
	            SweetAlert.swal({
	              	title:  "Error",
	              	text: typeof(responseData.msg) != 'undefined'? responseData.msg:"Have problem when delete group![CL]",
	              	type: "warning",
	              	confirmButtonText: "Ok",
	              	html: true
	            });
          	}
          	categoryList();
      	});
    }

  	}]);
})(window, window.angular, window.jQuery);