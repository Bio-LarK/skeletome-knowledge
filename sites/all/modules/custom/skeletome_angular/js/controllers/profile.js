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
        if(!angular.isDefined($scope.profile.field_profile_publications) || !angular.isDefined($scope.profile.field_profile_publications.und)) {
            $scope.profile.field_profile_publications = {
                und : []
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

        // Edit holder
        $scope.edit = {};

        // States of ui
        $scope.biographyState = "isDisplaying";
        $scope.professionalState = "isDisplaying";
        $scope.publicationsState = "isDisplaying";
        $scope.detailsState = "isDisplaying";
        $scope.orcidState = "isDisplaying";

        $scope.linkedIn = Drupal.settings.skeletome_profile.linkedIn;
        $scope.linkedInImportFields = {
            summary: true,
            position: true,
            location: true
        };
        $scope.orcidImportFields = {
            biography: true,
            works: true
        }

        $scope.DEFAULT_PUB_LIMIT = 3;

        // Setup the default length
        $scope.publicationDisplayLimit = $scope.DEFAULT_PUB_LIMIT;
        $scope.isHidingPublications =  $scope.profile.field_profile_publications.und.length > $scope.DEFAULT_PUB_LIMIT;

        console.log($scope.isHidingPublications);

    }


    $scope.showAllPublications = function() {
        $scope.isHidingPublications = false;
        console.log("hell oworld", $scope.profile.field_profile_publications.und.length);
        $scope.publicationDisplayLimit = $scope.profile.field_profile_publications.und.length;
    }

    $scope.hidePublications = function() {
        $scope.isHidingPublications = true;
        $scope.publicationDisplayLimit = $scope.DEFAULT_PUB_LIMIT;
    }

    $scope.editDetails = function() {
        $scope.edit.profile = angular.copy($scope.profile);
        $scope.detailsState = "isEditing";
    }
    $scope.$watch('profilePics.files', function(files) {
        // get out the new file
        if(files.length) {
            var newFile = files.pop();
            // Add the new file to the profile
            $scope.edit.profile.field_profile_image = {};
            $scope.edit.profile.field_profile_image.und = [newFile];

            console.log("profile image set", $scope.edit.profile.field_profile_image);
        }
    }, true);

    $scope.saveDetails = function(profile) {
        $scope.detailsState = "isLoading";

        console.log("saving profile", profile);
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

    $scope.showImportFromOrcid = function() {
        $scope.isShowingImportFromOrcid = true;
    }
    $scope.hideImportFromOrcid = function() {
        $scope.isShowingImportFromOrcid = false;
    }
    $scope.importFromOrcid = function(orcidId) {
        if(orcidId.length == 0) {
            return;
        }
        $scope.edit.profile = angular.copy($scope.profile);
        console.log("import from orcid " + orcidId);
        $scope.isLoadingImportFromOrcid = true;

        var url = '?q=orcid/profile/' + orcidId;
        console.log("url is" + url);
        $http.get(url).success(function(data) {
//            console.log(data);
            if($scope.orcidImportFields.biography) {
                $scope.edit.profile.body.und[0].value = data.bio || "";
            }

            if($scope.orcidImportFields.works) {
                $scope.edit.profile.field_profile_publications.und = [];
                angular.forEach(data.publications, function(publication) {
                    $scope.edit.profile.field_profile_publications.und.push({'value': publication});
                });
            }

            $scope.biographyState = "isLoading";
            $scope.publicationsState = "isLoading";

            $scope.saveProfile($scope.edit.profile).success(function(data) {
                $scope.isLoadingImportFromOrcid = false;
                $scope.isShowingImportFromOrcid = false;
                $scope.biographyState = "isDisplaying";
                $scope.publicationsState = "isDisplaying";
                $scope.profile = data;
            });

        });
    }

    $scope.showImportFromLinkedIn = function() {
        $scope.isShowingImportFromLinkedIn = true;
    }
    $scope.hideImportFromLinkedIn = function() {
        $scope.isShowingImportFromLinkedIn = false;
    }
    $scope.importFromLinkedIn = function() {
        $scope.edit.profile = angular.copy($scope.profile);
        $scope.isLoadingImportFromLinkedIn = true;

        $http.get('?q=linkedin/profile').success(function(data) {
            $scope.linkedIn.justGranted = false;

            $scope.edit.profile = angular.copy($scope.profile);
            if($scope.linkedInImportFields.summary) {
                $scope.edit.profile.body.und[0].value = data.bio || "";
            }
            if($scope.linkedInImportFields.position) {
                $scope.edit.profile.field_profile_position.und[0].value = data.position || "";
            }
            if($scope.linkedInImportFields.location) {
                $scope.edit.profile.field_profile_location.und[0].value = data.location || "";
            }

            $scope.biographyState = "isLoading";
            $scope.professionalState = "isLoading";

            $scope.saveProfile($scope.edit.profile).success(function(data) {
                $scope.isLoadingImportFromLinkedIn = false;
                $scope.isShowingImportFromLinkedIn = false;
                $scope.biographyState = "isDisplaying";
                $scope.professionalState = "isDisplaying";
                $scope.profile = data;
            });
        });
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