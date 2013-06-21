myApp.directive('cmReveal', function($parse) {
    return {
        restrict: 'E',
        scope: {
            model: '=model',
            showingCount: '=showingCount',
            defaultCount: '@defaultCount'
        },
        template: '<div ng-show="model.length > defaultCount" class="section-segment"><button ng-show="isHiding()" ng-click="showAll()" class="btn btn-reveal">Show All</button><button ng-show="!isHiding()" ng-click="hide()" class="btn btn-reveal">Hide</button></div>',
        replace: true,
        controller: function ( $scope, $http, $filter ) {

            $scope.$watch('defaultCount', function(value) {
                if(value) {
                    $scope.showingCount = $scope.defaultCount;
                }
            });

            $scope.isHiding = function() {
                return $scope.model.length > $scope.showingCount;
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


