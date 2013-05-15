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