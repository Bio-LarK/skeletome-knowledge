function ClinicalFeatureCtrl($scope) {
    $scope.boneDysplasiaDisplayLimit = 10;
    $scope.boneDysplasias = Drupal.settings.skeletome_builder.bone_dysplasias;
    $scope.clinicalFeature = Drupal.settings.skeletome_builder.clinical_feature;
    $scope.genes = Drupal.settings.skeletome_builder.genes;
    $scope.informationContent = Drupal.settings.skeletome_builder.information_content;
    $scope.bone_dysplasia = Drupal.settings.skeletome_builder.bone_dysplasia;
}