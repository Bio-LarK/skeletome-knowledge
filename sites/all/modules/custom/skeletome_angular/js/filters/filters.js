myApp.filter('uri', function() {
    return function(input) {

        if(!input) {
            // No input, dont bother
            return input;
        }

        return encodeURIComponent(input.replace(" ", "-").toLowerCase());
    }
});

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

/**
 * Transform the input so that its case matches the case of the string
 */
myApp.filter('matchcase', function() {
    return function(input, match) {
        if(input) {
            var newString = "";
            for (var i = 0; i < input.length && i < match.length; i++) {
                var inputCharacter = input.charAt(i);
                var matchingCharacter = match.charAt(i);

                // check the case of the character
                if(inputCharacter === matchingCharacter) {
                    // all good, move onto the next character
                    newString += inputCharacter;
                } else {
                    // they dont match
                    if(inputCharacter.toUpperCase() === matchingCharacter) {
                        // needs to be capitalized
                        newString += inputCharacter.toUpperCase();
                    } else {
                        // needs to be lowercased
                        newString += inputCharacter.toLowerCase();
                    }
                }
            }
            // Add on the rest (the match is obviously longer tahn the text)
            newString += input.substring(i);

            return newString;
        } else {
            return input;
        }
    }
});


myApp.filter('nameOrTitleStartsWith', function() {
    return function(inputArray, match) {
        match = match.toUpperCase();
        if(match == "") {
            return [];
        }

        if(inputArray) {
            var blehArray = [];;
            inputArray.forEach(function(value, index) {
                if(angular.isDefined(value.title) && value.title.toUpperCase().indexOf(match) === 0) {

                    blehArray.push(value);
                } else if(angular.isDefined(value.name) && value.name.toUpperCase().indexOf(match) === 0) {
                    blehArray.push(value);
                }
            })

            return blehArray;
        } else {
            return inputArray;
        }
    }
});

myApp.filter('lowercase', function() {
    return function(input) {
        if(input) {
            return input.toLowerCase();
        } else {
            return input;
        }
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

myApp.filter('highlight', function() {
    return function(input, match) {
        // Match index
        if(angular.isDefined(input)) {
            var matchIndex = input.indexOf(match);

            var firstString = input.substring(0, matchIndex);
            var highlightedString = input.substring(matchIndex, matchIndex + match.length + 1);
            var lastString = input.substring(matchIndex + match.length + 1);
            return firstString + '<span class="search-highlighted"><b>' + highlightedString + '</b></span>' + lastString;
        } else {
            return input;
        }
    }
});