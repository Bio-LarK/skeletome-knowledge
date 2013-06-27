function BoneDysplasiaCtrl($scope, $http, drupalContent, autocomplete) {

    $scope.model = {};
    $scope.showEditingPanel = false;
    $scope.editingPanel = "";

    $scope.descriptionLength = 1300;

    $scope.addFeatureFormVisible = false;

    /* Bone Dysplasia stuff */
    $scope.model.boneDysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;

    $scope.groupBoneDysplasias = Drupal.settings.skeletome_builder.group_bone_dysplasias;

    $scope.statements = [];
    angular.forEach(Drupal.settings.skeletome_builder.statements, function(statement, index) {
        if(!angular.isDefined(statement.field_statement_approved_time.und)) {
            $scope.statements.push(statement);
        }
    });


    $scope.genes = Drupal.settings.skeletome_builder.genes;

    $scope.tags = Drupal.settings.skeletome_builder.tags;

    /* All lists */
    $scope.allSources = Drupal.settings.skeletome_builder.all_sources;

    $scope.allGroups = Drupal.settings.skeletome_builder.all_groups;

    $scope.editedOMIM = $scope.omim;
    $scope.editedTags = angular.copy($scope.tags);



    $scope.init = function() {
        $scope.IS_DISPLAYING = "isDisplaying";
        $scope.IS_EDITING = "isEditing";
        $scope.IS_LOADING = "isLoading";

        // Setup the description
        $scope.model.edit = {};

        $scope.description = angular.isDefined($scope.model.boneDysplasia.body.und) ? $scope.model.boneDysplasia.body.und[0] : {'value': "", 'safe_value': ""};
        $scope.description.url = '?q=ajax/bone-dysplasia/description';

        // Reference to source for description
        $scope.provider = Drupal.settings.skeletome_builder.provider;
        $scope.reference = Drupal.settings.skeletome_builder.reference;

        // Make a string of the synonyms
        if(angular.isDefined($scope.model.boneDysplasia.field_bd_synonym.und)) {
            $scope.synString = $scope.model.boneDysplasia.field_bd_synonym.und.reduce(function (previous, synonym) {
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
        $scope.setupXRays();
        $scope.setupDetails();
        $scope.setupClinicalFeatures();
        $scope.setupGenes();
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


    /**
     * Edit Details
     */
    $scope.setupDetails = function() {
        $scope.model.detailsState = $scope.IS_DISPLAYING;
        $scope.moi = Drupal.settings.skeletome_builder.moi;
        $scope.omim = Drupal.settings.skeletome_builder.omim;
    }
    $scope.editDetails = function() {
        $scope.model.detailsState = $scope.IS_EDITING;
        $scope.model.edit.omim = angular.copy($scope.omim);
        $scope.model.edit.allMois = Drupal.settings.skeletome_builder.all_mois;

        if($scope.moi) {
            angular.forEach($scope.model.edit.allMois, function(moi, key) {
                if(moi.tid == $scope.moi.tid) {
                    $scope.model.edit.moi = moi;
                    console.log("edit moi set to");
                    return false;
                }
            });
        }
    }
    $scope.saveDetails = function() {

        $scope.model.detailsState = $scope.IS_LOADING;

        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/details', {
            'moiTid': $scope.model.edit.moi.tid,
            'omim': $scope.model.edit.omim
        }).success(function(data) {
            $scope.model.detailsState = $scope.IS_DISPLAYING;
            $scope.omim = $scope.model.edit.omim;
            $scope.moi = $scope.model.edit.moi;
        });
    }
    $scope.cancelDetails = function() {
        $scope.model.detailsState = $scope.IS_DISPLAYING;
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

//    $scope.saveDetails = function(editedOMIM, editedMoi) {
//
//        // if there is no moi, or its changed, save it
//
//        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/details', {
//            'moiTid': editedMoi.tid,
//            'omim': editedOMIM
//        }).success(function(data) {
//            });
//
//        // Check if the values have changed
//        $scope.moi = editedMoi
//        $scope.omim = editedOMIM;
//
//        $scope.closeEditingPanel();
//
//    }

    $scope.setupGenes = function() {
        $scope.model.genesState = $scope.IS_DISPLAYING;
        $scope.model.geneLookingIsLoading = false;
    }
    $scope.editGenes = function() {
        $scope.model.edit.genes = angular.copy($scope.genes);
        $scope.model.genesState = $scope.IS_EDITING;
    }
    $scope.cancelGenes = function() {
        $scope.model.genesState = $scope.IS_DISPLAYING;
    }
    $scope.showAddGene = function() {
        $scope.model.isShowingAddGene = true;
    }
    $scope.saveGenes = function(genes) {
        $scope.model.genesState = $scope.IS_LOADING;
        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/genes', {
            genes: genes
        }).success(function(data) {
            $scope.genes = data;
            $scope.model.genesState = $scope.IS_DISPLAYING;

        });
    }

    /**
     * Edit Xrays
     */
    $scope.setupXRays = function() {
        $scope.model.xrayState = $scope.IS_DISPLAYING;
        if(!angular.isDefined($scope.model.boneDysplasia.field_bd_xray_images.und)) {
            $scope.model.xrays = [];
        } else {
            $scope.model.xrays = $scope.model.boneDysplasia.field_bd_xray_images.und;
        }
        // X-Ray display limit
        $scope.xrayDefaultDisplayLimit = 9;
        $scope.xrayDisplayLimit = $scope.xrayDefaultDisplayLimit;
    }

    $scope.editXRays = function() {
        // Copy the xray
        $scope.model.edit.xrays = angular.copy($scope.model.xrays);
        angular.forEach($scope.model.edit.xrays, function(xray, index) {
            xray.added = true;
        });
        $scope.model.xrayState = $scope.IS_EDITING;
    }
    $scope.cancelXRays = function() {
        $scope.model.xrayState = $scope.IS_DISPLAYING;
    }
    $scope.saveXRays = function(profile) {
        $scope.model.xrayState = $scope.IS_LOADING;

        $scope.model.xrays = $scope.model.edit.xrays;
        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/xrays', {
            'xrays': $scope.model.xrays
        }).success(function(data) {
            $scope.model.xrayState = $scope.IS_DISPLAYING;
        });
    }

    $scope.removeXRay = function(xray) {
        var index = $scope.model.edit.xrays.indexOf(xray);
        $scope.model.edit.xrays.splice(index, 1);
    }

    /**
     * Setup the Clinical Features
     */
    $scope.setupClinicalFeatures = function() {
        $scope.model.clinicalFeatures = Drupal.settings.skeletome_builder.clinical_features;
        $scope.model.clinicalFeaturesState = $scope.IS_DISPLAYING;

        // X-Ray display limit
        $scope.model.clinicalFeaturesDefaultDisplayLimit = 5;
        $scope.model.clinicalFeaturesDisplayLimit = $scope.clinicalFeaturesDefaultDisplayLimit;

    }

    /**
     * Edit clinical features
     */
    $scope.editClinicalFeatures = function() {
        $scope.model.edit.clinicalFeatures = angular.copy($scope.model.clinicalFeatures);
        angular.forEach($scope.model.edit.clinicalFeatures, function(clinicalFeature, index) {
            clinicalFeature.added = true;
        });
        $scope.model.clinicalFeaturesState = $scope.IS_EDITING;
        $scope.model.edit.clinicalFeaturesSearchResultsCounter = 0;
    }
    $scope.searchForClinicalFeature = function(query) {
        if(query && query.length) {
            // there is a query
            $scope.model.edit.clinicalFeaturesSearchResultsCounter++;
            $scope.model.edit.clinicalFeaturesSearchResultsState = $scope.IS_LOADING;
            $http.get('?q=ajax/clinical-features/search/' + query).success(function(data) {

                if(query != $scope.model.edit.addClinicalFeatureQuery) {
                    $scope.model.edit.clinicalFeaturesSearchResultsCounter--;
                    return;
                }
                // loop through results we got back and add add/remove buttons
                var stuff = [];

                angular.forEach(data, function(result, index1) {
                    var found = false;
                    angular.forEach($scope.model.edit.clinicalFeatures, function(clinicalFeature, index) {
                        if(result.tid == clinicalFeature.tid) {
//                            data.splice(index1, 1);
//                            result.added = clinicalFeature.added;
                            found = true;
                        }
                    });
                    if(!found) {
                        stuff.push(result);
                    }
                });

                $scope.model.edit.clinicalFeaturesSearchResults = stuff;
                $scope.model.edit.clinicalFeaturesSearchResultsCounter--;
                if($scope.model.edit.clinicalFeaturesSearchResultsCounter == 0) {
                    $scope.model.edit.clinicalFeaturesSearchResultsState = $scope.IS_DISPLAYING;
                }
            });
        } else {
            $scope.model.edit.clinicalFeaturesSearchResultsCounter = 0;
        }
    }
    $scope.removeClinicalFeature = function(clinicalFeature) {
        var index = $scope.model.edit.clinicalFeatures.indexOf(clinicalFeature);
        $scope.model.edit.clinicalFeatures.splice(index, 1);
    }

    $scope.addClinicalFeature = function(clinicalFeature) {
        clinicalFeature.added = true;
        $scope.model.edit.clinicalFeatures.push(clinicalFeature);
    }

    /**
     * Save Clinical features
     */
    $scope.saveClinicalFeatures = function() {
        $scope.model.clinicalFeaturesState = $scope.IS_LOADING;
        $scope.model.edit.clinicalFeaturesSearchResultsState = $scope.IS_DISPLAYING;
//        Remove all xrays in the edit, that are set to 'false' for added
        var clinicalFeatures = [];
        angular.forEach($scope.model.edit.clinicalFeatures, function(clinicalFeature, index) {
            if(clinicalFeature.added) {
                clinicalFeatures.push(clinicalFeature);
            }
        });

        $scope.model.clinicalFeatures = clinicalFeatures;
        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/clinical-features', {
            'clinical_features': $scope.model.clinicalFeatures
        }).success(function(data) {
            $scope.model.clinicalFeaturesState = $scope.IS_DISPLAYING;
        });
    }
    $scope.cancelClinicalFeatures = function() {
        $scope.model.edit.clinicalFeaturesSearchResultsState = $scope.IS_DISPLAYING;
        $scope.model.clinicalFeaturesState = $scope.IS_DISPLAYING;
    }

    $scope.showAddClinicalFeature = function() {
        $scope.isAddingClinicalFeature = true;
        $scope.model.edit.addClinicalFeatureQuery = "";
        $scope.model.edit.clinicalFeaturesSearchResults = [];
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
//    $scope.addClinicalFeature = function(newClinicalFeature, boneDysplasia) {
//        newClinicalFeature.added = true;
//        $scope.clinicalFeatures.push(newClinicalFeature);
//        $http.post('?q=ajax/bone-dysplasia/clinical-feature/add', {
//            'boneDysplasiaNid': boneDysplasia.nid,
//            'clinicalFeatureTid': newClinicalFeature.tid
//        }).success(function(data) {
//                newClinicalFeature.information_content = data.information_content;
//            });
//    }
    /* Remove Clinical Feature from Bone Dysplasia */
//    $scope.removeClinicalFeature = function(featureToRemove, boneDysplasia) {
//        featureToRemove.added = false;
//
//        /* Remove it form the current list */
//        angular.forEach($scope.clinicalFeatures, function(currentClinicalFeature, index){
//            if(featureToRemove.tid == currentClinicalFeature.tid) {
//                $scope.clinicalFeatures.splice(index, 1);
//                return;
//            }
//        });
//        /* Send it to the server */
//        $http.post('?q=ajax/bone-dysplasia/clinical-feature/remove', {
//            'boneDysplasiaNid': boneDysplasia.nid,
//            'clinicalFeatureTid': featureToRemove.tid
//        }).success(function(data) {
//            });
//    }

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

    }

    $scope.addNewGeneToBoneDysplasia = function(geneName, boneDysplasia) {
        $scope.showAddNewGeneForm = false;
        $scope.editGeneLoading = 1;
        $http.post('?q=ajax/bone-dysplasia/' + boneDysplasia.nid + '/gene/add', {
            'geneName': geneName
        }).success(function(gene) {
                $scope.editGeneLoading = 0;
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


    /**
     *
     * @param statements
     */
    $scope.saveStatements = function(statements) {
        $scope.model.statementsState = "isLoading";
        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/statements', {
            'statements': statements
        }).success(function(data) {
            $scope.model.statementsState = "isDisplaying";
            // this callback will be called asynchronously
            // when the response is available
            $scope.statements = data;
        });
    }

    $scope.saveStatement = function(newStatement) {
        // Set it as loading the statement
        $scope.model.statementsState = "isLoading";

        // Make a copy of the text and clear out the input
        var newStatementText = angular.copy(newStatement);
        $scope.model.newStatement = "";

        $http.post('?q=ajax/bone-dysplasia/' + $scope.model.boneDysplasia.nid + '/statement', {
            'text': newStatementText
        }).success(function(data) {
            $scope.model.statementsState = "isDisplaying";
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