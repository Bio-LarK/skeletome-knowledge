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
function GeneCtrl($scope, $http) {
    $scope.master = {};
    $scope.edit = {};
    $scope.view = {};
    $scope.model = {};

    $scope.view.defaultDescriptionLength = 500;
    $scope.view.descriptionLength = $scope.view.defaultDescriptionLength;



    /**
     * Set up masters
     */
    $scope.master.gene = Drupal.settings.skeletome_builder.gene;
    // Fix up somep roblem with drupal
    $scope.master.gene.field_gene_gene_mutation = jQuery.map($scope.master.gene.field_gene_gene_mutation, function (value, key) { return value; });


    $scope.boneDysplasias = Drupal.settings.skeletome_builder.bone_dysplasias;
    $scope.boneDysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;

    console.log("Bone Dysplasias");
    console.log($scope.boneDysplasias);
    $scope.statements = Drupal.settings.skeletome_builder.statements;

    // Setup the editors
    $scope.editors = Drupal.settings.skeletome_builder.editors;

    console.log("statements");
    console.log($scope.statements);
    /**
     * Open the editing panel
     * @param panel
     */
    $scope.openEditingPanel = function(panel) {
        $scope.view.editingPanel = true;
        $scope.view.editingPanelType = panel;
    }
    /**
     * Close the editing panel
     */
    $scope.closeEditingPanel = function() {
        $scope.view.editingPanel = false;
        console.log($scope.view);
    }

    /**
     * Show Edit Gene Mutation
     * @param geneMutation  The Gene Mutation to edit (we will keep a reference to it, so we can update it)
     */
    $scope.showEditGeneMutation = function(geneMutation) {
        if(angular.isUndefined(geneMutation.body) || angular.isUndefined(geneMutation.body.und)) {
            geneMutation.body = {};
            geneMutation.body.und = [];
            geneMutation.body.und[0] = {};
            geneMutation.body.und[0].value = "";
        }
        $scope.edit.geneMutation = geneMutation;
        $scope.edit.geneMutationDescription = angular.copy(geneMutation.body.und[0].value);
        $scope.openEditingPanel('edit-gene-mutation');
    }
    $scope.saveGeneMutationDescription = function(geneMutationDescription, masterGeneMutation) {
        masterGeneMutation.body.und[0].value = geneMutationDescription;
        $scope.closeEditingPanel();

        $http.post('?q=ajax/gene-mutation/' + masterGeneMutation.nid + '/description', {
            'description'   : geneMutationDescription
        }).success(function(data) {
        });
    }

    /**
     * Show the edit description panel
     */


    /* Add / Editing */
    $scope.editDescription = function() {
        $scope.editedDescription = $scope.master.gene.body.und[0].value || "";
        $scope.isEditingDescription = true;
    }

    /* Save edited descrption */
    $scope.cancelEditingDescription = function() {
        $scope.isEditingDescription = false;
    }

    $scope.saveEditedDescription = function(newDescription) {
        $scope.isEditingDescription = false;

        $scope.master.description = "Loading..."; //$scope.edit.description;

        // Save the description
        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/description', {
            'description'   : newDescription
        }).success(function(data) {
            $scope.master.gene.body.und[0].safe_value = data.safe_value;
            $scope.master.gene.body.und[0].value = data.value;
                console.log("descriptions");
        });
    }



    /**
     *
     */
    $scope.showEditDetails = function() {
        $scope.edit.locus = angular.copy($scope.master.gene.field_gene_locus.und[0].value);
        $scope.edit.mesh = angular.copy($scope.master.gene.field_gene_mesh.und[0].value);
        $scope.edit.omim = angular.copy($scope.master.gene.field_gene_omim.und[0].value);
        $scope.edit.umls = angular.copy($scope.master.gene.field_gene_umlscui.und[0].value);
        $scope.edit.uniprot = angular.copy($scope.master.gene.field_gene_uniprot.und[0].value);
        $scope.edit.accession = angular.copy($scope.master.gene.field_gene_accession.und[0].value);
        $scope.edit.entrez = angular.copy($scope.master.gene.field_gene_entrezgene.und[0].value);
        $scope.edit.refseq = angular.copy($scope.master.gene.field_gene_refseq.und[0].value);

        $scope.openEditingPanel('edit-details');
    }
    $scope.saveDetails = function() {
        $scope.master.gene.field_gene_locus.und[0].value = $scope.edit.locus;
        $scope.master.gene.field_gene_mesh.und[0].value = $scope.edit.mesh;
        $scope.master.gene.field_gene_omim.und[0].value = $scope.edit.omim;
        $scope.master.gene.field_gene_umlscui.und[0].value = $scope.edit.umls;
        $scope.master.gene.field_gene_uniprot.und[0].value = $scope.edit.uniprot;
        $scope.master.gene.field_gene_accession.und[0].value = $scope.edit.accession;
        $scope.master.gene.field_gene_entrezgene.und[0].value = $scope.edit.entrez;
        $scope.master.gene.field_gene_refseq.und[0].value = $scope.edit.refseq;

        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/details', {
            'locus': $scope.edit.locus,
            'mesh': $scope.edit.mesh,
            'omim': $scope.edit.omim,
            'umls': $scope.edit.umls,
            'uniprot': $scope.edit.uniprot,
            'accession': $scope.edit.accession,
            'entrez': $scope.edit.entrez,
            'refseq': $scope.edit.refseq
        }).success(function(data) {

        });

        $scope.closeEditingPanel();
    }

    $scope.showAddNewGeneMutation = function() {
        $scope.openEditingPanel('add-new-gene-mutation');
    }

    console.log($scope.master.gene.field_gene_gene_mutation);
    $scope.addGeneMutationToGene = function(title, gene) {
        $scope.closeEditingPanel();
        $http.post('?q=ajax/gene/' + gene.nid + '/gene-mutation', {
            "title": title
        }).success(function(data) {

            $scope.master.gene.field_gene_gene_mutation.push(data);

            console.log($scope.master.gene.field_gene_gene_mutation);
        });
    }

    /**
     * Show the Add Statement panel
     */
    $scope.saveStatement = function(statementText) {
        $scope.model.isAddingStatement = false;
        $scope.model.isloadingNewStatement = true;

        var newStatementText = angular.copy(statementText);
        $scope.model.newStatement = "";

        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/statement', {
            'statement': newStatementText
        }).success(function(data) {
            $scope.model.isloadingNewStatement = false;
            $scope.statements.unshift(data);
        });
        $scope.closeEditingPanel();
    }


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
        $scope.editingStatements = true;
        angular.forEach($scope.statements, function(statement, index) {
            if(statement.comments && statement.comments.length) {
                statement.showComments = true;
            }
        });
    }
    $scope.hideEditStatements = function() {
        $scope.editingStatements = false;
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
    }

}
function BoneDysplasiaCtrl($scope, $http, drupalContent, autocomplete) {

    $scope.model = {};
    $scope.showEditingPanel = false;
    $scope.editingPanel = "";

    $scope.clinicalFeatureDisplayLimit = 10;
    $scope.descriptionLength = 1300;

    $scope.addFeatureFormVisible = false;
    $scope.editClinicalFeaturesVisible = false;


    /* Bone Dysplasia stuff */
    $scope.boneDysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;
    $scope.moi = Drupal.settings.skeletome_builder.moi;
    $scope.omim = Drupal.settings.skeletome_builder.omim;
    $scope.similar = Drupal.settings.skeletome_builder.similar;

    $scope.statements = Drupal.settings.skeletome_builder.statements;
    $scope.clinicalFeatures = Drupal.settings.skeletome_builder.clinical_features;
    angular.forEach($scope.clinicalFeatures, function(clinicalFeature, key) {
        clinicalFeature.added = true;
    });

    $scope.genes = Drupal.settings.skeletome_builder.genes;


    $scope.tags = Drupal.settings.skeletome_builder.tags;

    /* All lists */
    $scope.allSources = Drupal.settings.skeletome_builder.all_sources;

    $scope.allGroups = Drupal.settings.skeletome_builder.all_groups;

    $scope.editedOMIM = $scope.omim;
    $scope.editedTags = angular.copy($scope.tags);



    $scope.init = function() {

        // Make a string of the synonyms
        if(angular.isDefined($scope.boneDysplasia.field_bd_synonym.und)) {
            $scope.synString = $scope.boneDysplasia.field_bd_synonym.und.reduce(function (previous, synonym) {
                var seperator = "";
                if(previous != "") {
                    seperator = ", ";
                }

                return previous + seperator + "'" + synonym.value + "'";
            }, "");
        }

        // Setup the editors
        $scope.editors = Drupal.settings.skeletome_builder.editors;

        // Setup the xrays
        if(angular.isDefined(Drupal.settings.skeletome_builder.bone_dysplasia.field_bd_xray_images.und)) {
            $scope.xrays = Drupal.settings.skeletome_builder.bone_dysplasia.field_bd_xray_images.und;
        } else {
            $scope.xrays = [];
        }

        // X-Ray display limit
        $scope.xrayDefaultDisplayLimit = 9;
        $scope.xrayDisplayLimit = $scope.xrayDefaultDisplayLimit;
    }

    /* Actions */
    $scope.editDescription = function() {
        $scope.editingPanel = true;
        $scope.editing = "description";
    }

    $scope.createNewBoneDysplasia = function(title) {
        $http.post('?q=ajax/bone-dysplasia/new', {
            'title': title
        }).success(function(data) {
            // do redirect here
            console.log(data);
            window.location.href = "?q=node/" + data.nid;

        });
    }
    /*
        Open / Close Panel
     */
    $scope.openEditingPanel = function(panel) {
        $scope.showEditingPanel = true;
        $scope.editingPanel = panel;
    }
    $scope.closeEditingPanel = function() {
        $scope.showEditingPanel = false;
    }

    $scope.edit = {};

    $scope.showEditXRays = function() {
        /* Set up the selected moi for the dropdown */

        angular.forEach($scope.xrays, function(xray, key) {
            xray.added = true;
        });
        $scope.editedXRays = angular.copy($scope.xrays);
        $scope.openEditingPanel('edit-xrays');
    }
    $scope.removeXRay = function(xray) {
        xray.added = false;
        angular.forEach($scope.xrays, function(existingXray, index) {
            if(existingXray.fid == xray.fid) {
                $scope.xrays.splice(index, 1);
            }
        });
        $http.post('?q=ajax/bone-dysplasia/' + $scope.boneDysplasia.nid + '/xray/' + xray.fid + '/remove', {
        }).success(function(data) {
            console.log(data);
        });
    }
    $scope.readdXRay = function(xray) {
        xray.added = true;
        $scope.xrays.push(xray);
        $http.post('?q=ajax/bone-dysplasia/' + $scope.boneDysplasia.nid + '/xray/' + xray.fid + '/add', {
        }).success(function(data) {
        });
    }

    $scope.showEditDetails = function() {
        $scope.mois = Drupal.settings.skeletome_builder.all_mois;

        /* Set up the selected moi for the dropdown */
        if($scope.moi) {
            angular.forEach($scope.mois, function(moi, key) {
                if(moi.tid == $scope.moi.tid) {
                    $scope.edit.editedMoi = moi;
                    console.log("edit moi set to");
                    return false;
                }
            });
        }


        $scope.openEditingPanel('edit-details');
    }

    $scope.saveDetails = function(editedOMIM, editedMoi) {

        // if there is no moi, or its changed, save it

        $http.post('?q=ajax/bone-dysplasia/' + $scope.boneDysplasia.nid + '/details', {
            'moiTid': editedMoi.tid,
            'omim': editedOMIM
        }).success(function(data) {
        });

        // Check if the values have changed
        $scope.moi = editedMoi
        $scope.omim = editedOMIM;

        $scope.closeEditingPanel();

    }



    /* Add / Editing */
    $scope.editDescription = function() {
        $scope.editedDescription = $scope.boneDysplasia.body.und[0].value;
        $scope.isEditingDescription = true;
    }

    /* Save edited descrption */
    $scope.cancelEditingDescription = function() {
        $scope.isEditingDescription = false;
    }
    $scope.saveEditedDescription = function(newDescription) {
        $scope.isEditingDescription = false;

        $scope.boneDysplasia.body.und[0].safe_value = "Loading...";
        $scope.boneDysplasia.body.und[0].isLoading = true;


        // Save the description
        $http.post('?q=ajax/bone-dysplasia/description', {
            'id':$scope.boneDysplasia.nid,
            'description': newDescription
        }).success(function(data) {
            $scope.boneDysplasia.body.und[0].isLoading = false;
            $scope.boneDysplasia.body['und'][0]['safe_value'] = data.safe_value;
                $scope.boneDysplasia.body['und'][0]['value'] = data.value;
            // this callback will be called asynchronously
            // when the response is available
        });
    }

    /* Clinical Features */
    $scope.showEditClinicalFeatures = function() {
        $scope.editingClinicalFeatures = angular.copy($scope.clinicalFeatures);
        $scope.openEditingPanel('edit-features');
    }

// infantile mus

    /* Autocomplete for clinical features */
    $scope.searchForClinicalFeatures = function(name) {

        console.log("searching for clinical feature :" + name + ":");
        if(name == "") {
            $scope.editingClinicalFeatures = angular.copy($scope.clinicalFeatures);
            console.log("inpuit box is empty");
            return;
        }

        autocomplete.clinicalFeatures(name).success(function(data) {
            if(!$scope.editClinicalFeatureSearch) {
                return;
            }
            $scope.editingClinicalFeatures = drupalContent.mergeTermArrays($scope.clinicalFeatures, data);
        });
    }


    /* Add Clinical Feature to Bone Dysplasia */
    $scope.addClinicalFeature = function(newClinicalFeature, boneDysplasia) {
        newClinicalFeature.added = true;
        $scope.clinicalFeatures.push(newClinicalFeature);
        $http.post('?q=ajax/bone-dysplasia/clinical-feature/add', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'clinicalFeatureTid': newClinicalFeature.tid
        }).success(function(data) {
            newClinicalFeature.information_content = data.information_content;
        });
    }
    /* Remove Clinical Feature from Bone Dysplasia */
    $scope.removeClinicalFeature = function(featureToRemove, boneDysplasia) {
        featureToRemove.added = false;

        /* Remove it form the current list */
        angular.forEach($scope.clinicalFeatures, function(currentClinicalFeature, index){
            if(featureToRemove.tid == currentClinicalFeature.tid) {
                $scope.clinicalFeatures.splice(index, 1);
                return;
            }
        });
        /* Send it to the server */
        $http.post('?q=ajax/bone-dysplasia/clinical-feature/remove', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'clinicalFeatureTid': featureToRemove.tid
        }).success(function(data) {
        });
    }

    /**
     * Checks if gene search matches gene/gene mutation name
     * @param gene
     * @return {Boolean}
     */
    $scope.filterExistingGenes = function(gene){
        if($scope.editGeneSearch && gene) {
            var name = (gene.gene.title + " " + gene.gene_mutation.title).toLowerCase();
            var search = $scope.editGeneSearch.toLowerCase();
            return name.indexOf(search) !== -1;
        }
        return true;
    }

    $scope.showEditGenes = function() {
        // Mark all the current genes as added
        angular.forEach($scope.genes, function(gene, key) {
            gene.added = true;
//            angular.forEach(gene.field_gene_gene_mutation, function(geneMutation, key) {
//                geneMutation.added = true;
//            });
        });
//        if($scope.genes.length == 1) {
//            // When there is only 1 gene, open it up automatically
//            $scope.genes[0].showGeneMutations = true;
//        }

        // Make a copy so we can play around
        $scope.editingGenes = angular.copy($scope.genes);

        $scope.openEditingPanel('edit-genes');
    }
    /**
     * Searches for a Gene/Gene Mutation matching a search term
     * @param editGeneSearch    The Search term string to match
     */
    $scope.searchForGenes = function(editGeneSearch) {
        $scope.editGeneLoading++;
        $scope.showAddNewGeneForm = false;

        // Whenever there are no values, we show the current ones
        // Otherwise, its just the search
        // Might have a slight delay, but we will see.

        if(!$scope.editGeneSearch) {
            $scope.editGeneLoading = 0;
            console.log("input cleared");
            $scope.editingGenes = angular.copy($scope.genes);
            return;
        };

        $http.get('?q=ajax/search/gene/' + editGeneSearch).success(function(data) {
            $scope.editGeneLoading--;
            console.log("Request came back " + editGeneSearch);

            if(editGeneSearch != $scope.editGeneSearch) {
                return;
            }

            // We got back the correct search
            // Lets see if we found any results
            if(data.length == 0) {
                // No results
                // Show add gene form
                $scope.showAddNewGeneForm = true;
            }

            angular.forEach(data, function(searchGene, searchGenekey) {
                // Check if we already have it, if so, we mark it as added
                angular.forEach($scope.genes, function(addedGene, addedGeneKey) {
                    if(searchGene.nid == addedGene.nid) {
                        // We alredy have that gene, now check the individual gene mutations
                        searchGene.added = true;

//                        angular.forEach(searchGene.field_gene_gene_mutation, function(searchGeneMutation, searchGeneMutationKey) {
//                            angular.forEach(addedGene.field_gene_gene_mutation, function(addedGeneMutation, addedGeneMutationKey) {
//                                if(searchGeneMutation.nid == addedGeneMutation.nid) {
//                                    searchGeneMutation.added = true;
//                                }
//                            });
//                        });
                    }
                });
            });

//            if(data.length == 1) {
//                // When there is only 1 gene, open it up automatically
//                data[0].showGeneMutations = true;
//            }
            $scope.editingGenes = data;

        });
    }

    $scope.addNewGeneToBoneDysplasia = function(geneName, boneDysplasia) {
        $scope.showAddNewGeneForm = false;

        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene/add', {
            'geneName': geneName
        }).success(function(gene) {

            $scope.editingGenes = [
                gene
            ];
            $scope.genes.push(gene);
            gene.added = true;
        });
    }

    $scope.addGeneToBoneDysplasia = function(gene, boneDysplasia) {
        gene.added = true;
        $scope.genes.push(gene);
        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene/add', {
            'geneNid': gene.nid
        }).success(function() {

        });
    }
    $scope.removeGeneFromBoneDysplasia = function(gene, boneDysplasia) {
        gene.added = false;
        angular.forEach($scope.genes, function(addedGene, index) {
            if(addedGene.nid == gene.nid) {
                $scope.genes.splice(index, 1);
            }
        });

        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene/' + gene.nid + '/remove').success(function() {

        });
    }



    $scope.addNewGeneMutationToBoneDysplasia = function(geneMutationTitle, gene, boneDysplasia) {
        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene-mutation/add', {
            'geneMutationTitle': geneMutationTitle,
            'geneNid': gene.nid
        }).success(function(geneMutation) {
            // Add it to the existing data

            angular.forEach($scope.editingGenes, function(editingGene, index) {
                if(editingGene.nid == gene.nid) {
                    // Already exists, lets add the mutation here
                    editingGene.field_gene_gene_mutation.push(geneMutation);
                }
            });

            $scope.addGeneMutationHelper(geneMutation, gene);

        });
    }

    /**
     * Adds a Gene Mutations to a Bone Dysplasia
     * @param geneMutation      The Gene/Mutation Object
     * @param boneDysplasia     The Bone Dysplasia to add it to
     */
    $scope.addGeneMutation = function(geneMutation, gene, boneDysplasia) {
        // Add it to the existing data
        $scope.addGeneMutationHelper(geneMutation, gene);

        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene-mutation/add', {
            'geneMutationNid': geneMutation.nid
        }).success(function(data) {
        });
    }
    $scope.addGeneMutationHelper = function(geneMutation, gene) {
        geneMutation.added = true;

        // Lets check if the gene mutations gene, is already in the genes list
        var addedToExisting = false;
        angular.forEach($scope.genes, function(addedGene, index) {
            if(addedGene.nid == gene.nid) {
                // Already exists, lets add the mutation here
                addedGene.field_gene_gene_mutation.push(geneMutation);
                addedToExisting = true;
            }
        });
        // We dont have the gene already, so just make a new one and add it in
        if(!addedToExisting) {
            var geneToAdded = angular.copy(gene);
            geneToAdded.field_gene_gene_mutation = [
                geneMutation
            ];
            $scope.genes.push(geneToAdded);
        }
    }

    /**
     * Removes a Gene Mutation from a Bone Dysplasia
     * @param geneMutation
     * @param boneDysplasia
     */
    $scope.removeGeneMutation = function(geneMutation, gene, boneDysplasia) {
        geneMutation.added = false;

        angular.forEach($scope.genes, function(addedGene, key) {
            if(addedGene.nid == gene.nid) {
                // remove it from this one
                angular.forEach(addedGene.field_gene_gene_mutation, function(addedGeneMutation, geneMutationKey) {
                    // find the gene mutation
                    if(addedGeneMutation.nid == geneMutation.nid) {
                        addedGene.field_gene_gene_mutation.splice(geneMutationKey, 1);
                    }
                });
                if(addedGene.field_gene_gene_mutation.length == 0) {
                    // there are no more genes mutations remove it, so remove it
                    $scope.genes.splice(key, 1);
                }
            }
        });

        /* Send it to the server */
        $http.get('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene-mutation/remove/' + geneMutation.nid).success(function(data) {
            console.log(data);
        });
    }

    /* Show Add Form */
    $scope.increaseClinicalFeatureDisplayLimit = function() {
        $scope.clinicalFeatureDisplayLimit += 10;
    }



    $scope.saveStatement = function(newStatement) {
        // Set it as loading the statement
        $scope.model.isAddingStatement = false;
        $scope.model.isloadingNewStatement = true;

        // Make a copy of the text and clear out the input
        var newStatementText = angular.copy(newStatement);
        $scope.model.newStatement = "";

        $http.post('?q=ajax/bone-dysplasia/' + $scope.boneDysplasia.nid + '/statement/add', {
            'statement': newStatementText
        }).success(function(data) {
            $scope.model.isloadingNewStatement = false;

            // this callback will be called asynchronously
            // when the response is available
            $scope.statements.unshift(data);
        });
    }

