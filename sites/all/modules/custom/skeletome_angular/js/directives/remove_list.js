myApp.directive('removeList', function() {
    return {
        restrict: 'E',
        scope: {
            listModel: '='
        },
        template: '<div>\n    <div ng-repeat="item in listModel">\n        <a ng-click="removeItem(item)" href  class="section-segment section-segment-editing">\n            <span class="btn btn-remove"><i class="ficon-remove"></i></span>\n            <span>{{ item.title || item.name }}</span>\n        </a>\n    </div>\n   \n</div>',
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