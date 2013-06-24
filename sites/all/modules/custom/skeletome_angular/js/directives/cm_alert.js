myApp.directive('cmAlert', function() {
    return {
        restrict: 'E',
        scope: {
            state: '=',
            from: '@',
            to: '@'
        },
        template: '<div style="display: none;" class="section-segment alert alert-saved"><div ng-transclude></div></div> ',
        replace: true,
        transclude: true,
        require: '?ngModel',
        link: function($scope, elm, attr, ngModel) {
            $scope.$watch('state', function(newValue, oldValue) {
                if(newValue) {
                    console.log("transitioning from", newValue, oldValue, $scope.from, $scope.to);
                    if(oldValue == $scope.from && newValue == $scope.to) {
                        elm.slideDown('fast');
                        setTimeout(function() {
                            elm.slideUp('fast', function() {
                            });
                        }, 3000)
                    }
                }
            });
        }
    }
})