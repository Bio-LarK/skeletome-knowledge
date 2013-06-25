myApp.directive('removeItem', function() {
    return {
        restrict: 'E',
        require: '^removeList',
        template: '<a ng-click="removeItem(item)" class="section-segment section-segment-editing" href>\n    <span class="btn btn-remove"><i class="ficon-remove"></i></span> <span ng-transclude></span>\n</a>\n',
        replace: false,
        transclude: true,
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs, removeListCtrl) {

        }
    };
});