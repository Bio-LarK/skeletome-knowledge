myApp.directive('editButton', function() {
    return {
        restrict: 'E',
        scope: {
            click: '&'
        },
        template: '<a href ng-click="click()" class="btn btn-edit">\n    <i class="ficon-edit"></i> Edit\n</a>',
        replace: true,
        transclude: true,
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs) {

        }
    };
});