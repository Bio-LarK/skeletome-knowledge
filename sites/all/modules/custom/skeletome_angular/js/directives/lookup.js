myApp.directive('lookup', function($http) {
    return {
        restrict: 'E',
        scope: {
            url: '@',
            placeholder: '@',
            query: '=',
            results: '=',
            isLoading: '='
        },
        template: '<div>\n    <div class="search-input">\n        <i class="search-input-magnifying ficon-search"></i>\n        <input ng-model="query" ng-change="queryChanged(query)" class="full-width search-input" type="text" placeholder="{{ placeholder }}">\n        <i ng-show="isLoading" class="search-input-loading icon-refresh icon-refreshing"></i>\n        <button ng-show="!isLoading && query.length" ng-click="close()" class="search-input-close close">&times;</button>\n    </div>\n    <div ng-show="query.length && !isLoading && !results.length">\n        No results found.\n    </div>\n</div>\n',
        replace: true,
        transclude: true,
        controller: function ( $scope ) {
            $scope.loading = 0;

            $scope.close = function() {
                $scope.query = "";
                $scope.queryChanged($scope.query);
            }

            $scope.queryChanged = function(query) {
                $scope.results = [];
                if(query.length == 0) {
                    $scope.loading = 0;
                    return;
                }

                $scope.loading++;
                $scope.isLoading = $scope.loading > 0;

                // The user typed
                // Lets wait a little
                setTimeout(function() {
                    $scope.$apply(function() {
                        if(query == $scope.query) {
                            $http.get($scope.url + $scope.query).success(function(data) {
                                $scope.loading = Math.max($scope.loading - 1, 0);
                                $scope.isLoading = $scope.loading > 0;

                                if(query == $scope.query) {
                                    // We have got back the right results
                                    $scope.results = data;
                                }
                            });
                        } else {
                            $scope.loading = Math.max($scope.loading - 1, 0);
                            $scope.isLoading = $scope.loading > 0;
                        }
                    });
                }, 500);

            }
        },
        link: function(scope, elem, attrs) {

        }
    };
});