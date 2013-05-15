myApp.directive('cmPopover', function() {
    return function link(scope, iElement, iAttrs) {


        var isVisible = false;
        var clickedAway = false;

        iElement.popover({
            html: true,
            trigger: 'manual',
            "animation": true,
            "html": true,
            "placement": iAttrs.cmPopover || "bottom",
            "content": scope.$eval(iAttrs.cmPopoverContent)
        }).click(function(e) {
            iElement.popover('show');
            isVisible = true;
            clickedAway = false;
            jQuery('.popover').bind('click',function() {
                clickedAway = false
            });
            e.preventDefault();
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
});