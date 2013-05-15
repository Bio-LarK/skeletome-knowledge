function FrontPageCtrl($scope) {
    $scope.allClinicalFeatures = Drupal.settings.skeletome_builder.all_clinical_features;
    $scope.allBoneDysplasias = Drupal.settings.skeletome_builder.all_bone_dysplasias;
    $scope.allGroups = Drupal.settings.skeletome_builder.all_groups;
    $scope.allGenes = Drupal.settings.skeletome_builder.all_genes;
    $scope.latestReleases = Drupal.settings.skeletome_builder.latest_releases;
    $scope.topContributors = Drupal.settings.skeletome_builder.top_contributors;

    $scope.queryHolder = {};
    $scope.queryHolder.selectedIndex = -1;

    $scope.characterIndex = 0;

    $scope.isShowingInstructions = false;
    $scope.showInstructions = function() {
        $scope.isShowingInstructions = true;
    }
    $scope.typeInTerm = function(term) {

        $scope.queryHolder.isShowingSuggestions = true;



        var searchTerm = term;
        var myInterval = setInterval(function() {
            $scope.$apply(function() {
                $scope.queryHolder.query = searchTerm.substring(0, ++$scope.characterIndex);
                console.log("logging", $scope.queryHolder);
            });

            console.log($scope.queryHolder.query);
            if($scope.characterIndex == searchTerm.length) {
                $scope.characterIndex = 0;
//                jQuery('.navsearch-query').focus();
                clearInterval(myInterval);
            }
        }, 100);
    }

    $scope.doFind = function() {
        $scope.typeInTerm("Achondroplasia");
    }

    $scope.doCombine = function() {
        $scope.typeInTerm("Dwarfism; Kyphosis;");
    }

    $scope.doSearch = function() {
        $scope.typeInTerm("punctata");
    }
}
