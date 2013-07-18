myApp.directive('lockToTop', function() {
    return {
        restrict: 'A',
        replace: true,
        controller: function ($scope) {
            $scope.removeItem = function(item) {
                var index = $scope.listModel.indexOf(item);
                $scope.listModel.splice(index, 1);
            }
        },
        transclude: true,
        template: "<div ng-transclude></div>",
        replace: true,
        link: function($scope, elem, attrs) {
            /**
             * Created with JetBrains PhpStorm.
             * User: uqcmcna1
             * Date: 11/07/13
             * Time: 3:44 PM
             * To change this template use File | Settings | File Templates.
             */
            var docked = false;
            var init = elem.offset().top;
            var $parent = elem.parent();

            var elemHeight = elem.outerHeight();
            var parentPaddingTop =  parseInt($parent.css('padding-top'));
            var cssClass = "locked-to-top";

            var offset = 120;
            var elemHeight = elem.outerHeight();
            var scrollDownPast = init + elemHeight + offset;
            var scrollUpPast = init + elemHeight;

            jQuery(window).scroll(function()
            {
                var top = 0;
                var $elementsWithClass = jQuery('.' + cssClass);
                $elementsWithClass.each(function(index, elem) {
                    top += jQuery(elem).outerHeight();
                });

                if (!docked && (scrollDownPast - top) < jQuery(document).scrollTop()) {
                    // Lock it to the top

                    $parent.css({
                        'padding-top': parentPaddingTop + elemHeight + "px"
                    });

                    var width = elem.width();
                    elem.css({
                        position : "fixed",
                        top: "-" + elemHeight + 'px',
                        width: width,
                        'z-index': '100' - $elementsWithClass.length
                    });

                    elem.animate({
                        top: top + "px"
                    }, 250, function() {
                        // Animation complete.
                    });

                    elem.addClass(cssClass);

                    docked = true;
                } else if(docked && (scrollUpPast - top) >= jQuery(document).scrollTop()) {

                    $parent.css({
                        'padding-top': parentPaddingTop
                    });
                    // Put it back where it belongs
                    elem.css({
                        position : "static",
                        width: 'auto'
                    });
                    elem.removeClass(cssClass);

                    docked = false;
                }
            });

            jQuery(window).resize(function() {
                $parent.css({
                    'padding-top': parentPaddingTop
                });
                // Put it back where it belongs
                elem.css({
                    position : "static",
                    width: 'auto'
                });
                elem.removeClass(cssClass);

                docked = false;
            });
        }
    };
});

