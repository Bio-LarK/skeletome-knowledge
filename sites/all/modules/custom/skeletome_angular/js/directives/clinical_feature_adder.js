myApp.directive('clinicalFeatureAdder', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {       // create an isolate scope
        },
        templateUrl: Drupal.settings.skeletome_builder.base_url + '/sites/all/modules/custom/skeletome_builder/partials/clinical_feature_adder.php',
        controller: function($scope, $http) {
            $scope.test = "hello world";
            $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

            $scope.currentClinicalFeature = {
                'name': 'Features'
            };
            $scope.nextClinicalFeature = null;
            $scope.previousClinicalFeature = null;

            // Get some all the root level elements, add them to the 'root 'current clinical feature
            $http.get('?q=ajax/autocomplete/clinical-feature/a').success(function(children) {

                // Set all their parents
                angular.forEach(children, function(feature, index) {
                    feature.parentClinicalFeature = $scope.currentClinicalFeature;
                });

                $scope.currentClinicalFeature.childrenClinicalFeatures = children;
            });

            $scope.selectNextClinicalFeature = function(clinicalFeature) {
                $scope.nextClinicalFeature = clinicalFeature;

                // Have we loaded in its children yet?
                if($scope.nextClinicalFeature.children) {
                    // already has children loaded
                    console.log("has children, scroll to left");
                    $scope.animate = "left";
                } else {
                    var char = Math.random().toString(36).substring(7).substr(0,1);
                    clinicalFeature.isLoading = true;
                    $http.get('?q=ajax/autocomplete/clinical-feature/' + char).success(function(children) {
                        angular.forEach(children, function(feature, index) {
                            feature.parentClinicalFeature = $scope.nextClinicalFeature;
                        });
                        clinicalFeature.isLoading = false;
                        $scope.nextClinicalFeature.childrenClinicalFeatures = children;
                        $scope.animate = "left";
                    });
                }

            }

            $scope.selectPreviousClinicalFeature = function(clinicalFeature) {
                $scope.previousClinicalFeature = clinicalFeature;
                console.log("previous feature", $scope.clinicalFeature);
                $scope.animate = "right";
            }

        },
        link: function($scope, iElement, iAttrs) {

            $scope.$watch('animate', function(newValue) {
                console.log("watching animate", newValue);
                if(newValue) {
                    if(newValue == "left") {
                        console.log("animting left");
                        // we need to animate left
                        jQuery('.cf-selector-columnwrapper').animate({
                            opacity: 0.8,
                            left: '-=510'
                        }, 500, function() {
                            // Animation complete.
                            $scope.$apply(function() {
                                $scope.currentClinicalFeature = $scope.nextClinicalFeature;
                                $scope.nextClinicalFeature = null;
//                                $scope.selectedClinicalFeature = null;
                                $scope.animate = null;
                            });
                            jQuery('.cf-selector-columnwrapper').css('left', '-520px');
                        });
                    } else if (newValue == "right") {
                        jQuery('.cf-selector-columnwrapper').animate({
                            opacity: 0.8,
                            left: '+=510'
                        }, 500, function() {
                            // Animation complete.
                            $scope.$apply(function() {
                                $scope.currentClinicalFeature = $scope.previousClinicalFeature;
                                $scope.previousClinicalFeature = null;
//                                $scope.selectedClinicalFeature = null;
                                $scope.animate = null;
                            });
                            jQuery('.cf-selector-columnwrapper').css('left', '-520px');
                        });
                    }
                }
            })

        }
    }
});