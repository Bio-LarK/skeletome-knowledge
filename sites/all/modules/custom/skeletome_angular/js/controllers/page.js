function PageCtrl($scope, $http) {
    $scope.user = Drupal.settings.skeletome_builder.user;
    $scope.loginForm = Drupal.settings.skeletome_builder.login_form;
    $scope.globalSearch = function(term) {
        window.location.href = "?q=search/site/" + term;
    }


    $scope.queryHolder = {};
    $scope.queryHolder.selectedIndex = -1;
    $scope.queryHolder.isShowingSuggestions = true;


    $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;

}