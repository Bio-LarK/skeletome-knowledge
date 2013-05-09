function FrontPageCtrl($scope) {
    $scope.allClinicalFeatures = Drupal.settings.skeletome_builder.all_clinical_features;
    $scope.allBoneDysplasias = Drupal.settings.skeletome_builder.all_bone_dysplasias;
    $scope.allGroups = Drupal.settings.skeletome_builder.all_groups;
    $scope.allGenes = Drupal.settings.skeletome_builder.all_genes;
    $scope.latestReleases = Drupal.settings.skeletome_builder.latest_releases;
    $scope.topContributors = Drupal.settings.skeletome_builder.top_contributors;
}
