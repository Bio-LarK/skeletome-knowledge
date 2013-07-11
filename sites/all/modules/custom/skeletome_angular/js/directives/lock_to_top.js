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

            jQuery(window).scroll(function()
            {


                if (!docked && (elem.offset().top - jQuery("body").scrollTop() < 0))
                {
                    var width = elem.width();
                    elem.css({
                        position : "fixed",
                        top: 0,
                        width: width,
                        'z-index': '100'
                    });
                    docked = true;
                }
                else if(docked && jQuery("body").scrollTop() <= init)
                {
                    elem.css({
                        position : "static",
                        width: 'auto'
                    });

                    docked = false;
                }
            });
        }
    };
});

