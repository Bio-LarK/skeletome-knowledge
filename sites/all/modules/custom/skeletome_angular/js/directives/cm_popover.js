myApp.directive('cmPopover', function() {
    return {
        restrict: 'A',
        controller: function ( $scope, $http, $filter ) {

        },
        link: function($scope, iElement, iAttrs) {
            var isVisible = false;
            var clickedAway = false;

            iAttrs.$observe('cmPopoverContent', function(value) {
                if(value) {
                    iElement.popover({
                        html: true,
                        trigger: 'manual',
                        "animation": true,
                        "html": true,
                        "placement": iAttrs.cmPopover || "bottom",
                        "content": value
                    }).click(function(e) {
                        iElement.popover('show');
                        jQuery('.popover.bottom').css('left', '-100px');
                        jQuery('.popover.bottom .arrow').css('left', '85%');
                        isVisible = true;
                        clickedAway = false;
                        jQuery('.popover').bind('click',function() {
                            clickedAway = false
                        });
                        e.preventDefault();
                    });
                }

            });

            jQuery(document).click(function(e) {
                if(isVisible && clickedAway) {
                    iElement.popover('hide');
                    isVisible = clickedAway = false
                } else {
                    clickedAway = true
                }
            });
        }
    }
});