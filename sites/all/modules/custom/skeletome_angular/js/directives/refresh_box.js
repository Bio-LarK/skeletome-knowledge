myApp.directive('refreshBox', function() {
    return {
        restrict: 'E',
        template: '<div class="section-segment">\n    <div class="refreshing-box">\n        <div ng-show="isInternetExplorer">\n            Loading...\n        </div>\n        <div ng-show="!isInternetExplorer">\n            <i class="icon-refresh icon-refreshing"></i>\n        </div>\n\n    </div>\n</div>',
        replace: true,
        controller: function ($scope) {
            $scope.isInternetExplorer = /*@cc_on!@*/0;
        },
        link: function(scope, elem, attrs) {
        }
    };
});