myApp.directive('cmModal', function($parse) {
    return {
        restrict: 'A',

        link: function(scope, elem, attrs) {
            scope.$watch(attrs.cmModal, function(value) {
                if(value) {
                    elem.modal('show');
                } else {
                    elem.modal('hide');
                }
            });

            elem.on('hidden', function () {
                var toggle = scope.$eval(attrs.cmModal);

                if(toggle === true) {
                    var toggleModel = $parse(attrs.cmModal);
                    // This lets you SET the value of the 'parsed' model

                    scope.$apply(function() {
                        toggleModel.assign(scope, false);
                    })

                }
            })
        }
    };
});