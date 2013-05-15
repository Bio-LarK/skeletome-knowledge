/**
 * Creates a nav search bar
 */
myApp.directive('navSearch', function() {

    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
            navSearch: '=queryHolder',
            showSuggestions: '=showSuggestions'
//            selectedIndex: '=selectedIndex'
        },
        // /drupalv2/sites/all/modules/custom/skeletome_builder/images/bone-logo.png
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/navsearch.php',
        controller: function ( $scope, $http, $filter ) {
            // Scope
            $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

            $scope.navSearch.selectedSuggestion = "";
            $scope.navSearch.querySuggestions = [];

            $scope.NOT_SELECTED = -2;
            $scope.SEARCH_SELECTED = -1;
            $scope.FIRST_SUGGESTION = 0;
            $scope.selectedIndex = $scope.SEARCH_SELECTED;

            $scope.updateSelectedSuggestionText = function(index) {

                $scope.selectedIndex = index;

                // Selecting something in the data or search or nothing
                if(0 <= $scope.selectedIndex && $scope.selectedIndex < $scope.navSearch.querySuggestions.length) {
                    var myFilter = $filter('matchcase');

                    // Get out the text suggestion
                    var newSuggestion = $scope.navSearch.querySuggestions[$scope.selectedIndex];
                    var newSuggestionText = newSuggestion.title || newSuggestion.name;

                    // Transform the suggestion to match the case of hte input (tricky stuff!)
                    if($scope.isMultitermQuery()) {
                        // take the entire string.
                        if($scope.navSearch.query.length > 80) {
                            // do nothing, its going to scroll, so we cant show suggestions anymore
                        } else {
                            $scope.navSearch.selectedSuggestion = $scope.navSearch.query + newSuggestionText.substring($scope.lastQueryTerm().length);;
                        }
//                        $scope.navSearch.selectedSuggestion = $scope.selectedQueryTerms() + myFilter(newSuggestionText, $scope.lastQueryTerm());
                    } else {
                        $scope.navSearch.selectedSuggestion = myFilter(newSuggestionText, $scope.lastQueryTerm());
                    }


                } else {
                    $scope.navSearch.selectedSuggestion = "";
                }
            }

            $scope.isMultitermQuery = function() {
                if(!angular.isDefined($scope.navSearch.query)) {
                    return false;
                }

                if($scope.navSearch.query.trim().split(';').length > 1) {
                    return true;
                } else {
                    return false;
                }

//                var semicolonPosition = $scope.navSearch.query.lastIndexOf(';');
//                if(semicolonPosition > 0) {
//                    return true;
//                } else {
//                    return false;
//                }
            }

            $scope.addToMultitermQuery = function(term) {
                $scope.navSearch.query = $scope.selectedQueryTerms().trim() + " " + term + "; ";
                $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
            }

            /**
             * Get all search terms, except the last one
             *
             * Example: "Achondroplasia; Big Head; Dwarfism" - returns "Achondroplasia; Big Head; "
             * @returns {*}
             */
            $scope.selectedQueryTerms = function() {
                if(!angular.isDefined($scope.navSearch.query)) {
                    return $scope.navSearch.query;
                }

                var semicolonPosition = $scope.navSearch.query.lastIndexOf(';');

                if(semicolonPosition > 0) {
                    // Add in spaces
                    do {
                        semicolonPosition++
                    } while($scope.navSearch.query.charAt(semicolonPosition) === " ");

                    return $scope.navSearch.query.substring(0, semicolonPosition);
                } else {
                    return $scope.navSearch.query;
                }
            }

            /**
             * Get the last term of the query
             *
             * Example: "Achondroplasia; Big Head; Dwarfism" - returns "Dwarfism"
             * @returns {*}
             */
            $scope.lastQueryTerm = function() {
                if(!angular.isDefined($scope.navSearch.query)) {
                    return $scope.navSearch.query;
                }

                var semicolonPosition = $scope.navSearch.query.lastIndexOf(';');

                // check if therei s a semicolon
                if(semicolonPosition > 0) {
                    // really multi-words query
                    return $scope.navSearch.query.substring(semicolonPosition + 1).trim();
                } else {
                    return $scope.navSearch.query;
                }
            };

            /**
             * Search for results matching the query
             * @param query
             */
            $scope.$watch('navSearch.query', function(query) {
                $scope.performSearch(query);
            });

            $scope.performSearch = function(query) {
                if(!angular.isDefined(query)) {
                    return;
                }
                // Work out the query
//                console.log("Query Term \"", $scope.lastQueryTerm(), "\"") ;

                // Filter the current displayed queries based on the input
                var textFilter = $filter('nameOrTitleStartsWith');
                $scope.navSearch.querySuggestions = textFilter($scope.navSearch.querySuggestions, $scope.lastQueryTerm());

                // Mark as none selected
                if($scope.navSearch.querySuggestions.length) {
//                    $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                    $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                } else {
                    $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                }

                if($scope.lastQueryTerm() && $scope.lastQueryTerm().length >= 2) {
                    $http.get('?q=ajax/autocomplete/all/' + $scope.lastQueryTerm()).success(function(data) {
                        // add in all suggestions

                        // We filter the results by what we have entered
                        // cause we might be getting old results back from the database
                        // from a previous query
//                        console.log("last query term", $scope.lastQueryTerm());
                        $scope.navSearch.querySuggestions = textFilter(data, $scope.lastQueryTerm());

//                        console.log("Filtered reuslts", $scope.navSearch.querySuggestions);
                        if($scope.navSearch.querySuggestions.length) {
                            // selected suggestion
                            if($scope.selectedIndex == $scope.NOT_SELECTED) {
                                // If there isnt one selected, select the first one
                                $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                            } else {
                                $scope.updateSelectedSuggestionText($scope.selectedIndex);
                            }
                        } else {
                            $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                        }

                    });
                }
            }

            $scope.enteredSuggestion = function(suggestion) {
                if(!suggestion) {
                    // they have selected somethign not in the data, it must be the first one
                    var selectedIndex = $scope.SEARCH_SELECTED;
                } else {
                    var selectedIndex = $scope.navSearch.querySuggestions.indexOf(suggestion);
                }
                $scope.updateSelectedSuggestionText(selectedIndex);
            }

            $scope.leavedSuggestion = function(suggestion) {
                $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
            }

            $scope.clear = function() {
                $scope.navSearch.query = "";
                $scope.navSearch.querySuggestions = [];
            }

        },
        link: function($scope, iElement, iAttrs) {

            iAttrs.$observe('query', function(value) {
                if(value) {
                    $scope.navSearch.query = iAttrs.query;
                }
            });


            jQuery('.navsearch-suggestions', jQuery(iElement)).click(function(event) {
                $scope.$apply(function() {
                    console.log("clicked a suggestion");
                    $scope.showSuggestions = true;
                });
            });

            // Dom
            jQuery('.navsearch-query', jQuery(iElement)).blur(function(event) {
                setTimeout(function() {
                    $scope.$apply(function() {
                        console.log("blurring");
                        $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                        $scope.showSuggestions = false;
                    });
                }, 200);
            });
            jQuery('.navsearch-query', jQuery(iElement)).focus(function(event) {
                $scope.$apply(function() {
                    $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                    $scope.showSuggestions = true;
                });
            });


            jQuery('.navsearch-query', jQuery(iElement)).keydown(function(event) {
                // Down or Up pressed
                if(event.which == 40 || event.which == 38) {
                    $scope.$apply(function() {
                        if(event.which == 40) {
                            var newIndex = Math.min($scope.navSearch.querySuggestions.length, ++$scope.selectedIndex);
                        } else {
                            var newIndex = Math.max($scope.NOT_SELECTED, --$scope.selectedIndex);
                        }

                        $scope.updateSelectedSuggestionText(newIndex);
                    });
                    return false;

                } else if (event.which == 13) {
                    // enter pressed
                    if($scope.selectedIndex < 0 || $scope.isMultitermQuery()) {
                        // do the search
                        window.location.href = "?q=search/site/" + $scope.navSearch.query;
                    } else {
                        // selected object
                        var selectedObject = $scope.navSearch.querySuggestions[$scope.selectedIndex];

                        // update the query input to look correct
                        $scope.$apply(function() {
                            $scope.navSearch.query = selectedObject.title || selectedObject.name;
                            $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                        });

                        if(angular.isDefined(selectedObject.nid)) {
                            window.location.href = "?q=node/" + selectedObject.nid;
                        } else {
                            window.location.href = "?q=taxonomy/term/" + selectedObject.tid;
                        }
                    }
                    return false;

                } else if (event.which == 9) {
                    // tab
                    // update the query input to look correct
                    if($scope.selectedIndex >= 0) {

                        var selectedObject = $scope.navSearch.querySuggestions[$scope.selectedIndex];
//
                        $scope.$apply(function() {

                            if($scope.isMultitermQuery()) {
                                console.log("tab on multi-term query");
                                // dont replace the whole thing
                                var index = $scope.navSearch.query.lastIndexOf(";");
                                $scope.navSearch.query = $scope.selectedQueryTerms().trim() + " " + (selectedObject.title || selectedObject.name);
                            } else {
                                // replace the whole thing
                                console.log("tab on regular query");
                                $scope.navSearch.query = selectedObject.title || selectedObject.name;
                            }

                            $scope.navSearch.query += "; ";
                            $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                            $scope.performSearch($scope.navSearch.query);
                        });

                    }


                    return false;
                }
            });
        }
    }
});