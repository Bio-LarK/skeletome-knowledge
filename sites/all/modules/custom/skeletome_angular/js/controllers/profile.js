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

        if($scope.linkedIn.justGranted) {
            $scope.importLinkedInBiography();
        }

        console.log("publications", $scope.profile.field_profile_publications.und);
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
    $scope.importLinkedInBiography = function() {
        if($scope.biographyState != "isEditing") {
            $scope.edit.profile = angular.copy($scope.profile);
        }
        $scope.biographyState = "isLoading";

        $http.get('?q=linkedin/profile').success(function(data) {
            $scope.linkedIn.justGranted = false;

            if(angular.isDefined(data.error)) {
                console.log("Error in profile data");
                $scope.linkedIn.isAuthenticated = false;
                $scope.biographyState = "isEditing";
            } else {
                $scope.edit.profile = angular.copy($scope.profile);
                $scope.edit.profile.body.und[0].value = data.bio + " " + data.position + " " + (data.location || "") + $scope.edit.profile.body.und[0].value;
                $scope.biographyState = "isEditing";
            }
        });
    }
    $scope.importOrcidBiography = function() {
        $scope.orcidImportFields.biography = true;
        $scope.orcidImportFields.works = false;
        $scope.isShowingImportFromOrcid = true;
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


    $scope.editPublications = function() {
        $scope.edit.profile = angular.copy($scope.profile);
        $scope.publicationsState = "isEditing";
    }
    $scope.showAddPublication = function() {
        $scope.isAddingPublication = true;
    }
    $scope.addPublication = function(publicationText) {
        // Construct a publication
        var publication = {
            value: $scope.stripTags(publicationText, "<b><i>")
        };
        $scope.edit.profile.field_profile_publications.und.unshift(publication);
        $scope.edit.newProfileText = "";
        $scope.isAddingPublication = false;
    }
    $scope.savePublications = function(profile) {
        $scope.publicationsState = "isLoading";

        $scope.saveProfile(profile).success(function(data) {
            $scope.publicationsState = "isDisplaying";
            $scope.profile = data;
        });
    }
    $scope.stripTags = function (input, allowed) {
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
            commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
    }

    $scope.removePublication = function(publication) {
        var index = $scope.edit.profile.field_profile_publications.und.indexOf(publication);
        $scope.edit.profile.field_profile_publications.und.splice(index, 1);
    }
    $scope.cancelPublications = function() {
        $scope.publicationsState = "isDisplaying";
    }
    $scope.importOrcidPublications = function() {
        $scope.orcidImportFields.biography = false;
        $scope.orcidImportFields.works = true;
        $scope.isShowingImportFromOrcid = true;
    }

    $scope.hideImportFromOrcid = function() {
        $scope.isShowingImportFromOrcid = false;
    }
    $scope.importFromOrcid = function(orcidId) {
        if(orcidId.length == 0) {
            return;
        }
        $scope.isShowingImportFromOrcid = false;

        if($scope.biographyState != "isEditing") {
            $scope.edit.profile = angular.copy($scope.profile);
        }

        if($scope.orcidImportFields.biography) {
            $scope.biographyState = "isLoading";
        }
        if($scope.orcidImportFields.works) {
            $scope.publicationsState = "isLoading";
        }
        $http.get('?q=orcid/profile/' + orcidId).success(function(data) {

            if($scope.orcidImportFields.biography) {
                $scope.edit.profile.body.und[0].value = (data.bio || "") + $scope.edit.profile.body.und[0].value;
            }

            if($scope.orcidImportFields.works) {
//                $scope.edit.profile.field_profile_publications.und = [];
                angular.forEach(data.publications, function(publication) {
                    $scope.edit.profile.field_profile_publications.und.push({'value': publication});
                });
            }

            if($scope.orcidImportFields.biography) {
                $scope.biographyState = "isEditing";
            }
            if($scope.orcidImportFields.works) {
                $scope.publicationsState = "isEditing";
            }
        });
    }

    $scope.importFromLinkedIn = function() {

        if($scope.biographyState != "isEditing") {
            $scope.edit.profile = angular.copy($scope.profile);
        }
        $scope.biographyState = "isLoading";

        $http.get('?q=linkedin/profile').success(function(data) {
            $scope.linkedIn.justGranted = false;

            if(angular.isDefined(data.error)) {
                linkedIn.isAuthenticated = false;
                $scope.biographyState = "isEditing";
            } else {
                $scope.edit.profile = angular.copy($scope.profile);
                $scope.edit.profile.body.und[0].value = $scope.edit.profile.body.und[0].value + data.bio + " " + data.position + " " + data.location || "";
                $scope.biographyState = "isEditing";
            }
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