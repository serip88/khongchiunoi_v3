var isDefined = angular.isDefined,
    isFunction = angular.isFunction,
    isString = angular.isString,
    isObject = angular.isObject,
    isArray = angular.isArray,
    forEach = angular.forEach,
    extend = angular.extend,
    equals = angular.equals,
    copy = angular.copy,
    ngElement = angular.element,
    httpBlockConfig = {block: true},
    sessionStorageEnabled = ("sessionStorage" in window) ? true : false,
    localStorageEnabled = ("localStorage" in window) ? true : false;

app.factory("commonService", ["$http", "$q", 'SweetAlert', '$translate', function ($http, $q, SweetAlert, $translate) {
    var commonObject = {};
    commonObject.sync = {user_data:{},is_requested:0};
    commonObject.options = {is_alert:false};
    commonObject.api = {
      member_login: 'access/login',
    };
    commonObject.alert = function (msg, type) {
        var title = $translate.instant('common.WARNING.warning');
        switch(type) {
            case 's':
                title = $translate.instant('common.success');
                type = "success";
                break;
            case 'e':
                title = $translate.instant('common.ERROR.error');
                type = "error";
                break;
            default:
                type = 'warning';
        }
        SweetAlert.swal({
            title: title,
            text: msg,
            type: type,
            confirmButtonText: $translate.instant('common.CONFIRM.ok')
        });
    }
    commonObject.httpGet = function (path, params, block) {
        if(typeof block == 'undefined'){
            block = true;
        }
        var deferred = $q.defer();
        $http.get([baseConfig.apiUrl, path].join('/'), {block: block, params: params})
            .success(function (data) {
                deferred.resolve(data);
            }).error(function (data) {
                deferred.resolve(data);
                if(commonObject.options.is_alert){
                    commonObject.alert(data.msg,'w');
                }
            });
        return deferred.promise;
    }

    commonObject.httpPost = function (path, params, block) {
        var deferred = $q.defer();
        if(typeof block == 'undefined'){
            block = httpBlockConfig;
        }
        $http.post([baseConfig.apiUrl, path].join('/'), params, block)
            .success(function (data) {
                deferred.resolve(data);
            }).error(function (data) {
                deferred.resolve(data);
                if(commonObject.options.is_alert){
                    commonObject.alert(data.msg,'w');
                }  
            });
        return deferred.promise;
    }

    commonObject.sup_check_file_info = function (file) {
        var stt = false;
        var valid_ext = ['jpg','jpeg','png'];
        if(file){
            var type = false; var ext = false;
            var mine = file.type.split('/');
            if(mine[0]=='image' && valid_ext.indexOf(mine[1]) != -1 ){
                stt = true;
            }
        }
        return stt;
    }
    return commonObject;
  }]);