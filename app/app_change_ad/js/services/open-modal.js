
app.service('openModal', ['$uibModal', '$rootScope', '$document', '$timeout', function ($uibModal, $rootScope, $document, $timeout) {
    var windowSize = 'small';
    function resetWindowSize() {
        windowSize = 'small';
    }
    var getTemplate = function (obj) {
        var scrollClass = '';
        if (obj.scroll) {
            scrollClass = " style='overflow-y:auto;height:" + obj.height + ";'";
        }
        var noPaddingClass = '';
        if(obj.no_padding){
            noPaddingClass = 'no-padding';
        }
        var cancel = 'Close';
        var template = '<div class="modal-header">' +
          '<button type="button" class="close" ng-click="cancel(false)">' +
          '<i class="ace-icon fa fa-times"></i>' +
          '</button>' +
          '<h4 class="modal-title smaller">' + obj.title + '</h4>' +
          '</div>' +
          '<div class="modal-body ' + noPaddingClass + '" ' + scrollClass + '">' +
          ((obj.hasOwnProperty('url') && '' != obj.url) ? '<div ng-include="\'' + obj.url + '\'"></div>' : '') +
          (obj.hasOwnProperty('body') ? obj.body : '') +
          '</div>' +
          '<div class="modal-footer">';

        if (isObject(obj.ok) && isString(obj.ok.txt)) {
            template += '<button class="btn btn-sm btn-success" ng-click="ok()">' + obj.ok.txt + '</button>';
        }
        if (isObject(obj.ok2) && isString(obj.ok2.txt)) {
            template += '<button class="btn btn-sm btn-warning" ng-click="ok2()">' + obj.ok2.txt + '</button>';
        }
        if (isString(obj.cancel)) {
            cancel = obj.cancel;
        }
        template += '<button class="btn btn-sm" ng-click="cancel(false)">' + cancel + '</button>';
        template += '</div>';
        return template;
    }
    var getController = function (obj) {
        return ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {
          if (isObject(obj.ok)) {
              $scope.ok = function () {
                  if (obj.ok.hasOwnProperty('fn') && isFunction(obj.ok.fn)) {
                      obj.ok.fn();
                  }
                  $scope.cancel(true);
              }
          }
          if (isObject(obj.ok2)) {
              $scope.ok2 = function () {
                  if (obj.ok2.hasOwnProperty('fn') && isFunction(obj.ok2.fn)) {
                      obj.ok2.fn();
                  }
                  $scope.cancel(true);
              };
          }
          $scope.cancel = function (yesno) {
              $uibModalInstance.close(yesno);
              if (obj.hasOwnProperty('callback') && isFunction(obj.callback))
              {
                  obj.callback();
              }
          };
        }]
    }
    var openModal = function (obj) {
        var defaultOptions = {
            windowClass: 'modal-type1 ' + windowSize,
            backdrop : 'static'
        }
        var modal = $uibModal.open(extend(defaultOptions, obj));
        resetWindowSize();
        // to keep modal-open class for parent modal
         modal.result.then(function () {
            var body = $document.find('body').eq(0);
            $timeout(function () {
                body.toggleClass('modal-open', $document.find('.modal.in').length > 0)
            }, 300);
        })
        modal.opened.then(function() {
          if(obj.hasOwnProperty('callback') && typeof obj.callback === 'function')
          {
            $timeout(function () {
                obj.callback($document);
            }, 300);
          }
        });
        return modal;
    }

    var openModalAlert = function (title, body, callback) {
        var obj = {},
        modalObj = {};
        obj.title = title;
        obj.body = body;
        if (isFunction(callback))
        {
            obj.callback = callback;
        }
        modalObj.template = getTemplate(obj);
        modalObj.controller = getController(obj);
        return openModal(modalObj);
    }

    return {
        //In case of need more width
        //We have 3 value :
        //1. small or dialog-type : default
        //2. medium
        //3. large or full
        setWindowClass: function (className) {
            windowSize = className;
        },
        alert: openModalAlert,
        confirm: function (obj) {
            var modalObj = {};
            modalObj.template = getTemplate(obj);
            modalObj.controller = getController(obj);
            return openModal(modalObj);
        },
        custom: function (obj) {
            return openModal(obj);
        },
        getTemplate: getTemplate,
        errors: function(errors) {
            var contents = '';
            var lang = {required:'common_error_required_input', pattern:'common_error_wrong_input'};
            forEach(errors, function(error, fieldName) {
                var msg = '';
                forEach(error, function(flag, errorName) {
                    if(msg !== '') {
                        msg += ', ';
                    }
                    msg += lang.hasOwnProperty(errorName) ? lang[errorName] : '';
                });
                contents += [fieldName, ' : ', msg].join('');
                contents += '<br>';
            });
            openModalAlert('alert_error_msg', contents);
        }
    }
}]); 