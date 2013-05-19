function DashboardCtrl($scope, $http) {

    $scope.init = function() {
        $scope.pageTracks = Drupal.settings.skeletome_dashboard.page_tracks;
        $scope.topPages = Drupal.settings.skeletome_dashboard.top_pages;
        $scope.searches = Drupal.settings.skeletome_dashboard.searches;

        // load the recent search results
        angular.forEach($scope.searches, function(search, index) {
            search.terms = [];

            // check if multi-part query
            if(search.target_search.indexOf(";") != -1) {
                var terms = search.target_search.split(";");
                angular.forEach(terms, function(term, index) {
                    if(term != "") {
                        search.terms.push(term.trim());
                    }
                });
            }
        });

        angular.forEach($scope.pageTracks, function(pageTrack, index) {
            if(angular.isDefined(pageTrack.field_page_tracker_search.und)) {
                pageTrack.field_page_tracker_search.und[0].terms = [];

                if(pageTrack.field_page_tracker_search.und[0].value.indexOf(";") != -1) {
                    var terms = pageTrack.field_page_tracker_search.und[0].value.split(";");
                    angular.forEach(terms, function(term, index) {
                        if(term != "") {
                            pageTrack.field_page_tracker_search.und[0].terms.push(term.trim());
                        }
                    });
                }
            }
        });

        console.log($scope.pageTracks);
    }

}