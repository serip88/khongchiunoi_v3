/**
 * Created by Rain on 23/02/2016.
 */
 var emailApi = {
    baseUrl: baseConfig.apiUrl,
    save: 'email/save',
    edit: 'email/edit',
    emailList: 'email/list',
    emailDelete: 'email/delete'
};
(function(window, angular, $, undefined){
    'use strict';

    app.factory("statisticService", ["$http", "$q", function ($http, $q) {
	    var statisticService = {};
	    
	    return statisticService;
	  }]);

	app.controller('StatisticCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'SweetAlert', 'statisticService','commonService', function($scope, $uibModal, $log, openModal, SweetAlert, statisticService, commonService) {

		function emailList() {
			var params = {'keyword':$scope.keyword};
	        commonService.httpGet(emailApi.emailList,params).then(function(responseData) {
	            if (responseData.status) {
	              		$scope.emailList = responseData.rows;
	              		$scope.email = {selected:[],roles:[],is_check_all:false};
	              		angular.forEach( $scope.emailList, function(value, key) {
	                	$scope.emailList[key]['id'] = parseInt(value.id) ;
	                	//$scope.user.roles[value.user_id]= value.username ;
	                	$scope.email.roles.push({id:value.id,name:value.email});
	              	});
	            }
	        });
	    }
	    emailList();
	    $scope.reload = function() {
	    	emailList();
	    }
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
		          	scope.email.status = "1";
		          	scope.email.mode = 'add';
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
			                }else{
				                SweetAlert.swal({
				                  title: "Add Email False!",
				                  text: responseData.msg,
				                  type: "warning",
				                  confirmButtonText: "Close"
				                });
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
	      	// commonService.httpGet(categoryApi.categoryList).then(function(responseData) {
	       //    	if (responseData.status) {
	       //      	modalEdit('lg',item);
	       //    	}
	      	// });
	      	modalEdit('lg',item);
	    }
	    function modalEdit(size,item) {
	    var modalObj = {
	      templateUrl: baseConfig.tplUrl +'/statistic/email/add.html',
	      size: size,
	      controller: ['$scope', '$uibModalInstance','dataInit', function(scope, $uibModalInstance, dataInit){
	        scope.email = item;
	        scope.email.mode = 'edit';
	        scope.cancel = function(){
	          $uibModalInstance.close();
	        };
	        scope.ok = function(invalid){
	        	if(!validateAdd() || invalid){
	              return;
	            }
	          	commonService.httpPost(emailApi.edit,scope.email).then(function(responseData) {
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
	            if(typeof(scope.email.email) == 'undefined' ){
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
      	commonService.httpPost(emailApi.emailDelete,{'email_delete':$scope.email.selected} ).then(function(responseData) {
          	if(responseData.status) {
           		SweetAlert.swal("Delete success!", "", "success");
          	}else{
	            SweetAlert.swal({
	              	title:  "Error",
	              	text: typeof(responseData.msg) != 'undefined'? responseData.msg:"Have problem when delete ![CL]",
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