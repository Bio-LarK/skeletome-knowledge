/**
 * Attr: cmTooltip and cmTooltipContent
 */
myApp.directive('cmTooltip', function() {
    return function (scope, iElement, iAttrs) {
        console.log("appling cm tooltip");
        var currentValue = "";

        iAttrs.$observe('cmTooltipContent', function(value) {
            if(value != currentValue && value != "") {
                iElement.tooltip({
                    "animation": true,
                    "placement": "top",
                    "title": value
                });
                currentValue = value;
            }
        });
    }

});