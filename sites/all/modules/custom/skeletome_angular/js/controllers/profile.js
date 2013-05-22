myApp.service('Api', function() {
    this.baseUrl = (function() {
        // Replace : with \\: for angularjs
        var url = Drupal.settings.skeletome_builder.base_url;//window.location.protocol + "//" + window.location.host.replace(":", "\\:");

        var pos = url.lastIndexOf(':');
        url = url.substring(0,pos) + '\\:' + url.substring(pos+1)

        return url;
    })();

});

function ProfileCtrl($scope, $http) {


    $scope.init = function() {
        $scope.contributed = Drupal.settings.skeletome_profile.contributed;
        $scope.activity = Drupal.settings.skeletome_profile.activity;
        $scope.profile = Drupal.settings.skeletome_profile.profile || {};
        $scope.user = Drupal.settings.skeletome_profile.user;

        // fix up the activity
        angular.forEach($scope.activity, function(activity, index) {

            if(!angular.isDefined(activity.cid)) {
                console.log(activity.body);
                activity.body = jQuery(activity.body).text();
            }
        });

        $scope.profilePics = {
            files: []
        }

        if(!angular.isDefined($scope.profile.body)) {
            $scope.profile.body = {
                'und' : [
                    {'value' : '', 'safe_value': ''}
                ]
            };
        }
        if(!angular.isDefined($scope.profile.field_profile_location)) {
            $scope.profile.field_profile_location = {
                und : [
                    {'value' : ''}
                ]
            };
        }
        if(!angular.isDefined($scope.profile.field_profile_position)) {
            $scope.profile.field_profile_position = {
                und : [
                    {'value' : ''}
                ]
            };
        }
        if(!angular.isDefined($scope.profile.field_profile_user_id)) {
            $scope.profile.field_profile_user_id = {
                und : [
                    {'value' : $scope.user.uid}
                ]
            };
        }
        if(!angular.isDefined($scope.profile.field_profile_orcid_id)) {
            $scope.profile.field_profile_orcid_id = {
                und : [
                    {value: ""}
                ]
            };
        }

        $scope.name = Drupal.settings.skeletome_profile.name;
        $scope.roles = Drupal.settings.skeletome_profile.roles;
        $scope.recentActivityDisplayLimit = 10;
        $scope.contributedDisplayLimit = 10;
        $scope.edit = {};

        $scope.biographyState = "isDisplaying";
        $scope.professionalState = "isDisplaying";
        $scope.detailsState = "isDisplaying";
        $scope.orcidState = "isDisplaying";
    }



//    $scope.fetchOrcidPublic = function(orcidId) {
//        console.log("fetching orcid");
//        $http.get('?q=ajax/profile/orcid/0000-0002-1808-0964').success(function(data, status, headers, config) {
//            // this callback will be called asynchronously
//            // when the response is available
//            console.log("got data", data);
//        }).error(function(data, status, headers, config) {
//                // called asynchronously if an error occurs
//                // or server returns response with an error status.
//            console.log("error", data, status, header, config);
//        });
//    }
//
//    setTimeout(function() {
//        $scope.fetchOrcidPublic();
//    }, 2000);

    $scope.editDetails = function() {
        $scope.edit.profile = angular.copy($scope.profile);
        $scope.detailsState = "isEditing";
    }
    $scope.$watch('profilePics.files', function(files) {
        // get out the new file
        if(files.length) {
            var newFile = files.pop();
            // Add the file to the image property of the profile
            if(!angular.isDefined($scope.edit.profile.field_profile_image)) {
                $scope.edit.profile.field_profile_image = {
                    und: []
                };
            }

            $scope.edit.profile.field_profile_image.und = [newFile];

            console.log("profile image set", $scope.edit.profile.field_profile_image);
        }
    }, true);

    $scope.saveDetails = function(profile) {
        $scope.detailsState = "isLoading";

        $scope.saveProfile(profile).success(function(data) {
            $scope.detailsState = "isDisplaying";
            $scope.profile = data;
        });
    }
    $scope.cancelDetails = function() {
        $scope.detailsState = "isDisplaying";
    }




    $scope.editBiography = function() {
        $scope.edit.profile = angular.copy($scope.profile);
        $scope.biographyState = "isEditing";
    }
    $scope.saveBiography = function(profile) {
        $scope.biographyState = "isLoading";

        $scope.saveProfile(profile).success(function(data) {
            $scope.biographyState = "isDisplaying";
            $scope.profile = data;
        });
    }
    $scope.cancelBiography = function() {
        $scope.biographyState = "isDisplaying";
    }

    $scope.editProfessional = function() {
        $scope.professionalState = "isEditing";
        $scope.edit.profile = angular.copy($scope.profile);
    }
    $scope.saveProfessional = function(profile) {
        $scope.professionalState = "isLoading";

        $scope.saveProfile(profile).success(function(data) {
            $scope.professionalState = "isDisplaying";
            $scope.profile = data;
        });
    }
    $scope.cancelProfessional = function() {
        $scope.professionalState = "isDisplaying";
    }

    $scope.saveProfile = function(profile) {
        var url = "";
        if(angular.isDefined(profile.nid)) {
            url = '?q=rest/profile/' + profile.nid + '/update';
        } else {
            url = '?q=rest/profile/save';
        }

        return $http.post(url, {
            node: profile
        });
    }



//    $http.post('?q=rest/profile/9256/update', {
//        hello: 'go'
//    }).success(function(data) {
//        console.log(data);
//    });
//
//    $scope.profile = Profile.get({ profileId:9256 }, function() {
//        $scope.profile.title = "new title";
//        $scope.profile.$update();
//    });
}