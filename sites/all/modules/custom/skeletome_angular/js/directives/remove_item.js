myApp.directive('removeItem', function() {
    return {
        restrict: 'E',
        require: '^removeList',
        template: '<a ng-click="click(item)" class="section-segment section-segment-editing media-body" href>\n    <span class="btn btn-remove" style="float:left;"><i class="ficon-remove"></i></span> <div class="media-body" ng-bind-html-unsafe="title"></div>\n</a>\n',
        replace: true,
        transclude: false,
        scope: {
            title: '@',
            click: '&'
        },
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs, removeListCtrl) {

        }
    };
});