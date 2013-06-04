function DescriptionCtrl($scope, $http) {

    $scope.defaultDescriptionLength = 500;

    // Setup the default length
    $scope.descriptionLength = $scope.defaultDescriptionLength;
    $scope.isHidingDescription =  $scope.description.value.length > $scope.defaultDescriptionLength;

    $scope.showDescription = function() {
        $scope.isHidingDescription = false;
        $scope.descriptionLength = $scope.description.safe_value.length + 100;
    }

    $scope.hideDescription = function() {
        $scope.isHidingDescription = true;
        $scope.descriptionLength = $scope.defaultDescriptionLength;
    }

    /* Add / Editing */
    $scope.editDescription = function() {
        $scope.editedDescription = $scope.description.value;
        $scope.isEditingDescription = true;
    }

    /* Save edited descrption */
    $scope.cancelEditingDescription = function() {
        $scope.isEditingDescription = false;
    }
    $scope.saveEditedDescription = function(newDescription) {

        console.log("saving editied description");
        $scope.isEditingDescription = false;

        $scope.description.safe_value = "Loading...";
        $scope.description.isLoading = true;

        var nodeId = 0;
        if(angular.isDefined($scope.master)) {
            nodeId = $scope.master.gene.nid;
        } else {
            nodeId = $scope.model.boneDysplasia.nid;
        }

        // Save the description
        $http.post($scope.description.url, {
            'id': nodeId,
            'description': newDescription
        }).success(function(data) {
            $scope.description.isLoading = false;
            $scope.description.safe_value = data.safe_value;
            $scope.description.value = data.value;
        });
    }
}