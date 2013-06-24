myApp.directive('cmGenes', function($parse) {
    return {
        restrict: 'E',
        scope: {
            title: '@title',
            model: '=model'
        },
        template: '<section><div class="section-segment section-segment-header"><h3>{{ title }}</h3></div>\n    <div class="section-segment" ng-repeat="item in model">\n        <div ng-transclude></div>\n    </div>\n</section>',
        replace: true,
        transclude: true,
        controller: function ( $scope, $http, $filter ) {


        },
        link: function(scope, elem, attrs) {
        }
    };
});


