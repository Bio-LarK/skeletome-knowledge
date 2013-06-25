myApp.directive('refreshBox', function() {
    return {
        restrict: 'E',
        template: '<div class="section-segment">\n    <div class="refreshing-box">\n        <i class="icon-refresh icon-refreshing"></i>\n    </div>\n</div>',
        replace: true,
        controller: function ($scope) {
        },
        link: function(scope, elem, attrs) {
        }
    };
});