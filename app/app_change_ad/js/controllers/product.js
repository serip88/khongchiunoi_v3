/**
 * Created by Rain on 23/02/2016.
 */
 var productApi = {
    baseUrl: baseConfig.apiUrl,
    productSave: 'product/save',
    productEdit: 'product/edit',
    productDelete: 'product/delete',
    productList: 'product/product_list',
    categoryList: 'category/category_list',
};
(function(window, angular, $, undefined){
	'use strict';

    app.factory("productService", ["$http", "$q", 'SweetAlert', function ($http, $q, SweetAlert) {
	    var productObject = {};
	    
	    productObject.httpGet = function (path, params, block) {
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
	    productObject.httpPost = function (path, params, block) {
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
	    return productObject;
	}]);

	app.controller('ProductCtrl', ['$scope', '$log', 'openModal', 'SweetAlert', 'productService', 'commonService', function($scope, $log, openModal, SweetAlert, productService, commonService) {
		$scope.checkAll = function() {
	    	$scope.products.selected = $scope.products.roles.map(function(item) { return item.id; });
	    };
	    $scope.uncheckAll = function() {
	      	$scope.products.selected = [];
	    };
	    $scope.isCheckAll = function() {
	      	$scope.products.is_check_all = !$scope.products.is_check_all;
	      	if($scope.products.is_check_all){
	        	$scope.checkAll();
	      	}else{
	        	$scope.uncheckAll();
	      	}
	    }; 
		function productList() {
	        productService.httpGet(productApi.productList).then(function(responseData) {
	            if (responseData.status) {
	              $scope.productList = responseData.rows;
	              $scope.products = {selected:[],roles:[],is_check_all:false};
	              angular.forEach( $scope.productList, function(value, key) {
	                $scope.productList[key]['product_id'] = parseInt(value.product_id) ;
	                //$scope.user.roles[value.user_id]= value.username ;
	                $scope.products.roles.push({id:value.product_id,name:value.title});
	              });
	            }
	        });
	    }
	    productList();
		$scope.openAddProduct = function (size) {
			commonService.httpGet(productApi.categoryList).then(function(responseData) {
	            if (responseData.status) {
	      			modalAddProduct(size,responseData.rows);
	      		}
	        });
	      
	    };
	    function modalAddProduct(size,category_list) {
	        var modalObj = {
		        templateUrl: baseConfig.adminTpl +'/catalog/product/add_product.html',
		        size: size,
		        controller: ['$scope','commonService', '$uibModalInstance','Upload','$timeout','dataInit', function(scope, commonService, $uibModalInstance,Upload, $timeout, dataInit){
		          	scope.product = {};
		          	scope.categoryList = dataInit;
		          	scope.categoryList.push({id:0,path_parent_name:'[Không danh mục]'});
		           	scope.cancel = function(){
		            	$uibModalInstance.close();
		           	};
		          	scope.ok = function(invalid){
			            if(!validateAddProduct() || invalid){
			              return;
			            }
			            scope.product.parent_id = scope.product.parent_selected?scope.product.parent_selected.id:0;
			            productService.httpPost(productApi.productSave,scope.product).then(function(responseData) {
			                if(responseData.status) {
			                 	SweetAlert.swal("Add product success!", "", "success");
			                 	$uibModalInstance.close();
			                 	productList();
			                }
			            });
		          	};
		          	scope.uploadFiles = function(file, errFiles) {
			        	scope.f = file;
			        	if(file){
			        		//scope.f = file;	
			        	}else{
			        		//not update image name, image path if not select image
			        		scope.product.file = null;
			        	}
				        scope.errFile = errFiles && errFiles[0];
				        if (file) {
				        	var file_is_valid = commonService.sup_check_file_info(file);
				        	if(!file_is_valid){
				        		SweetAlert.swal({
						         	title: "Error",
						        	text: "Image file invalid",
						         	type: "warning",
						         	confirmButtonText: "Ok"
						        });
						        file = null;
						        scope.f = null;
				        		return;
				        	}
				            file.upload = Upload.upload({
				                url: baseConfig.home+'api/upload/upload_img_user',
				                method: 'POST',
							    headers: {
							        'Content-Type': file.type
							    },
				                data: {file: file}
				            });

				            file.upload.then(function (response) {
				                $timeout(function () {
				                    file.result = response.data;
				                    scope.product.file = response.data;

				                });
				            }, function (response) {
				                if (response.status > 0)
				                    scope.errorMsg = response.status + ': ' + response.data;
				            }, function (evt) {
				                file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
				            });
				        }   
				    }
		          	function validateAddProduct() {
			            if(typeof(scope.product.name) == 'undefined' ){
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

	    $scope.productEdit = function (item) {
	      	commonService.httpGet(productApi.categoryList).then(function(responseData) {
	          	if (responseData.status) {
	            	modalEditProduct('lg',responseData.rows,item);
	          	}
	      	});
	    }
	    function modalEditProduct(size,category_list,item) {
		    var modalObj = {
		      templateUrl: baseConfig.adminTpl +'/catalog/product/add_product.html',
		      size: size,
		      controller: ['$scope','commonService', '$uibModalInstance','Upload','$timeout','dataInit', function(scope, commonService, $uibModalInstance,Upload, $timeout, dataInit){
				scope.popover = {title: 'Title', content: '', templateUrl: baseConfig.adminTpl +'/catalog/product/popover/edit_image.html'};

		        scope.product = angular.copy(item);
		        scope.categoryList = dataInit;
		        scope.categoryList.push({id:0,path_parent_name:'[Không danh mục]'});
		        scope.product.parent_selected = {id:item.parent_id};
		        scope.cancel = function(){
		          $uibModalInstance.close();
		        };
		        scope.ok = function(invalid){
		        	if(!validateEditProduct() || invalid){
		              return;
		            }
		          	scope.product.parent_id = scope.product.parent_selected?scope.product.parent_selected.id:0;
		          	productService.httpPost(productApi.productEdit,scope.product).then(function(responseData) {
		              if (responseData.status) {
		               SweetAlert.swal("Edit Product success!", "", "success");
		               $uibModalInstance.close();
		               productList();
		              }else{
		                SweetAlert.swal({
		                  title: "Edit Product False!",
		                  text: "",
		                  type: "warning",
		                  confirmButtonText: "Close"
		                });
		              }
		          });
		        };
		        scope.uploadFiles = function(file, errFiles) {
		        	scope.f = file;
		        	if(file){
		        		//scope.f = file;	
		        	}else{
		        		//not update image name, image path if not select image
		        		scope.product.file = null;
		        	}
			        scope.errFile = errFiles && errFiles[0];
			        if (file) {
			        	var file_is_valid = commonService.sup_check_file_info(file);
			        	if(!file_is_valid){
			        		SweetAlert.swal({
					         	title: "Error",
					        	text: "Image file invalid",
					         	type: "warning",
					         	confirmButtonText: "Ok"
					        });
					        file = null;
					        scope.f = null;
			        		return;
			        	}
			            file.upload = Upload.upload({
			                url: baseConfig.home+'api/upload/upload_img_user',
			                method: 'POST',
						    headers: {
						        'Content-Type': file.type
						    },
			                data: {file: file}
			            });

			            file.upload.then(function (response) {
			                $timeout(function () {
			                    file.result = response.data;
			                    scope.product.file = response.data;

			                });
			            }, function (response) {
			                if (response.status > 0)
			                    scope.errorMsg = response.status + ': ' + response.data;
			            }, function (evt) {
			                file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
			            });
			        }   
			    }
		        function validateEditProduct() {
		            if(typeof(scope.product.name) == 'undefined' ){
		              return 0;
		            }else{
		              return 1;
		            } 
	          	};
	          	scope.cleanInput = function(obj) {
			    	var a = document.getElementsByClassName('bootstrap-filestyle') ;
			    	$(a).find('input').val('');
		    	}
		    }]
		    };
		    modalObj.resolve = {
		        dataInit: function(){
		            return category_list;
		        }
		    };
		    openModal.custom(modalObj);
		}
		$scope.deleteProduct = function () {
		    if($scope.products.selected.length){
		      SweetAlert.swal({
		         title: "Are you sure?",
		         text: "Your will not be able to recover this product!",
		         type: "warning",
		         showCancelButton: true,
		         confirmButtonColor: "#DD6B55",
		         confirmButtonText: "Yes, delete it!",
		         closeOnConfirm: false}, 
		      function(isConfirm){ 
		          if(isConfirm){
		            deleteProductAction();
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
		function deleteProductAction(){
	      productService.httpPost(productApi.productDelete,{'product_delete':$scope.products.selected} ).then(function(responseData) {
	          if (responseData.status) {
	           	SweetAlert.swal("Delete success!", "", "success");
	           	productList();
	          }else{
	            SweetAlert.swal({
	              title: "Have problem when delete product!",
	              text: "",
	              type: "warning",
	              confirmButtonText: "Ok"
	            });
	          }
	      });
	    }

	}]);		
})(window, window.angular, window.jQuery);