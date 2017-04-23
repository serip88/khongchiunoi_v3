/**
 * Created by Rain on 23/02/2016.
 */
 var areaApi = {
    baseUrl: baseConfig.apiUrl,
    areaSave: 'area/save',
    areaEdit: 'area/edit',
    areaList: 'area/list',
    areaDelete: 'area/delete',
    districtList: 'area/district_list',
    districtDelete: 'area/district_delete'
};
(function(window, angular, $, undefined){
    'use strict';

    app.factory("areaService", ["$http", "$q", function ($http, $q) {
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

	app.controller('areaCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'SweetAlert', 'commonService', function($scope, $uibModal, $log, openModal, SweetAlert, commonService) {

		function areaList() {
	        commonService.httpGet(areaApi.areaList).then(function(responseData) {
	            if (responseData.status) {
	              $scope.list = responseData.rows;
	              $scope.category = {selected:[],roles:[],is_check_all:false};
	              angular.forEach( $scope.list, function(value, key) {
	                $scope.list[key]['id'] = parseInt(value.id) ;
	                //$scope.user.roles[value.user_id]= value.username ;
	                $scope.category.roles.push({id:value.id,name:value.name});
	              });
	            }
	        });
	    }
	    areaList();

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
		$scope.openAddArea = function (size) {
			modalAddArea(size,[]);
	    };
	    function modalAddArea(size,category_list) {
	        var modalObj = {
		        templateUrl: baseConfig.adminTpl +'/area/modal/add_area.html',
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
			            commonService.httpPost(areaApi.areaSave,scope.category).then(function(responseData) {
			                if(responseData.status) {
			                 	SweetAlert.swal("Add success!", "", "success");
			                 	$uibModalInstance.close();
			                 	areaList();
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
	    $scope.areaEdit = function (item) {
		    modalEditCategory('lg',[],item);
	    }
	    function modalEditCategory(size,category_list,item) {
	    var modalObj = {
	      templateUrl: baseConfig.adminTpl +'/area/modal/add_area.html',
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
	          	commonService.httpPost(areaApi.areaEdit,scope.category).then(function(responseData) {
	              if (responseData.status) {
	               SweetAlert.swal("Edit success!", "", "success");
	               $uibModalInstance.close();
	               areaList();
	              }else{
	                SweetAlert.swal({
	                  title: "Edit False!",
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

	$scope.deleteProvince = function () {
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
      	commonService.httpPost(areaApi.areaDelete,{'category_delete':$scope.category.selected} ).then(function(responseData) {
          	if(responseData.status) {
           		SweetAlert.swal("Delete success!", "", "success");
          	}else{
	            SweetAlert.swal({
	              	title:  "Error",
	              	text: typeof(responseData.msg) != 'undefined'? responseData.msg:"Have problem when delete group![CL]",
	              	type: "warning",
	              	confirmButtonText: "Ok",
	              	html: true
	            });
          	}
          	areaList();
      	});
    }

  	}]);
	app.controller('districtCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'SweetAlert', 'commonService', function($scope, $uibModal, $log, openModal, SweetAlert, commonService) {

		function districtList() {
	        commonService.httpGet(areaApi.districtList).then(function(responseData) {
	            if (responseData.status) {
	              $scope.list = responseData.rows;
	              $scope.category = {selected:[],roles:[],is_check_all:false};
	              angular.forEach( $scope.list, function(value, key) {
	                $scope.list[key]['id'] = parseInt(value.id) ;
	                //$scope.user.roles[value.user_id]= value.username ;
	                $scope.category.roles.push({id:value.id,name:value.name});
	              });
	            }
	        });
	    }
	    districtList();

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
		$scope.openAddArea = function (size) {
			commonService.httpGet(areaApi.areaList).then(function(responseData) {
	            if (responseData.status) {
	      			modalAddArea(size,responseData.rows);
	      		}else{
	      			modalAddArea(size,[]);
	      		}
	        });
	      
	    };
	    function modalAddArea(size,category_list) {
	        var modalObj = {
		        templateUrl: baseConfig.adminTpl +'/area/modal/add_area.html',
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
			            commonService.httpPost(areaApi.areaSave,scope.category).then(function(responseData) {
			                if(responseData.status) {
			                 	SweetAlert.swal("Add success!", "", "success");
			                 	$uibModalInstance.close();
			                 	districtList();
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
	    $scope.areaEdit = function (item) {
	      commonService.httpGet(areaApi.areaList).then(function(responseData) {
	          if (responseData.status) {
	            modalEditCategory('lg',responseData.rows,item);
	          }else{
	          	modalEditCategory('lg',[],item);
	          }
	      });
	    }
	    function modalEditCategory(size,category_list,item) {
	    var modalObj = {
	      templateUrl: baseConfig.adminTpl +'/area/modal/add_area.html',
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
	          	commonService.httpPost(areaApi.areaEdit,scope.category).then(function(responseData) {
	              if (responseData.status) {
	               SweetAlert.swal("Edit success!", "", "success");
	               $uibModalInstance.close();
	               districtList();
	              }else{
	                SweetAlert.swal({
	                  title: "Edit False!",
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

	$scope.deleteProvince = function () {
	    if($scope.category.selected.length){
	      SweetAlert.swal({
	         title: "Are you sure?",
	         text: 'Your will not be able to recover this item!',
	         html: true,
	         type: "warning",
	         showCancelButton: true,
	         confirmButtonColor: "#DD6B55",
	         confirmButtonText: "Yes, delete it!",
	         closeOnConfirm: false
	     	}, 
	      function(isConfirm){ 
	          if(isConfirm){
	            deleteProvinceAction();
	          }
	      });
	    }else{
	      SweetAlert.swal({
	         title: "Please select item!",
	         text: "",
	         type: "warning",
	         confirmButtonText: "Ok"
	       });
	    }
	}
	function deleteProvinceAction(){
      	commonService.httpPost(areaApi.areaDelete,{'category_delete':$scope.category.selected} ).then(function(responseData) {
          	if(responseData.status) {
           		SweetAlert.swal("Delete success!", "", "success");
          	}else{
	            SweetAlert.swal({
	              	title:  "Error",
	              	text: typeof(responseData.msg) != 'undefined'? responseData.msg:"Have problem when delete group![CL]",
	              	type: "warning",
	              	confirmButtonText: "Ok",
	              	html: true
	            });
          	}
          	districtList();
      	});
    }

  	}]);
})(window, window.angular, window.jQuery);