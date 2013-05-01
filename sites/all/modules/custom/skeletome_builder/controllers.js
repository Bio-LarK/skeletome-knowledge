var myApp = angular.module('Skeletome', []);

myApp.factory('drupalContent', function() {
    return {
        mergeTermArrays : function(termArray1, termArray2) {
            termArray2 = angular.copy(termArray2);
            angular.forEach(termArray1, function(term1, key1){
                angular.forEach(termArray2, function(term2, key2){
                    if(term1.tid == term2.tid) {
                        termArray2.splice(key2, 1);
                        return false;
                    }
                });
            });
            return termArray1.concat(termArray2);
        },
        mergeNodeArrays : function(nodeArray1, nodeArray2) {
            nodeArray2 = angular.copy(nodeArray2);
            angular.forEach(nodeArray1, function(node1, key1){
                angular.forEach(nodeArray2, function(node2, key2){
                    if(node1.nid == node2.nid) {
                        nodeArray2.splice(key2, 1);
                        return false;
                    }
                });
            });
            return nodeArray1.concat(nodeArray2);
        },
        markAsAdded: function(myArray) {
            angular.forEach(myArray, function(element, key) {
                element.added = true;
            });
            return myArray;
        },
        sortUniqueTerms: function(arr) {
            arr = arr.sort(function (a, b) {
                return +(a.tid) - +(b.tid);
            });
            var ret = [arr[0]];
            for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
                if (arr[i-1].tid !== arr[i].tid) {
                    ret.push(arr[i]);
                }
            }
            return ret;
        },
        sortUniqueNodes: function(arr) {
            arr = arr.sort(function (a, b) {
                return +(a.nid) - +(b.nid);
            });
            var ret = [arr[0]];
            for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
                if (arr[i-1].nid !== arr[i].nid) {
                    ret.push(arr[i]);
                }
            }
            return ret;
        }
    };
});

myApp.factory('autocomplete', function($http) {
    return {
        clinicalFeatures : function(name) {
            return $http.get('?q=ajax/autocomplete/clinical-feature/' + name);
        }
    }
});


