/**
 * Created by Rain on 23/02/2016.
 */
(function(window, angular, $, undefined){
    'use strict';

  
  app.controller('UserGroupCtrl', ['$scope', '$uibModal', '$log', 'openModal', 'userService', 'SweetAlert', function($scope, $uibModal, $log, openModal, userService, SweetAlert) {

    function userGroupList() {
        userService.httpGet(userApi.adminGroup).then(function(responseData) {
            if (responseData.status) {
              $scope.userGroupList = responseData.rows;
              $scope.user_group = {selected:[],roles:[],is_check_all:false};
              angular.forEach( $scope.userGroupList, function(value, key) {
                $scope.userGroupList[key]['id'] = parseInt(value.id) ;
                //$scope.user.roles[value.user_id]= value.username ;
                $scope.user_group.roles.push({id:value.id,name:value.name});
              });
            }
        });
    }
    userGroupList();
    $scope.checkAll = function() {
    $scope.user_group.selected = $scope.user_group.roles.map(function(item) { return item.id; });
    };
    $scope.uncheckAll = function() {
      $scope.user_group.selected = [];
    };
    $scope.isCheckAll = function() {
      $scope.user_group.is_check_all = !$scope.user_group.is_check_all;
      if($scope.user_group.is_check_all){
        $scope.checkAll();
      }else{
        $scope.uncheckAll();
      }
    };     

    $scope.openAddGroupUser = function (size) {
      userService.httpGet(userApi.groupPermissions).then(function(responseData) {
          if (responseData.status) {
            modalAddUser(size,responseData.rows);
          }
      });
      
    }; 
    function modalAddUser(size,data) {
      var modalObj = {
        templateUrl: baseConfig.adminTpl +'/system/user_groups/add_group_user.html',
        size: size,
        controller: ['$scope', '$uibModalInstance', function(scope, $uibModalInstance){
          scope.newuser = {};
          scope.newuser.list_permissions = data.list_permissions;
          scope.usergroup = {access_selected:[],roles:[],access_check_all:false,modify_selected:[],type_group:[],type_group_selected:{} };
          scope.usergroup.type_group = data.type_group;
            angular.forEach( scope.newuser.list_permissions, function(value, key) {
              scope.usergroup.roles.push(value);
            });
          scope.checkAllAc = function() {
            //$scope.usergroup.access_selected = $scope.usergroup.roles.map(function(item) { return item.id; });
            scope.usergroup.access_selected = angular.copy(scope.usergroup.roles);
          };
          scope.uncheckAllAc = function() {
            scope.usergroup.access_selected = [];
          };  
          scope.checkAllMo = function() {
            //$scope.usergroup.access_selected = $scope.usergroup.roles.map(function(item) { return item.id; });
            scope.usergroup.modify_selected = angular.copy(scope.usergroup.roles);
          };
          scope.uncheckAllMo = function() {
            scope.usergroup.modify_selected = [];
          };
          scope.cancel = function(){
            $uibModalInstance.close();
          };
          scope.ok = function(invalid){
            if(!validateAddUser() || invalid){
              return;
            }       
            scope.newuser.access_selected = scope.usergroup.access_selected;
            scope.newuser.modify_selected = scope.usergroup.modify_selected;
            scope.newuser.type_selected   = scope.usergroup.type_group_selected.id;
            userService.httpPost(userApi.groupSave,scope.newuser).then(function(responseData) {
                if (responseData.status) {
                 SweetAlert.swal("Add success!", "", "success");
                 userGroupList();
                 $uibModalInstance.close();
                }
            });
          };
          function validateAddUser() {
            if(typeof(scope.newuser.user_group_name)  == 'undefined' ){
              return 0;
            }else{
              return 1;
            } 
          };

        }]
      };
      openModal.custom(modalObj);
    }

    $scope.userGroupEdit = function (item) {
      userService.httpGet(userApi.groupDetail,item).then(function(responseData) {
          if (responseData.status) {
            modalEditUser('lg',responseData.data);
          }
      });
    }
    function modalEditUser(size,data) {
       var modalObj = {
        templateUrl: baseConfig.adminTpl +'/system/user_groups/add_group_user.html',
        size: size,
        controller: ['$scope', '$uibModalInstance', function(scope, $uibModalInstance){
          scope.newuser = {};
          scope.usergroup = {access_selected:[],roles:[],access_check_all:false,modify_selected:[],type_group:[],type_group_selected:{} };
          scope.newuser.id = data.id;
          scope.newuser.user_group_name = data.name;
          scope.usergroup.roles = scope.newuser.list_permissions = data.list_permissions;
          scope.usergroup.type_group = data.type_group;
          scope.usergroup.type_group_selected = {id:data.type};
            angular.forEach( data.permission.access, function(value, key) {
              scope.usergroup.access_selected.push(value);
            });
            angular.forEach( data.permission.modify, function(value, key) {
              scope.usergroup.modify_selected.push(value);
            });
          scope.checkAllAc = function() {
            //$scope.usergroup.access_selected = $scope.usergroup.roles.map(function(item) { return item.id; });
            scope.usergroup.access_selected = angular.copy(scope.usergroup.roles);
          };
          scope.uncheckAllAc = function() {
            scope.usergroup.access_selected = [];
          };  
          scope.checkAllMo = function() {
            //$scope.usergroup.access_selected = $scope.usergroup.roles.map(function(item) { return item.id; });
            scope.usergroup.modify_selected = angular.copy(scope.usergroup.roles);
          };
          scope.uncheckAllMo = function() {
            scope.usergroup.modify_selected = [];
          };
          scope.cancel = function(){
            $uibModalInstance.close();
          };
          scope.ok = function(invalid){
            if(!validateAddUser() || invalid){
              return;
            }       
            scope.newuser.access_selected = scope.usergroup.access_selected;
            scope.newuser.modify_selected = scope.usergroup.modify_selected;
            scope.newuser.type_selected   = scope.usergroup.type_group_selected.id;
            userService.httpPost(userApi.groupEdit,scope.newuser).then(function(responseData) {
                if (responseData.status) {
                 SweetAlert.swal("Edit success!", "", "success");
                 userGroupList();
                 $uibModalInstance.close();
                }else{
                  SweetAlert.swal({
                    title: "Edit unsuccess!",
                    text: "",
                    type: "warning",
                    confirmButtonText: "Ok"
                  });
                }
            });
          };
          function validateAddUser() {
            if(typeof(scope.newuser.user_group_name) == 'undefined'){
              return 0;
            }else{
              return 1;
            } 
          };

        }]
      };
      openModal.custom(modalObj);
    }

    function deleteUserGroupAction(){
      userService.httpPost(userApi.groupDelete,{'group_delete':$scope.user_group.selected} ).then(function(responseData) {
          if (responseData.status) {
           SweetAlert.swal("Delete success!", "", "success");
           userGroupList();
          }else{
            SweetAlert.swal({
              title: "Have problem when delete group!",
              text: "",
              type: "warning",
              confirmButtonText: "Ok"
            });
          }
      });
    }
    $scope.deleteUserGroup = function () {
    if($scope.user_group.selected.length){
      SweetAlert.swal({
         title: "Are you sure?",
         text: "Your will not be able to recover this group!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Yes, delete it!",
         closeOnConfirm: false}, 
      function(isConfirm){ 
          if(isConfirm){
            deleteUserGroupAction();
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

  }]);

  

})(window, window.angular, window.jQuery);