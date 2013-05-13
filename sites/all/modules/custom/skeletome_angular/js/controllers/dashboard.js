function DashboardCtrl($scope, $http) {

    $scope.init = function() {
        $scope.pageTracks = Drupal.settings.skeletome_dashboard.page_tracks;
        $scope.topPages = Drupal.settings.skeletome_dashboard.top_pages;
        $scope.searches = Drupal.settings.skeletome_dashboard.searches;

        // load the recent search results
        angular.forEach($scope.searches, function(search, index) {
            search.isLoading = true;
            $http.get('?q=ajax/dashboard/search/' + search.target_search).success(function(results) {
                search.isLoading = false;
                search.results = results;
            });
        });

    }

}