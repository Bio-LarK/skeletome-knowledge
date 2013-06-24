myApp.directive('addList', function() {
    return {
        restrict: 'E',
        scope: {
            listModel: '=',
            addToModel: '='
        },
        template: '<div>\n    <div ng-repeat="item in listModel">\n        <a ng-show="!item.added" ng-click="itemClicked(item)" href  class="section-segment">\n            <span class="btn btn-add"><i class="ficon-ok"></i></span>\n            <span>{{ item.title || item.name }}</span>\n        </a>\n\n        <div ng-show="item.added" class="section-segment">\n            <span class="btn btn-added"><i class="ficon-ok"></i></span>\n            <span>{{ item.title || item.name }}</span>\n        </div>\n        \n    </div>\n   \n</div>',
        replace: true,
        transclude: true,
        controller: function ( $scope ) {
            // We need to sanitise the list model
            // to make sure it doesnt have anything we have already added
            $scope.$watch('listModel', function(value) {
                if(value) {
                    angular.forEach(value, function(listItem, index) {
                        angular.forEach($scope.addToModel, function(existingItem, index) {
                            if(listItem.tid) {
                                if(listItem.tid == existingItem.tid) {
                                    listItem.added = true;
                                    return;
                                }
                            }
                            if(listItem.nid) {
                                if(listItem.nid == existingItem.nid) {
                                    listItem.added = true;
                                    return;
                                }
                            }
                        })
                    })
                }
            });
            $scope.itemClicked = function(item) {
                item.added = !item.added;
                $scope.addToModel.push(item);
            }
        },
        link: function(scope, elem, attrs) {

        }
    };
});