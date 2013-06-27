function GeneCtrl($scope, $http) {
    $scope.master = {};
    $scope.edit = {};
    $scope.view = {};
    $scope.model = {};

    $scope.view.defaultDescriptionLength = 500;
    $scope.view.descriptionLength = $scope.view.defaultDescriptionLength;





    $scope.init = function() {
        /**
         * Set up masters
         */
        $scope.model.edit = {};

        $scope.master.gene = Drupal.settings.skeletome_builder.gene;
        // Fix up somep roblem with drupal
        $scope.master.gene.field_gene_gene_mutation = jQuery.map($scope.master.gene.field_gene_gene_mutation, function (value, key) { return value; });

        // The description
        $scope.description = angular.isDefined($scope.master.gene.body.und) ? $scope.master.gene.body.und[0] : {'value': "", 'safe_value': ""};
        $scope.description.url = '?q=ajax/gene/' + $scope.master.gene.nid + '/description';

        $scope.boneDysplasias = Drupal.settings.skeletome_builder.bone_dysplasias;
        $scope.boneDysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;
        $scope.statements = Drupal.settings.skeletome_builder.statements;

        // Setup the editors
        $scope.editors = Drupal.settings.skeletome_builder.editors;
    }




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


//    /* Add / Editing */
//    $scope.editDescription = function() {
//        $scope.editedDescription = $scope.master.gene.body.und[0].value || "";
//        $scope.isEditingDescription = true;
//    }
//
//    /* Save edited descrption */
//    $scope.cancelEditingDescription = function() {
//        $scope.isEditingDescription = false;
//    }
//
//    $scope.saveEditedDescription = function(newDescription) {
//        $scope.isEditingDescription = false;
//
//        $scope.master.description = "Loading..."; //$scope.edit.description;
//
//        // Save the description
//        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/description', {
//            'description'   : newDescription
//        }).success(function(data) {
//                $scope.master.gene.body.und[0].safe_value = data.safe_value;
//                $scope.master.gene.body.und[0].value = data.value;
//                console.log("descriptions");
//            });
//    }



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

    $scope.addGeneMutationToGene = function(title, gene) {
        $scope.closeEditingPanel();
        $http.post('?q=ajax/gene/' + gene.nid + '/gene-mutation', {
            "title": title
        }).success(function(data) {

                $scope.master.gene.field_gene_gene_mutation.push(data);

                console.log($scope.master.gene.field_gene_gene_mutation);
            });
    }

    $scope.saveStatements = function(statements) {
        $scope.model.statementsState = "isLoading";
        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/statements', {
            'statements': statements
        }).success(function(data) {
            $scope.model.statementsState = "isDisplaying";
            // this callback will be called asynchronously
            // when the response is available
            $scope.statements = data;
        });
    }

    /**
     * Show the Add Statement panel
     */
    $scope.saveStatement = function(statementText) {
        // Set it as loading the statement
        $scope.model.statementsState = "isLoading";

        var newStatementText = angular.copy(statementText);
        $scope.model.newStatement = "";

        $http.post('?q=ajax/gene/' + $scope.master.gene.nid + '/statement', {
            'text': newStatementText
        }).success(function(data) {
            $scope.model.statementsState = "isDisplaying";
            $scope.statements.unshift(data);
        });
    }


}