function SearchCtrl($scope, $http, filterFilter, autocomplete, drupalContent) {

    // Variables
    $scope.filterType = ''
    $scope.showEditingPanel = false;
    $scope.refinePanel = "refine-bone-dysplasias";

    // Get the boostrapped in data
    $scope.type = Drupal.settings.skeletome_builder.type;
    $scope.query = Drupal.settings.skeletome_builder.query;
    $scope.results = Drupal.settings.skeletome_builder.data.results || [];
    $scope.clinicalFeatures = Drupal.settings.skeletome_builder.data.clinical_features || [];
    $scope.counts = Drupal.settings.skeletome_builder.counts;

    $scope.filters = [];
    $scope.newFilters = [];

    $scope.totalCount = function() {
        return parseInt($scope.counts.bone_dysplasia) + parseInt($scope.counts.gene) + parseInt($scope.counts.clinical_feature) + parseInt($scope.counts.group);
    }

    $scope.selectedCount = function() {
        if($scope.type == "all") {
            return $scope.totalCount();
        } else if ($scope.type == "bone-dysplasias") {
            return $scope.counts.bone_dysplasia;
        } else if ($scope.type == "genes") {
            return $scope.counts.gene;
        } else if ($scope.type == "groups") {
            return $scope.counts.group;
        }
    }

//    $scope.filters = [];
//
//    /**
//     * Adds an object ot filter by (e.g. a clinical feature - show all items that have htis clinical feature)
//     */
    $scope.filterBy = function(content) {
        content.filter = true;
        $scope.newFilters.push(content);
    }
    $scope.removeFilter = function(content) {
        content.filter = false;
        $scope.newFilters.splice($scope.newFilters.indexOf(content), 1);
    }
//    /**
//     * Clears all filters
//     */
//    $scope.clearAllFilters = function() {
//        $scope.filters = [];
//    }
//
//    $scope.filteredResults = $scope.results;

    /**
     * Returns a list of results, filtered by the filters
     * @returns {*}
     */
    $scope.filterResults = function() {
//        console.log("Filtering results");
//        // Scope the results to the type
//        var scopedResults = filterFilter($scope.results, $scope.query);
//
//        // If there are no other things, return the scoped
//        if(!$scope.filters.length) {
//            console.log("no filters");
//            $scope.filteredResults = scopedResults;
//        }
//
//        // There are other things to filter by, lets do that
//        var results = [];
//
//        // The fields to look at for filtering
//        var fields = [
//            {
//                name            : 'sk_bd_tags',
//                referenceType   : 'tid'
//            },
//            {
//                name            : 'field_skeletome_tags',
//                referenceType   : 'tid'
//            },
//            {
//                name            : 'field_bd_gm',
//                referenceType   : 'nid'
//            }
//        ];
//
//
//        console.log('filters');
//        console.log($scope.filters);
//        // Now lets look through, and check if the results should be in the filtered set
//        angular.forEach(scopedResults, function(result, index) {
//            var allFound = true;
//
//            angular.forEach($scope.filters, function(filterContent) {
//                var found = false;
//
//                angular.forEach(fields, function(field, index) {
//                    if(angular.isDefined(result[field.name])) {
//                        angular.forEach(result[field.name], function(fieldValue, index) {
//                            // For each of the ids for each field, save it
//
//                            if(angular.isDefined(fieldValue.tid) && angular.isDefined(filterContent.tid) && fieldValue.tid == filterContent.tid) {
//                                found = true;
//                                // break
//                                console.log(fieldValue.tid + " " + filterContent.tid);
//                                return false;
//                            }
//
//                            if(angular.isDefined(fieldValue.nid) && angular.isDefined(filterContent.nid) && fieldValue.nid == filterContent.nid) {
//                                found = true;
//                                // break
//                                console.log(fieldValue.nid + " " + filterContent.nid);
//                                return false;
//                            }
//                        });
//                    }
//                    // we found the filter id
//                    if(found) {
//                        // break;
//                        return false;
//                    }
//                });
//
//                if(!found) {
//                    // we didnt find the filter id, in any of the fields
//                    allFound = false;
//                    return false;
//                }
//            });
//
//            // All the filter ids were found, so add it to the return list
//            if(allFound) {
//                results.push(result);
//            }
//        });
//
//        $scope.filteredResults = results;
    }

//    $scope.filteredGenes = function() {
//        console.log("filtering genes");
//        var results = $scope.filteredResults;
//        var gms = [];
//        angular.forEach(results, function(result, index) {
//            angular.forEach(result.field_bd_gm, function(gm, index) {
//                gms.push(gm);
//            });
//        });
//
//        return drupalContent.sortUniqueNodes(gms);
//    }

//    $scope.clinicalFeatureDisplayCount = 40;
//    $scope.filteredClinicalFeatures = function() {
//        console.log("filtering clinical features");
//        var results = $scope.filteredResults;
//        var features = [];
//        angular.forEach(results, function(result, index) {
//            angular.forEach(result.field_skeletome_tags, function(clinicalFeature, index) {
//                features.push(clinicalFeature);
//            });
//        });
//
//        return drupalContent.sortUniqueTerms(features);;
//    }
//
//    $scope.filteredGroups = function() {
//        console.log("filtering group");
//
//        var groupTags = [];
//        angular.forEach($scope.filteredResults, function(result, index) {
//            angular.forEach(result.sk_bd_tags, function(groupTag, index) {
//                groupTags.push(groupTag);
//            });
//        });
//
//        return drupalContent.sortUniqueTerms(groupTags);
//    }

    $scope.showRefinePanel = function() {
        $scope.showEditingPanel = true;
    }
    $scope.closeRefinePanel = function() {
        $scope.showEditingPanel = false;
    }
    $scope.showRefineGenes = function() {
        $scope.filterType =  'genes';
        $scope.showRefinePanel();
    }
    $scope.showRefineClinicalFeatures = function() {
        $scope.filterType =  'clinicalFeature';
        $scope.showRefinePanel();
    }
    $scope.showRefineGroups = function() {
        $scope.filterType = 'group';
        $scope.showRefinePanel();
    }

//    /**
//     * Filter to show all
//     */
//    $scope.showAll = function() {
//        $scope.type = 'all';
//        $scope.query = {};
//        $scope.filterResults();
//        $scope.displayCount = $scope.defaultDisplayCount;
//    }

//    /**
//     * Filter to show only Groups
//     */
//    $scope.showGroups = function() {
//        $scope.type = 'groups';
//        $scope.query = {
//            vocabulary_machine_name: 'sk_group'
//        }
//        $scope.filterResults();
//        $scope.displayCount = $scope.defaultDisplayCount;
//
//    }

//    /**
//     * Filter to show only bone dysplasias
//     */
//    $scope.showBoneDysplasias = function() {
//        $scope.type = "bone-dysplasias";
//        $scope.filterResults();
//        $scope.displayCount = $scope.defaultDisplayCount;
//    }
//
//    /**
//     * Filter to show clinical feature results
//     */
//    $scope.showClinicalFeatures = function() {
//        $scope.type = 'clinical-features';
//        $scope.query = {
//            vocabulary_machine_name: 'skeletome_vocabulary'
//        }
//        $scope.filterResults();
//        $scope.displayCount = $scope.defaultDisplayCount;
//    }
//    $scope.showGenes = function() {
//        $scope.type = 'genes';
//        $scope.query = {
//            type: 'gene'
//        }
//        $scope.filterResults();
//        $scope.displayCount = $scope.defaultDisplayCount;
//    }
}








