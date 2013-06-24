myApp.directive('box', function() {
    return {
        restrict: 'E',
        scope: {
        },
        template: '<section ng-transclude></section>',
        replace: true,
        transclude: true,
        controller: function ( $scope ) {
            this.state = "isDisplaying";
            this.changeState = function(state) {
                this.state = state;
            }
        },
        link: function(scope, elem, attrs) {
        }
    };
});


myApp.directive('boxState', function() {
    return {
        require:'^box',
        restrict: 'E',
        scope: {
        },
        template: '<div class="section-segment" ng-transclude></div>',
        replace: false,
        transclude: true,
        controller: function($scope) {
        },
        link: function(scope, lElement, attrs, boxCtrl) {
            //console.log(cartController);
        }
    }
});
