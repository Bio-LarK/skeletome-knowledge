myApp.directive('fancyBox', function() {
    return {
        link: function(scope, iElement, iAttrs) {
            setTimeout(function() {
                jQuery(".xray-list-image-link", iElement).fancybox({
                    'transitionIn'	:	'elastic',
                    'transitionOut'	:	'elastic',
                    'speedIn'		:	600,
                    'speedOut'		:	200,
                    'overlayShow'	:	false
                });
                console.log("applying box now");
            }, 500);

//                setTimeout(function() {

//                }, 500);

        }
    }
});