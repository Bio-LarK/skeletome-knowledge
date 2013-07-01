/**
 * Creates a nav search bar
 */
myApp.directive('navSearch', function() {

    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
            model: '=model'
        },
        // /drupalv2/sites/all/modules/custom/skeletome_builder/images/bone-logo.png
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/navsearch.php',
        controller: function ( $scope, $http, $filter ) {
            // Scope
            $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

            // Array of selected terms
            if(!angular.isDefined($scope.model.query)) {
                $scope.model.query = [];
            }

            // the text being entered by the user
            if(!angular.isDefined($scope.model.entry)) {
                $scope.model.entry = "";
            }

            // suggestions for the entry
            $scope.model.suggestions = [];
            // the highlighted suggestion
            $scope.model.suggestionText = "";
            // show suggestions

            $scope.NOT_SELECTED = -2;
            $scope.SEARCH_SELECTED = -1;
            $scope.FIRST_SUGGESTION = 0;

            $scope.isLoading = 0;

            $scope.model.highlightedIndex = $scope.SEARCH_SELECTED;


            $scope.updateSelectedSuggestionText = function(index) {

                $scope.selectedIndex = index;

                // Selecting something in the data or search or nothing
                if(0 <= $scope.selectedIndex && $scope.selectedIndex < $scope.model.suggestions.length) {
                    // Get out the text suggestion
                    var newSuggestion = $scope.model.suggestions[$scope.selectedIndex];
                    var newSuggestionText = newSuggestion.title || newSuggestion.name;

                    if($scope.model.isShowingSuggestions) {
                        $scope.model.suggestionText = $scope.model.entry + newSuggestionText.substring($scope.model.entry.length);;
                    }
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
                    $scope.doAutocomplete(entry);
                }
            });

            $scope.doAutocomplete = function(entry) {

                // Empty string
                if(entry.length == "") {
                    $scope.isLoading = 0;
                }

                if($scope.model.entry == "") {
                    $scope.model.suggestions = [];
                    $scope.model.suggestionText = "";
                    $scope.isLoading = 0;
                    return;
                }

                $scope.isLoading++;

                // Filter past suggestions while we wait for results to come back
                var textFilter = $filter('nameOrTitleStartsWith');
                $scope.model.suggestions = textFilter($scope.model.suggestions, $scope.model.entry);

                // Are there any existing suggestions left?
                if($scope.model.suggestions.length) {
                    $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                } else {
                    $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                }

                // We wait a little, and check after the short delay
                // that what we are searching for, is what the user is typing
                setTimeout(function() {
                    if(entry == $scope.model.entry) {
                        $scope.$apply(function() {
                            $http.get('?q=ajax/autocomplete/all/' + $scope.model.entry).success(function(data) {
                                // add in all suggestions
                                if($scope.isLoading > 0) {
                                    $scope.isLoading--;
                                }

                                if(entry.length < $scope.model.entry - 2) {
                                    return;
                                }

                                // We filter the results by what we have entered
                                // cause we might be getting old results back from the database
                                // from a previous query
                                $scope.model.suggestions = textFilter(data, $scope.model.entry);

                                if($scope.model.suggestions.length) {
                                    // selected suggestion
                                    if($scope.selectedIndex == $scope.NOT_SELECTED || $scope.selectedIndex == $scope.SEARCH_SELECTED) {
                                        // If there isnt one selected, select the first one
                                        $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);
                                    } else {
                                        $scope.updateSelectedSuggestionText($scope.selectedIndex);
                                    }
                                } else {
                                    $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                                }
                            });
                        });
                    } else {
                        if($scope.isLoading > 0) {
                            $scope.isLoading--;
                        }
                    }
                }, 200);
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
                $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
//                $scope.model.isShowingSuggestions = true;
            }

            /** Tab pressed, complete with first suggestion */
            $scope.tabPressed = function() {
                if($scope.selectedIndex >= 0) {
                    var selectedObject = $scope.model.suggestions[$scope.selectedIndex];

                    $scope.addToMultitermQuery(selectedObject);
                }
            }

            $scope.addToMultitermQuery = function(term) {
                $scope.model.query.push(term);
                $scope.model.entry = "";
                $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                $scope.doAutocomplete($scope.model.entry);
            }

            $scope.searchUrl = function() {
                // Lets build the query
                var boneDysplasiaIds = "";
                var boneDysplasiaCount = 0;
                var geneIds = "";
                var geneCount = 0;
                var clinicalFeatureIds = "";
                var clinicalFeatureCount = 0;
                var groupIds = "";
                var groupCount = 0;

                angular.forEach($scope.model.query, function(value, index) {
                    if(value.machine_name == "skeletome_vocabulary") {
                        clinicalFeatureIds += "&cf[" + clinicalFeatureCount++ + "]=" + value.tid;
                    } else if (value.machine_name == "sk_group_tag") {
                        groupIds += "&gr[" + groupCount++ + "]=" + value.tid;
                    } else if (value.type == "bone_dysplasia") {
                        boneDysplasiaIds += "&bd[" + boneDysplasiaCount++ + "]=" + value.nid;
                    } else if (value.type == "gene") {
                        geneIds += "&bd[" + geneCount++ + "]=" + value.nid;
                    }
                });

                return "?q=full-search&query=" + angular.copy($scope.model.entry) + boneDysplasiaIds + geneIds + clinicalFeatureIds + groupIds;
            }

            /** Enter pressed, run the search */
            $scope.enterPressed = function() {
                if($scope.model.query.length) {
                    // multi-part query
                    window.location.href = $scope.searchUrl();
                } else {
                    if($scope.selectedIndex == $scope.SEARCH_SELECTED) {
                        window.location.href = $scope.searchUrl();
                    } else {
                        var selectedObject = $scope.model.suggestions[$scope.selectedIndex];
                        if(angular.isDefined(selectedObject.nid)) {
                            window.location.href = "?q=node/" + selectedObject.nid;
                            $scope.model.entry = selectedObject.title;
                            $scope.inputBlurred();
                        } else {
                            window.location.href = "?q=taxonomy/term/" + selectedObject.tid;
                            $scope.model.entry = selectedObject.name;
                            $scope.inputBlurred();
                        }
                    }

                }
            }

            $scope.arrowPressed = function(key) {
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
                console.log("input focused!");
                $scope.model.isShowingSuggestions = true;
                $scope.updateSelectedSuggestionText($scope.FIRST_SUGGESTION);

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