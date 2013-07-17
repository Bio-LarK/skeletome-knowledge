function SearchCtrl($scope, $http) {
    $scope.init = function() {


        $scope.model.results = Drupal.settings.skeletome_search_page.results;
        $scope.model.boneDysplasias = Drupal.settings.skeletome_search_page.boneDysplasias || [];
        $scope.model.genes = Drupal.settings.skeletome_search_page.genes || [];
        $scope.model.clinicalFeatures = Drupal.settings.skeletome_search_page.clinicalFeatures || [];
        $scope.model.groups = Drupal.settings.skeletome_search_page.groups || [];

        $scope.model.navSearchModel.entry = Drupal.settings.skeletome_search_page.queryString;
        $scope.model.navSearchModel.query = Drupal.settings.skeletome_search_page.queryTerms;
        $scope.model.navSearchModel.isShowingSuggestion = false;

        $scope.model.facets = [];
        $scope._updateFacets(Drupal.settings.skeletome_search_page.facets);

        $scope.model.pageCount = 0;

        $scope.model.moreResults = true;


        this.searchContent = function() {
            var search = $scope.model.navSearchModel.entry;
            var conditions = [];

            // If there is only one pill left, whatever it is, thats what we search for
            if($scope.model.navSearchModel.query.length == 1 && $scope.model.navSearchModel.entry == "")  {
                search += " " + $scope.model.navSearchModel.query[0].title || $scope.model.navSearchModel.query[0].name;
            } else {

            }
            angular.forEach($scope.model.navSearchModel.query, function(queryTerm, index) {
                if(queryTerm.machine_name == "skeletome_vocabulary") {
                    conditions.push("im_field_skeletome_tags:" + queryTerm.tid);
                } else {
                    search += " " + queryTerm.title;
                }
            });

            console.log("search content", search, conditions);

            return {
                'search': search.trim(),
                'conditions': conditions
            }
        }

        $scope.$watch('model.navSearchModel.query', function(query) {
            if(query) {

                // the query has been changed, lets see if we can change the conditions
                $scope.model.pageCount = 0;

                $scope._doSearch(false);
            }
        }, true);

    }


    $scope.loadMore = function() {
        $scope.model.pageCount++;
        $scope._doSearch(true);
    }

    $scope.addClinicalFeature = function(clinicalFeature) {
        $scope.model.results = [];
        // add the clinical feature to the query
        clinicalFeature.machine_name = "skeletome_vocabulary";
        $scope.model.navSearchModel.query.push(clinicalFeature);
    }

    $scope._doSearch = function(append) {

        var searchContent = this.searchContent();
        console.log("calling do search");
        if(!searchContent.search.length && !searchContent.conditions.length) {
            $scope.model.results = false;
            $scope.model.moreResults = false;
            return;
        }


//        var url = "?q=ajax/full-search&searchstring=" + ($scope.model.searchString || Drupal.settings.skeletome_search_page.searchString) + "&conditions=" + encodeURIComponent(JSON.stringify($scope.model.conditions)) + "&page=" + $scope.model.pageCount;
        var url = "?q=ajax/full-search&searchstring=" + searchContent.search + "&conditions=" + encodeURIComponent(JSON.stringify(searchContent.conditions)) + "&page=" + $scope.model.pageCount;

        $scope.model.isLoading = true;

        $http.get(url).success(function(data) {
            $scope.model.moreResults = true;
            $scope.model.isLoading = false;


            if(!angular.isDefined(data.results.apachesolr_search_browse)) {
                if(append) {
                    $scope.model.results = $scope.model.results.concat(data.results);
                } else {
                    $scope.model.results = data.results;
                }
            } else {
                console.log("results are empty");
                $scope.model.results = [];
            }

            $scope._updateFacets(data.facets);

            if(data.results.length != 10) {
                $scope.model.moreResults = false;
            }
        });
    }

    $scope._updateFacets = function(data) {
        // clean the facets
        $scope.model.facets = [];

        angular.forEach(data.clinical_features, function(value, index) {
            var found = false;
            angular.forEach($scope.model.navSearchModel.query, function(term, index2) {
                if(term.tid == value.tid) {
                    found = true;
                }
            })
            if(!found) {
                $scope.model.facets.push(value);
            }
        });
    }

}