function SourceReleaseCtrl($scope, $http) {

    $scope.init = function() {
        // Load in the releases
        $scope.releases = Drupal.settings.skeletome_builder.releases;

        // Check if we have a hash
        // A hash indicates the group tag to highlight
        $scope.hash = window.location.href.substring(window.location.href.indexOf("#")+1);

        // Create a reference to the selected release
        angular.forEach($scope.releases, function(release, index) {
            if(release.selected) {
                // This release has group tags
                $scope.release = $scope.releases[index];

                // Check tag for a hash match
                angular.forEach($scope.release.tags, function(tag, index) {
                    if(tag.tid == $scope.hash) {
                        // Load the Bone Dysplasias for this group
                        $scope.getBoneDysplasiasForTag(tag);
                    }
                })
            }
        });
        // Load in the source
        $scope.source = Drupal.settings.skeletome_builder.releases;
    }

    $scope.toggleShowRelease = function(release) {
        if(release != $scope.release) {
            $scope.release = release;
            $scope.getTagsForRelease(release);
        }
    }

    $scope.getTagsForRelease = function(release) {
        $http.get('?q=ajax/release/' + release.tid + '/tags/get').success(function(data) {
            release.tags = data;
        });
    }

    $scope.getBoneDysplasiasForTag = function(tag) {
        tag.showBoneDysplasias = !tag.showBoneDysplasias;
        console.log("getting bone dyspalsias");
        if(angular.isUndefined(tag.boneDysplasias)) {
            $http.get('?q=ajax/tag/' + tag.tid + '/bone-dysplasias/get').success(function(data) {
                tag.boneDysplasias = data;
            });
        }
    }


}

function ClinicalFeatureCtrl($scope) {
    $scope.boneDysplasiaDisplayLimit = 10;
    $scope.boneDysplasias = Drupal.settings.skeletome_builder.bone_dysplasias;
    $scope.clinicalFeature = Drupal.settings.skeletome_builder.clinical_feature;
    $scope.genes = Drupal.settings.skeletome_builder.genes;
    $scope.informationContent = Drupal.settings.skeletome_builder.information_content;
    $scope.bone_dysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;
}

