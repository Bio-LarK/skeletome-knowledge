myApp.directive('fadeIn', function() {
    return {
        restrict: 'A',
        link: function(scope, elm, attrs) {
            jQuery(elm)
                .css({ opacity: 0 })
                .animate({ opacity: 1 }, parseInt(attrs.fadeIn));
        }
    };
});