function GroupCtrl($scope) {
    $scope.members = Drupal.settings.skeletome_builder.members;
    $scope.clinicalFeatures = Drupal.settings.skeletome_builder.clinical_features;

    console.log($scope.clinicalFeatures);
    $scope.genes = Drupal.settings.skeletome_builder.genes;
    $scope.sourceRelease = Drupal.settings.skeletome_builder.source_release;
    $scope.source = Drupal.settings.skeletome_builder.source;
    $scope.groupName = Drupal.settings.skeletome_builder.group_name;
}