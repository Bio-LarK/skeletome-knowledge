myApp.directive('removeList', function() {
    return {
        restrict: 'E',
        scope: {
            listModel: '=',
            filterBy: '='
        },
        template: '<div>\n    <div ng-repeat="item in listModel | filter:filterBy">\n        <remove-item click="removeItem(item)" title="{{ item.title || item.name || item.value }}"></remove-item>\n        <!--<a ng-click="removeItem(item)" href  class="section-segment section-segment-editing">\n        </a>-->\n    </div>\n    <div ng-show="!listModel.length" class="section-segment section-segment-editing" >\n        <div class="muted">\n            No items.\n        </div>\n    </div>\n</div>',
        replace: true,
        transclude: true,
        controller: function ($scope) {
            $scope.removeItem = function(item) {
                var index = $scope.listModel.indexOf(item);
                $scope.listModel.splice(index, 1);
            }
        },
        link: function(scope, elem, attrs) {

        }
    };
});