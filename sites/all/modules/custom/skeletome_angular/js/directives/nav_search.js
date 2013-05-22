/**
 * Creates a nav search bar
 */
myApp.directive('navSearch', function() {

    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
            model: '=model',
            isShowingSuggestions: '=isShowingSuggestions'
//            selectedIndex: '=selectedIndex'
        },
        // /drupalv2/sites/all/modules/custom/skeletome_builder/images/bone-logo.png
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/navsearch.php',
        controller: function ( $scope, $http, $filter ) {
            // Scope
            $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

            // Array of selected terms
            $scope.model.query = [];
            // the text being entered by the user
            $scope.model.entry = "";
            // suggestions for the entry
            $scope.model.suggestions = [];
            // the highlighted suggestion
            $scope.model.suggestionText = "";
            // show suggestions
            $scope.model.isShowingSuggestions = true;

            $scope.NOT_SELECTED = -2;
            $scope.SEARCH_SELECTED = -1;
            $scope.FIRST_SUGGESTION = 0;

            $scope.model.highlightedIndex = $scope.SEARCH_SELECTED;


            $scope.updateSelectedSuggestionText = function(index) {

                $scope.selectedIndex = index;

                // Selecting something in the data or search or nothing
                if(0 <= $scope.selectedIndex && $scope.selectedIndex < $scope.model.suggestions.length) {
                    // Get out the text suggestion
                    var newSuggestion = $scope.model.suggestions[$scope.selectedIndex];
                    var newSuggestionText = newSuggestion.title || newSuggestion.name;

                    $scope.model.suggestionText = $scope.model.entry + newSuggestionText.substring($scope.model.entry.length);;

                } else {
                    $scope.model.suggestionText = "";
                }
            }

            /**
             * Search for results matching the query
             * @param query
             */
            $scope.$watch('model.entry', function(entry) {
                if(angular.isDefined(entry)) {
                    $scope.doAutocomplete();
                }
            });

            $scope.doAutocomplete = function() {

                // Empty string
                if($scope.model.entry == "") {
                    $scope.model.suggestions = [];
                    return;
                }

                // Filter past suggestions while we wait for results to come back
                var textFilter = $filter('nameOrTitleStartsWith');
                $scope.model.suggestions = textFilter($scope.model.suggestions, $scope.model.entry);

                // Are there any existing suggestions left?
                if($scope.model.suggestions.length) {
                    $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                } else {
                    $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                }


                if($scope.model.entry.length >= 2) {
                    // Got 2 characters, so search
                    $http.get('?q=ajax/autocomplete/all/' + $scope.model.entry).success(function(data) {
                        // add in all suggestions

                        // We filter the results by what we have entered
                        // cause we might be getting old results back from the database
                        // from a previous query
                        $scope.model.suggestions = textFilter(data, $scope.model.entry);

                        if($scope.model.suggestions.length) {
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
                    var selectedIndex = $scope.model.suggestions.indexOf(suggestion);
                }
                $scope.updateSelectedSuggestionText(selectedIndex);
            }

            $scope.leavedSuggestion = function(suggestion) {
                $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
            }

            $scope.clear = function() {
                $scope.model.entry = "";
                $scope.model.suggestions = [];
            }

            $scope.removeQueryObject = function(object) {
                var objectIndex = $scope.model.query.indexOf(object);
                $scope.model.query.splice(objectIndex, 1);
            }

            /** Tab pressed, complete with first suggestion */
            $scope.tabPressed = function() {
                if($scope.selectedIndex >= 0) {

                    var selectedObject = $scope.model.suggestions[$scope.selectedIndex];

                    // add the object to the query
                    $scope.model.query.push(selectedObject);
                    $scope.model.entry = "";


                    $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                    $scope.doAutocomplete();
                }
            }

            /** Enter pressed, run the search */
            $scope.enterPressed = function() {
                if($scope.model.query.length) {
                    // multi-part query
                    // &f[0]=im_field_skeletome_tags%3A13004&f[1]=im_field_skeletome_tags%3A17077

                    // Lets build the query

                    // Filters
                    var filters = "";
                    var query = "";
                    var filterIndex = 0;
                    angular.forEach($scope.model.query, function(value, index) {
                        if(value.machine_name == 'skeletome_vocabulary') {
                            filters += "&f[" + filterIndex++ + "]=im_field_skeletome_tags%3A" + value.tid;
                        }
                        if(value.type == 'bone_dysplasia' || value.type == 'gene') {
                            query += value.title + " ";
                        }
                    });
                    var entry = angular.copy($scope.model.entry);

                    window.location.href = "?q=search/site/" + entry + " " + query + filters;
                } else {
                    var selectedObject = $scope.model.suggestions[$scope.selectedIndex];

                    if(angular.isDefined(selectedObject.nid)) {
                        window.location.href = "?q=node/" + selectedObject.nid;
                    } else {
                        window.location.href = "?q=taxonomy/term/" + selectedObject.tid;
                    }
                }
            }

            $scope.arrowPressed = function(key) {
                console.log("arrow pressed", key);
                if(key == 40) {
                    var newIndex = Math.min($scope.model.suggestions.length, ++$scope.selectedIndex);
                } else {
                    var newIndex = Math.max($scope.NOT_SELECTED, --$scope.selectedIndex);
                }
                $scope.updateSelectedSuggestionText(newIndex);
            }

            $scope.inputBlurred = function() {
                console.log("blurring");
                $scope.updateSelectedSuggestionText($scope.NOT_SELECTED);
                $scope.model.isShowingSuggestions = false;
            }
            $scope.inputFocused = function() {
                $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                $scope.model.isShowingSuggestions = true;
            }

            $scope.addToMultitermQuery = function(term) {
                $scope.model.query.push(term);
                $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
            }

        },
        link: function($scope, iElement, iAttrs) {

            $scope.$watch(function() {
                return jQuery(iElement).height();
            }, function(value) {
                jQuery('ul.navsearch-suggestions').css('top', value + "px");
            });

            jQuery('.navsearch-query', jQuery(iElement)).keydown(function(event) {
                // Down or Up pressed
                if (event.which == 9) {
                    // tab
                    // update the query input to look correct
                    $scope.$apply(function() {
                        $scope.tabPressed();
                    });
                    return false;
                } else if (event.which == 13) {
                    $scope.$apply(function() {
                        $scope.enterPressed();
                    });
                    return false;
                } else if(event.which == 40 || event.which == 38) {
                    $scope.$apply(function() {
                        $scope.arrowPressed(event.which);
                    });
                    return false;
                }
            });


            jQuery('.navsearch-query', jQuery(iElement)).blur(function(event) {
                setTimeout(function() {
                    $scope.$apply(function() {
                        $scope.inputBlurred();
                    });
                }, 200);
            });
            jQuery('.navsearch-query', jQuery(iElement)).focus(function(event) {
                $scope.$apply(function() {
                    $scope.inputFocused();
                });
            });

            /*iAttrs.$observe('query', function(value) {
                if(value) {
                    $scope.navSearch.query = iAttrs.query;
                }
            });


            jQuery('.navsearch-suggestions', jQuery(iElement)).click(function(event) {
                $scope.$apply(function() {
                    console.log("clicked a suggestion");
                    $scope.isShowingSuggestions = true;
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
                            $scope.doAutocomplete($scope.navSearch.query);
                        });

                    }


                    return false;
                }
            });*/
        }
    }
});