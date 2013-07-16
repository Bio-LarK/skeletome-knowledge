myApp.directive('shortHighlight', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: function ($scope) {

        },
        link: function(scope, elem, attrs) {

            attrs.$observe('shortHighlight', function(value) {
                if(value == 'true') {
                    elem.css({
                        'background-color': 'rgb(255, 246, 139)'
                    });

                    elem.delay(2000).animate({
                        'background-color': 'white'
                    }, 3000, function() {
                        // completee
                    });
                }
            })
        }
    };
});

