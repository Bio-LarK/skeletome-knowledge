myApp.filter('truncate', function() {
    return function(input, count) {

        if(!input) {
            // No input, dont bother
            return input;
        }

        // Lets start editing
        var out = input;
        // If count isnt there, its 25
        if(!count) {
            count = 25;
        }

        // Truncate and add ...
        if(input.length > count) {
            var htmllessString = input.replace(/(<([^>]+)>)/ig,"");
            if(htmllessString.length != input.length) {

                // work out how much html
                var truncateHTML = input.substring(0, (count - 1)) + "...";
                var truncateNoHTML = htmllessString.substring(0, (count - 1)) + "...";

                // amount of html
                var htmlCount = input.length - htmllessString.length;
                var htmlPercentage = htmlCount / input.length;

                var count = count * (1/(1 - htmlPercentage));
                out = input.substring(0, (count - 1)) + "...";

            } else {
                // No HTML (so much easier)
                out = input.substring(0, (count - 1)) + "...";
            }
        }
        return out;
    }
});

myApp.filter('capitalize', function() {
    return function(input) {
        if(input) {
            return input.toLowerCase().replace(/^.|\s\S/g, function(a) { return a.toUpperCase(); });
        } else {
            return input;
        }

    }
});