function GroupCtrl($scope) {
    $scope.members = Drupal.settings.skeletome_builder.members;
    $scope.clinicalFeatures = Drupal.settings.skeletome_builder.clinical_features;

    console.log($scope.clinicalFeatures);
    $scope.genes = Drupal.settings.skeletome_builder.genes;
    $scope.sourceRelease = Drupal.settings.skeletome_builder.source_release;
    $scope.source = Drupal.settings.skeletome_builder.source;
    $scope.groupName = Drupal.settings.skeletome_builder.group_name;
}

function StatementCtrl($scope, $http) {
    $scope.statementDisplayLimit = 2;

    /**
     * Show the area for statement creation
     */
    $scope.showAddStatement = function() {
        $scope.model.isAddingStatement = true;
    }

    /**
     * Hide the area for statement creation
     * @param newStatement      The statement object to clear
     */
    $scope.cancelStatement = function(newStatement) {
        $scope.model.isAddingStatement = false;
        newStatement = "";
    }

    /**
     * Show the comments for a statement
     * @param statement
     */
    $scope.showComments = function(statement) {
        statement.isShowingComments = !statement.isShowingComments;

        if(angular.isUndefined(statement.comments)) {
            statement.isLoadingComments = true;
            $http.get('?q=ajax/statement/' + statement.nid + '/comments').success(function(data){
                statement.isLoadingComments = false;
                statement.comments = data;
            });
        }
    }

    $scope.deleteCommentFromStatement = function(comment, statement) {
        statement.comments.splice(statement.comments.indexOf(comment), 1);

        $http.get('?q=ajax/statement/' + statement.nid + '/comment/' + comment.cid + '/remove', {
        }).success(function(data) {
        });
    }
    $scope.showEditStatements = function() {
        $scope.isEditingStatements = true;
        angular.forEach($scope.statements, function(statement, index) {
            if(statement.comments && statement.comments.length) {
                statement.showComments = true;
            }
        });
    }
    $scope.hideEditStatements = function() {
        $scope.isEditingStatements = false;
        angular.forEach($scope.statements, function(statement, index) {
            statement.showComments = false;
        });
    }
    $scope.deleteStatement = function(statement) {
        $scope.statements.splice($scope.statements.indexOf(statement), 1);
        $http.get('?q=ajax/statement/' + statement.nid + '/remove', {
        }).success(function(data) {
        });
    }


    /**
     * Adds a comment to a statement
     * @param statement
     * @param comment
     */
    $scope.addComment = function(statement, comment) {

        statement.isLoadingComments = true;

        // Clear the text box
        var commentText = angular.copy(comment);
        statement.newComment = "";

        $http.post('?q=ajax/statement/' + statement.nid + '/comment/add', {
            comment_text: commentText
        }).success(function(data) {
            statement.isLoadingComments = false;
            statement.comments.push(data);
        });
    }
    $scope.cancelComment = function(statement) {
        statement.newComment = "";
        statement.isShowingComments = false;
    }

}




function PageCtrl($scope, $http) {
    $scope.mainMenu = Drupal.settings.skeletome_builder.main_menu;
    $scope.user = Drupal.settings.skeletome_builder.user;
    $scope.loginForm = Drupal.settings.skeletome_builder.login_form;
    console.log($scope.mainMenu);
    $scope.globalSearch = function(term) {
        window.location.href = "?q=search/site/" + term;
    }

    $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

}

function FrontPageCtrl($scope) {
    $scope.allClinicalFeatures = Drupal.settings.skeletome_builder.all_clinical_features;
    $scope.allBoneDysplasias = Drupal.settings.skeletome_builder.all_bone_dysplasias;
    $scope.allGroups = Drupal.settings.skeletome_builder.all_groups;
    $scope.allGenes = Drupal.settings.skeletome_builder.all_genes;
    $scope.latestReleases = Drupal.settings.skeletome_builder.latest_releases;
    $scope.topContributors = Drupal.settings.skeletome_builder.top_contributors;
}




