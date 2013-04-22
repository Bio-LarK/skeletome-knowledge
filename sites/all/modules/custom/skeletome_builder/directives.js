myApp.directive('ckEditor', function() {
    return {
        require: '?ngModel',
        link: function(scope, elm, attr, ngModel) {

//            CKEDITOR.timestamp = (new Date()).toString() ;

            var config = {
//                height: '800px',
                on :
                {
                    // Maximize the editor on start-up.
                    'instanceReady' : function( evt )
                    {
                        console.log("instance is ready!");
                        setTimeout(function() {
                                console.log("looking for modal inner");
                                if(jQuery(elm).closest('.modal-body-inner').length) {
                                    console.log("inside a modal inner");
                                    evt.editor.resize("100%", jQuery('.modal-body-inner').eq(0).height() - 50);
                                }
                            }
                            , 200);
                    }
                },

                forcePasteAsPlainText : true,
                fontSize_defaultLabel : '12px',
                extraPlugins : 'iframedialog,pubmed',
                toolbar :
                    [
                        { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
                        { name: 'tools', items : [ 'Pubmed' ] }
                    ]

            };
            if(attr.height) {
                config.height = attr.height;
            }

            var ck = CKEDITOR.replace(elm[0], config);

            if (!ngModel) return;

            ck.on('pasteState', function() {
                // While something is typing
                scope.$apply(function() {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ck.on('save', function() {
                // Triggers when a new reference is inserted.
                scope.$apply(function() {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ngModel.$render = function(value) {
                ck.setData(ngModel.$viewValue);
            };
        }
    };
});


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

myApp.directive('cmFocus', function() {
    return function (scope, iElement, iAttrs) {
        scope.$watch(iAttrs.cmFocus, function (value) {
            console.log("watching");
            if(value == true) {
                setTimeout(function() {
                    iElement.focus();
                }, 100);

            } else {

            }
        });
    }
});

myApp.directive('cmPopover', function() {
    return function link(scope, iElement, iAttrs) {


        var isVisible = false;
        var clickedAway = false;

        iElement.popover({
            html: true,
            trigger: 'manual',
            "animation": true,
            "html": true,
            "placement": iAttrs.cmPopover || "bottom",
            "content": scope.$eval(iAttrs.cmPopoverContent)
        }).click(function(e) {
            console.log("show popover");
            iElement.popover('show');
            isVisible = true;
            clickedAway = false;
            jQuery('.popover').bind('click',function() {
                clickedAway = false
                //alert('popover has been clicked!');
                console.log("inside popover clicked");
            });
            e.preventDefault();

            console.log("showing popover", isVisible, clickedAway);
        });

//        iElement.parent().on('click', '.popover', function(e) {
//
//            e.preventDefault();
//        });

        jQuery(document).click(function(e) {
            if(isVisible && clickedAway) {
                iElement.popover('hide');
                isVisible = clickedAway = false
            } else {
                clickedAway = true
            }
            console.log("document clicked hiding", isVisible, clickedAway);
        });


//
//        var isVisible = false;
//        var clickedAway = false;
//
//        iElement.popover({
//            "animation": true,
//            "html": true,
//            "placement": iAttrs.cmPopover || "bottom",
//            "content": scope.$eval(iAttrs.cmPopoverContent)
//        }).click(function(e) {
//            iElement.popover('show');
//            e.preventDefault();
//        });
//
//
//        jQuery('.popover').on('click', function() {
//            console.log("clicked the popover content");
//        });


//        iElement.click(function(e) {
//            console.log("clicked", isVisible);
//            if(!isVisible) {
//                console.log("making popover");
//
//
//                isVisible = true;
//            } else {
//                iElement.popover('destroy');
//                console.log("destroying popover");
//                isVisible = false;
//            }
//
//            e.preventDefault();
//        });

//
//        }).click(function(e) {
//                if()
//            console.log("pop over button clicked");
//            jQuery(this).popover('show');
//            isVisible = true;
//            e.preventDefault();
//        });

//        jQuery(document).click(function(e) {
//            if(isVisible & clickedAway) {
//                jQuery(iElement).popover('hide');
//                isVisible = clickedAway = false
//            } else {
//                clickedAway = true
//            }
//        });


    }
});

/**
 * Attr: cmTooltip and cmTooltipContent
 */
myApp.directive('cmTooltip', function() {
    return function (scope, iElement, iAttrs) {
        console.log("appling cm tooltip");
        var currentValue = "";

        iAttrs.$observe('cmTooltipContent', function(value) {
            if(value != currentValue && value != "") {
                iElement.tooltip({
                    "animation": true,
                    "placement": "top",
                    "title": value
                });
                currentValue = value;
            }
        });



    }

});


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


myApp.directive('fadeIn', function() {
    return {
        restrict: 'A',
        link: function(scope, elm, attrs) {
            jQuery(elm)
                .css({ opacity: 0 })
                .animate({ opacity: 1 }, parseInt(attrs.fadeIn));
        }
    };
});



myApp.directive('search', function() {
    return {
        transclude: true,
        scope: {
            placeholder: '@',             // the title uses the data-binding from the parent scope
            change: '&',              // create a delegate onOk function
            model: '='           // set up visible to accept data-binding
        },
        restrict: 'E',
        replace: true,
        template: '<div class="search-input"><i class="icon-search"></i><input ng-model="model" class="full-width search-input" type="text" placeholder="{{ placeholder }}"><a class="close" href="" ng-show="model.length" ng-click="model = \'\'; change()">&times;</a></div>',
        link: function postLink(scope, iElement, iAttrs) {

            scope.$watch('model', function(newValue, oldValue) {
                scope.change();
            });
        }
    }
});

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
})
myApp.directive('dropZoneUpload', function() {
    return {
        link: function (scope, iElement, iAttrs, ngModel) {

            iAttrs.$observe('dropZoneUpload', function(value) {
               if(value && value != "") {

                   iElement.dropzone(
                       {
                           url: iAttrs.dropZoneUpload,
                           parallelUploads: 2,
                           enqueueForUpload: true,
                           dictDefaultMessage: "Drop <b>X-Ray images</b> here to upload."
                       }

                   );

                   var myDropzone = Dropzone.forElement(iElement[0]);
                   myDropzone.on("addedfile", function(file) {
                       console.log("file added");
                   });
                   myDropzone.on('uploadprogress', function(file, progress) {
                       console.log("progress " + progress);
                   });

                   myDropzone.on('success', function(file, response) {
                       console.log("response");

                       setTimeout(function() {
                           myDropzone.removeFile(file);
                       }, 1000);

                       if(iAttrs.ngModel) {
                           var jsonResponse = jQuery.parseJSON( response );
                           var model = scope.$eval(iAttrs.ngModel);
                           scope.$apply(function() {
                               model.unshift(jsonResponse);
                           });

                       }
                   });

               }
            });



        }
    }
})
//
//myApp.directive('cmMouse', function() {
//    return function postLink(scope, iElement, iAttrs) {
//
//        iAttrs.$observe('cmMouseEnter', function(value) {
//            if(value) {
//                console.log("attaching mouse etner to value " + value);
//                iElement.mouseenter(function() {
//                    scope.$eval(value);
//                });
//            }
//        });
//
//        iAttrs.$observe('cmMouseLeave', function(value) {
//            if(value) {
//                iElement.mouseleave(function() {
//                    scope.$eval(value);
//                });
//            }
//        });
//
//        iAttrs.$observe('cmMouseHover', function(value) {
//            if(value) {
//                iElement.hover(function() {
//                    scope.$eval(value);
//                });
//            }
//        });
//
//    }
//});

myApp.directive('clinicalFeatureAdder', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
        },
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/clinical_feature_adder.php',
        link: function($scope, iElement, iAttrs) {

        }
    }
});

/**
 * Creates a nav search bar
 */
myApp.directive('navSearch', function() {


    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
        },
        // /drupalv2/sites/all/modules/custom/skeletome_builder/images/bone-logo.png
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/navsearch.php',
        controller: function ( $scope, $http, $filter ) {
            // Scope
            $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

            $scope.navSearch = {
                "selectedSuggestion": "",
                "querySuggestions": []
            };
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
                        $scope.navSearch.selectedSuggestion = $scope.selectedQueryTerms() + myFilter(newSuggestionText, $scope.lastQueryTerm());
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

                var semicolonPosition = $scope.navSearch.query.lastIndexOf(';');
                if(semicolonPosition > 0) {
                    return true;
                } else {
                    return false;
                }
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
                if(semicolonPosition > 0) {
                    return $scope.navSearch.query.substring(semicolonPosition + 1).trim();
                } else {
                    return $scope.navSearch.query;
                }
            };

            /**
             * Search for results matching the query
             * @param query
             */
            $scope.search = function(query) {

                // Work out the query
                console.log("Query Term", $scope.lastQueryTerm());

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
                        $scope.navSearch.querySuggestions = textFilter(data, $scope.lastQueryTerm());

                        console.log("Filtered reuslts", $scope.navSearch.querySuggestions);
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

        },
        link: function($scope, iElement, iAttrs) {

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
                                // dont replace the whole thing
                                var index = $scope.navSearch.query.lastIndexOf(";");
                                $scope.navSearch.query = $scope.selectedQueryTerms().trim() + " " + (selectedObject.title || selectedObject.name);
                            } else {
                                // replace the whole thing
                                $scope.navSearch.query = selectedObject.title || selectedObject.name;
                            }

                            $scope.navSearch.query += "; ";
                            $scope.updateSelectedSuggestionText($scope.SEARCH_SELECTED);
                            $scope.search($scope.navSearch.query);
                        });

                    }


                    return false;
                }
            });
        }
    }
});



