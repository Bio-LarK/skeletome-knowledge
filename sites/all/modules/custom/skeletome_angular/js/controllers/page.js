function PageCtrl($scope, $http) {
    $scope.user = Drupal.settings.skeletome_builder.user;
    $scope.loginForm = Drupal.settings.skeletome_builder.login_form;
    $scope.globalSearch = function(term) {
        window.location.href = "?q=search/site/" + term;
    }

    $scope.navSearchModel = {};
    if(Drupal.settings.skeletome_builder.search_query) {
        $scope.navSearchModel.entry = Drupal.settings.skeletome_builder.search_query;
    } else {
        $scope.navSearchModel.entry = "";
    }


    $scope.navSearchModel.selectedIndex = -1;
    $scope.navSearchModel.isShowingSuggestions = false;

    $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

}