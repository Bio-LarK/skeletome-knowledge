myApp.directive('cmReveal', function($parse) {
    return {
        restrict: 'E',
        scope: {
            model: '=model',
            showingCount: '=showingCount',
            defaultCount: '@defaultCount'
        },
        template: '<div ng-show="model.length > defaultCount" class="section-segment">\n    <button ng-show="isHiding()" ng-click="showAll()" class="btn btn-reveal">Show More <i class="ficon-angle-down"></i></button>\n    <button ng-show="!isHiding()" ng-click="hide()" class="btn btn-reveal">Hide <i class="ficon-angle-up"></i></button>\n</div>',
        replace: true,
        controller: function ( $scope, $http, $filter ) {

            $scope.$watch('defaultCount', function(value) {
                if(value) {
                    $scope.showingCount = $scope.defaultCount;
                }
            });

            $scope.isHiding = function() {
                if(!angular.isDefined($scope.model)) {
                    return false;
                } else {
                    return $scope.model.length > $scope.showingCount;
                }

            }

            $scope.showAll = function() {
//                $scope.isHiding = false;
                $scope.showingCount = $scope.model.length;
            }

            $scope.hide = function() {
//                $scope.isHiding = true;
                $scope.showingCount = $scope.defaultCount;
            }

        },
        link: function(scope, elem, attrs) {
        }
    };
});


