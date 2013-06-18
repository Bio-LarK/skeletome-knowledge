myApp.directive('myModal', function($parse) {
    return {
        restrict: 'E',
        template: '<div class="modal modal-dark hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" ng-transclude></div>',
        replace: true,
        transclude: true,
        controller: function ( $scope, $http, $filter ) {

        },
        link: function(scope, elem, attrs) {
            scope.$watch(attrs.visible, function(value) {
                if(value) {
                    elem.modal('show');
                } else {
                    elem.modal('hide');
                }
            });

            elem.on('hidden', function () {
                var toggle = scope.$eval(attrs.visible);

                if(toggle === true) {
                    var toggleModel = $parse(attrs.visible);
                    // This lets you SET the value of the 'parsed' model

                    scope.$apply(function() {
                        toggleModel.assign(scope, false);
                    })

                }
            })
        }
    };
});