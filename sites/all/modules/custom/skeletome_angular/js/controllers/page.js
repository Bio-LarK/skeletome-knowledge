function PageCtrl($scope, $http) {
    $scope.user = Drupal.settings.skeletome_builder.user;
    $scope.loginForm = Drupal.settings.skeletome_builder.login_form;
    $scope.globalSearch = function(term) {
        window.location.href = "?q=search/site/" + term;
    }


    $scope.model = {};
    $scope.model.navSearchModel = {};
    $scope.model.navSearchModel.entry = "";
    $scope.model.navSearchModel.query = [];
    $scope.model.navSearchModel.selectedIndex = -1;
    $scope.model.navSearchModel.isShowingSuggestions = false;

    $scope.baseUrl = Drupal.settings.skeletome_builder.base_url;
    $scope.browseTid = Drupal.settings.skeletome_builder.isds;
}