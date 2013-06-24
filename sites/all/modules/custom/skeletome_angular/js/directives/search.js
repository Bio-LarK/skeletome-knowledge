myApp.directive('search', function() {
    return {
        transclude: true,
        scope: {
            placeholder: '@',             // the title uses the data-binding from the parent scope
            change: '&',              // create a delegate onOk function
            model: '='           // set up visible to accept data-binding
        },
        restrict: 'E',
        replace: true,
        template: '<div class="search-input"><i class="ficon-filter"></i><input ng-model="model" class="full-width search-input" type="text" placeholder="{{ placeholder }}"><a class="close" href="" ng-show="model.length" ng-click="model = \'\'; change()">&times;</a></div>',
        link: function postLink(scope, iElement, iAttrs) {

            scope.$watch('model', function(newValue, oldValue) {
                scope.change();
            });
        }
    }
});