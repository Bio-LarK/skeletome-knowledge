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