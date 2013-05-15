myApp.directive('cmFocus', function() {
    return function (scope, iElement, iAttrs) {
        scope.$watch(iAttrs.cmFocus, function (value) {
            console.log("watching");
            if(value == true) {
                setTimeout(function() {
                    iElement.focus();
                }, 100);

            } else {

            }
        });
    }
});