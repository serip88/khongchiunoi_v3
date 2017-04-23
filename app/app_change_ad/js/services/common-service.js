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

app.factory("commonService", ["$http", "$q", 'SweetAlert', function ($http, $q, SweetAlert) {
    var commonObject = {};
    commonObject.sync = {user_data:{},is_requested:0};
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
                SweetAlert.swal({
                    title: "Warning",
                    text: data.msg,
                    type: "warning",
                    confirmButtonText: "Ok"
                });
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
                SweetAlert.swal({
                    title: "Warning",
                    text: data.msg,
                    type: "warning",
                    confirmButtonText: "Ok"
                });
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