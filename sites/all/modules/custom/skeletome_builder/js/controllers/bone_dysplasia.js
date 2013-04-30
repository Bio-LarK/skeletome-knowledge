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

        // Setup the description
        $scope.description = $scope.boneDysplasia.body.und[0];
        $scope.description.url = '?q=ajax/bone-dysplasia/description';

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