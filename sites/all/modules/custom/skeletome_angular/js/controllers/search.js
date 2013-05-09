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