//    /* Autocomplete for genes */
//    $scope.autocompleteGroups = [];
//    $scope.searchForGroups = function(groupName) {
//        if(groupName == "") {
//            $scope.autocompleteGroups = [];
//            return;
//        }
//        $http.get('?q=ajax/autocomplete/groups/' + groupName).success(function(data, status, headers, config) {
//            $scope.autocompleteGroups = data;
//        });
//    }

    /* Add group to groups to add */
//    $scope.groupsToAdd = [];
//    $scope.addGroupToGroupsAdd = function(group, groupsToAdd) {
//        $scope.groupsToAdd.push(group);
//    }
//    $scope.addGroupsToBoneDysplasia = function(groupsToAdd, boneDysplasia) {
//        angular.forEach(groupsToAdd, function(group, index){
//            $scope.tags.push(group);
//        });
//        $http.post('?q=ajax/bone-dysplasia/groups/edit', {
//            'boneDysplasiaNid': boneDysplasia.nid,
//            'groups': $scope.tags
//        }).success(function(data) {
//            alert("edited group saved");
//        });
//    }



    /* Edit OMIM */
    $scope.saveEditedOMIM = function(editedOMIM, boneDysplasia) {
        $scope.omim = editedOMIM;

        $http.post('?q=ajax/bone-dysplasia/omim/', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'omim': editedOMIM
        }).success(function(data) {
            alert("edited omim saved");
        });
    }

    /* Edit Mode of Inheritance */
    $scope.saveEditedMoi = function(editedMoi, boneDysplasia) {
        $scope.moi = editedMoi;

        $http.post('?q=ajax/bone-dysplasia/moi', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'moiTid': editedMoi.tid
        }).success(function(data) {
            alert("edited moi saved");
        });
    }

    $scope.removeEditedTag = function(index, editedTags) {
        editedTags.splice(index, 1);
    }
    $scope.saveEditedGroups = function(editedTags, boneDysplasia) {
        $scope.tags = editedTags;

        $http.post('?q=ajax/bone-dysplasia/groups/edit', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'groups': editedTags
        }).success(function(data) {
            alert("edited group saved");
        });
    }

    $scope.createNewGroupAndAdd = function(newGroupSource, newGroupSourceRelease, newGroupName, newGroupDescription, boneDysplasia) {
        $http.post('?q=ajax/bone-dysplasia/groups/new', {
            'boneDysplasiaNid': boneDysplasia.nid,
            'groupSourceTid': newGroupSource.tid,
            'groupSourceReleaseTid':    newGroupSourceRelease.tid,
            'groupNameTid': newGroupName.tid,
            'description' : newGroupDescription
        }).success(function(data) {
            $scope.tags.push(data);
            alert("new group saved");
        });
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
    $scope.allSources = Drupal.settings.skeletome_builder.all_sources;
    $scope.topContributors = Drupal.settings.skeletome_builder.top_contributors;
}




