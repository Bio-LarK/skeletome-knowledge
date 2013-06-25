myApp.directive('cancelButton', function() {
    return {
        restrict: 'E',
        scope: {
            click: '&'
        },
        template: '<a href ng-click="click()" class="btn btn-cancel">\n    <i class="ficon-remove"></i> Cancel\n</a>',
        replace: true,
        transclude: true,
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs) {

        }
    };
});