myApp.directive('autocomplete', function() {
    return {
        require: '?ngModel',
        link: function (scope, iElement, iAttrs, ngModel) {

            var urlRoot = iAttrs.autocomplete;

            console.log(iAttrs.autocomplete);

            iElement.autocomplete({
                minLength: 0,
                delay: 0,
                source: function(request, response) {
                    // Get some data, give it to the autocomplete
                    jQuery.getJSON(urlRoot + request.term, response);
                },
                focus: function( event, ui ) {
                    var name = "";
                    if(angular.isDefined(ui.item.title)) {
                        name = ui.item.title;
                    } else {
                        name = ui.item.name;
                    }

                    iElement.val(name);
                    return false;
                },
                select: function( event, ui ) {
                    console.log("selecting");
                    var name = "";
                    if(angular.isDefined(ui.item.title)) {
                        name = ui.item.title;
                    } else {
                        name = ui.item.name;
                    }

                    scope.$apply(function() {
                        if(angular.isDefined(iAttrs.ngModel)) {
                            ngModel.$setViewValue(name);
                        }

                        if(angular.isDefined(ui.item.title)) {
//                            window.location.href = "?q=node/" + ui.item.nid;
                            console.log("redirecting to node");
                            window.location.assign(Drupal.settings.skeletome_builder.base_url + "/?q=node/" + ui.item.nid);
                        } else {
                            console.log("redirecting to term");
                            window.location.assign(Drupal.settings.skeletome_builder.base_url + "/?q=taxonomy/term/" + ui.item.tid);
                        }

                    });

                    return false;
                },
                open: function(){
                    /** Fixes some gui issues */
//                    console.log("Width: " + iElement.outerWidth());
                    jQuery('.ui-autocomplete').css('width', iElement.outerWidth());

//                    setTimeout(function() {
//                        var currentTop = parseInt(jQuery('.ui-autocomplete').css('top'), 10);
//                        jQuery('.ui-autocomplete').css('top', (currentTop + 5) + "px");
//                    }, 1000);

                }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                // The HTML for the actual dropdown
                var name = "";
                var url = "";
                var type = "";
                var img = "";

//                console.log(item);
                if(angular.isDefined(item.title)) {
                    name = item.title;
                    url = "?q=node/" + item.nid;
                    type = item.type;
                    if(type == "bone_dysplasia") {
                        img = Drupal.settings.skeletome_builder.base_url + "/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia.png";
                    } else if (type == "gene") {
                        img = Drupal.settings.skeletome_builder.base_url + "/sites/all/modules/custom/skeletome_builder/images/logo-small-gene.png";
                    }
                } else {
                    name = item.name;
                    url = "?q=taxonomy/term/" + item.tid;
                    type = item.machine_name;

                    if(type == "skeletome_vocabulary") {
                        img = Drupal.settings.skeletome_builder.base_url + "/sites/all/modules/custom/skeletome_builder/images/logo-small-phenotype.png";
                    } else if (type == "sk_group_tag") {
                        img = Drupal.settings.skeletome_builder.base_url + "/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia-group.png";
                    }
                }

                return jQuery("<li>").data( "item.autocomplete", item).append(
                    jQuery("<a>").attr("href", url).append(
                        jQuery("<img>").css('height','20px').attr("src", img)
                    ).append(' ' + name)
                ).appendTo( ul );
            };

        }
    }
});

myApp.directive('modal', function() {
    return function (scope, iElement, iAttrs) {

    };
})