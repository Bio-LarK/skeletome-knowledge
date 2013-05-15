myApp.directive('cmReturn', function() {
    return function (scope, iElement, iAttrs) {

        iElement.bind('keypress', function(event){
            if(event.which == 13) {
                scope.$eval(iAttrs.cmReturn);
                return false;
            }
        });
    }
});