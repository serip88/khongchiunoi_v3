/**
 * Created by Rain on 23/02/2016.
 */
 var emailApi = {
    baseUrl: baseConfig.apiUrl,
    save: 'email/save',
    categoryEdit: 'category/edit',
    emailList: 'email/list',
    categoryDelete: 'category/delete'
};
(function(window, angular, $, undefined){
    'use strict';

    app.factory("statisticService", ["$http", "$q", function ($http, $q) {
	    var statisticService = {};
	    
	    return statisticService;
	  }]);

	app.controller('StatisticCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'SweetAlert', 'statisticService','commonService', function($scope, $uibModal, $log, openModal, SweetAlert, statisticService, commonService) {

		function emailList() {
	        commonService.httpGet(emailApi.emailList).then(function(responseData) {
	            if (responseData.status) {
	              $scope.emailList = responseData.rows;
	              $scope.email = {selected:[],roles:[],is_check_all:false};
	              angular.forEach( $scope.categoryList, function(value, key) {
	                $scope.emailList[key]['id'] = parseInt(value.id) ;
	                //$scope.user.roles[value.user_id]= value.username ;
	                $scope.email.roles.push({id:value.id,name:value.email});
	              });
	            }
	        });
	    }
	    emailList();

	    $scope.checkAll = function() {
	    	$scope.email.selected = $scope.email.roles.map(function(item) { return item.id; });
	    };
	    $scope.uncheckAll = function() {
	      	$scope.email.selected = [];
	    };
	    $scope.isCheckAll = function() {
	      	$scope.email.is_check_all = !$scope.email.is_check_all;
	      	if($scope.email.is_check_all){
	        	$scope.checkAll();
	      	}else{
	        	$scope.uncheckAll();
	      	}
	    };  
		$scope.openAdd = function (size) {
			modalAdd(size);
	    };
	    function modalAdd(size) {
	        var modalObj = {
		        templateUrl: baseConfig.tplUrl +'/statistic/email/add.html',
		        size: size,
		        controller: ['$scope', '$uibModalInstance', 'dataInit', function(scope, $uibModalInstance, dataInit){
		          	scope.email = {};
		           	scope.cancel = function(){
		            	$uibModalInstance.close();
		           	};
		          	scope.ok = function(invalid){
			            if(!validateAdd() || invalid){
			              return;
			            }
			            commonService.httpPost(emailApi.save,scope.email).then(function(responseData) {
			                if(responseData.status) {
			                 	SweetAlert.swal("Add success!", "", "success");
			                 	$uibModalInstance.close();
			                 	emailList();
			                }
			            });
		          	};
		          	function validateAdd() {
			            if(typeof(scope.email.email) == 'undefined'){
			              return 0;
			            }else{
			              return 1;
			            } 
		          	};
		        }]
	      	};
	      	modalObj.resolve = {
		        dataInit: function(){
		            return {};
		        }
		    };
	      	openModal.custom(modalObj);
	    }
	    $scope.edit = function (item) {
	      commonService.httpGet(categoryApi.categoryList).then(function(responseData) {
	          if (responseData.status) {
	            modalEdit('lg',responseData.rows,item);
	          }
	      });
	    }
	    function modalEdit(size,email_list,item) {
	    var modalObj = {
	      templateUrl: baseConfig.tplUrl +'/statistic/email/add.html',
	      size: size,
	      controller: ['$scope', '$uibModalInstance','dataInit', function(scope, $uibModalInstance, dataInit){
	        scope.email = item;
	        scope.emailList = dataInit;
	        scope.emailList.push({id:0,path_parent_name:'[Không danh mục]'});
	        scope.email.parent_selected = {id:item.parent_id};
	        scope.cancel = function(){
	          $uibModalInstance.close();
	        };
	        scope.ok = function(invalid){
	        	if(!validateAdd() || invalid){
	              return;
	            }
	          	scope.email.parent_id = scope.email.parent_selected?scope.email.parent_selected.id:0;;
	          	commonService.httpPost(categoryApi.categoryEdit,scope.email).then(function(responseData) {
	              if (responseData.status) {
	               SweetAlert.swal("Edit Email success!", "", "success");
	               $uibModalInstance.close();
	               emailList();
	              }else{
	                SweetAlert.swal({
	                  title: "Edit Email False!",
	                  text: "",
	                  type: "warning",
	                  confirmButtonText: "Close"
	                });
	              }
	          });
	        };
	        function validateAdd() {
	            if(typeof(scope.email.name) == 'undefined' ){
	              return 0;
	            }else{
	              return 1;
	            } 
          	};
	        }]
	    };
	    modalObj.resolve = {
	        dataInit: function(){
	            return email_list;
	        }
	    };
	    openModal.custom(modalObj);
	}

	$scope.delete = function () {
	    if($scope.email.selected.length){
	      SweetAlert.swal({
	         title: "Are you sure?",
	         text: 'Your will not be able to recover this email!',
	         html: true,
	         type: "warning",
	         showCancelButton: true,
	         confirmButtonColor: "#DD6B55",
	         confirmButtonText: "Yes, delete it!",
	         closeOnConfirm: false
	     	}, 
	      function(isConfirm){ 
	          if(isConfirm){
	            deleteAction();
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
	function deleteAction(){
      	commonService.httpPost(categoryApi.categoryDelete,{'category_delete':$scope.email.selected} ).then(function(responseData) {
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
          	emailList();
      	});
    }

  	}]);
})(window, window.angular, window.jQuery);