myApp.directive('saveButton', function() {
    return {
        restrict: 'E',
        scope: {
            click: '&'
        },
        template: '<a href ng-click="click()" class="btn btn-save">\n    <i class="ficon-ok"></i> Save\n</a>',
        replace: true,
        transclude: true,
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs) {

        }
    };
});