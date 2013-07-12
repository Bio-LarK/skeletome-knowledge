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
        link: function(scope, elem, attrs) {
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
            var parentPaddingTop =  parseInt($parent.css('padding-top'));

            jQuery(window).scroll(function()
            {
                console.log("scrolling");
                var top = 0;
                jQuery('.locked-top-top').each(function(index, elem) {
                    top += jQuery(elem).outerHeight();
                });


                console.log("init", init, "body", jQuery(document).scrollTop());
                if (!docked && init < (jQuery(document).scrollTop() + top))
                {
                    // Lock it to the top

                    $parent.css({
                        'padding-top': parentPaddingTop + elem.outerHeight() + "px"
                    });

                    console.log("top is", top);


                    var width = elem.width();
                    elem.css({
                        position : "fixed",
                        top: top + "px",
                        width: width,
                        'z-index': '100'
                    });

                    elem.addClass('locked-top-top');

                    docked = true;
                }
                else if(docked && init >= jQuery(document).scrollTop() + top)
                {
                    $parent.css({
                        'padding-top': parentPaddingTop
                    });
                    // Put it back where it belongs
                    elem.css({
                        position : "static",
                        width: 'auto'
                    });
                    elem.removeClass('locked-top-top');

                    docked = false;
                }
            });
        }
    };
});

