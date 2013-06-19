myApp.directive('cmAlert', function() {
    return {
        scope: {
            'cmAlert': '=',
            'cmAlertTime': '@'
        },
        require: '?ngModel',
        link: function(scope, elm, attr, ngModel) {

            scope.$watch('cmAlert', function(newValue, oldValue) {
                console.log("old", oldValue, "new", newValue);

                if(oldValue == "isLoading" && newValue == "isDisplaying") {
                    elm.slideDown('fast');
                    setTimeout(function() {
                        elm.slideUp('fast', function() {
                            scope.$apply(function() {
                            });
                        });
                    }, 3000)
                } else {

                    if(oldValue == true && newValue == false) {
                        elm.slideDown('fast');
                        setTimeout(function() {
                            elm.slideUp('fast', function() {
                                scope.$apply(function() {
                                });
                            });
                        }, 3000)
                    } else {
                        elm.css('display', 'none');
                    }
                }
            });
        }
    }
})