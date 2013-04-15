myApp.directive('ckEditor', function() {
    return {
        require: '?ngModel',
        link: function(scope, elm, attr, ngModel) {

//            CKEDITOR.timestamp = (new Date()).toString() ;

            var ck = CKEDITOR.replace(elm[0], {
                on :
                {
                    // Maximize the editor on start-up.
                    'instanceReady' : function( evt )
                    {
                        setTimeout(function() {
                            evt.editor.resize("100%", jQuery('.modal-body-inner').eq(0).height() - 50);
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

            });

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
    return function (scope, iElement, iAttrs) {

        iElement.popover({
            "animation": true,
            "html": true,
            "placement": iAttrs.cmPopover || "bottom",
            "content": scope.$eval(iAttrs.cmPopoverContent)
        });

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