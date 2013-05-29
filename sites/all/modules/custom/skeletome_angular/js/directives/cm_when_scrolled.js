myApp.directive('cmWhenScrolled', function() {
    return function(scope, elm, attr) {
        var raw = elm[0];

        elm.bind('scroll', function() {
            console.log("scrolling");
            if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {

                console.log("SCROLL LOAD NOW");
                scope.$apply(attr.whenScrolled);
            }
        });
    };
});