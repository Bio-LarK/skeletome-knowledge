var myApp = angular.module('PubMed', []);

var baseUrl = parent.Drupal.settings.skeletome_builder.base_url + "/";

myApp.directive('cmReturn', function() {
    return function (scope, iElement, iAttrs) {

        iElement.bind('keypress', function(event){
            if(event.which == 13) {
                scope.$apply(function() {
                    scope.$eval(iAttrs.cmReturn);
                });
                return false;
            }
        });
    }
});




function ReferenceCtrl($scope, $http, filterFilter) {

    console.log('test');

    $scope.test = 'hello world';

    $scope.searchOnline = false;

    $scope.pubMedOnline = {};


    // Load in existing Publications
    $http.get(baseUrl + '?q=ajax/biblios').success(function(biblios) {
        console.log(biblios);
        $scope.biblios = biblios;

        console.log($scope.biblios);
    });

    $scope.pubmedSearch = function(query) {
        // Do a pubmed query search
        $scope.isSearching = true;
        $scope.pubmedResults = null;

        $http.get(baseUrl + '?q=ajax/pubmed/search/' + query).success(function(data) {
            $scope.pubmedResults = data.results;
            $scope.isSearching = false;
        });
    }

    $scope.addNewCitation = function(pubmedId) {
        console.log("pubmed id is", pubmedId);
        $http.post(baseUrl + '?q=ajax/biblio', {
            'pubmedId': pubmedId
        }).success(function(biblio) {
            $scope.sendNidToEditor(biblio.nid);
        });
    }

    $scope.addExistingCitation = function(nid) {
        $scope.sendNidToEditor(nid);
    }


    $scope.sendNidToEditor = function(nid) {
        console.log("Sending NID to editor", nid);
        var dialog = window.parent.CKEDITOR.dialog.getCurrent();
        dialog.definition.onOk(
            nid
        );
        dialog.hide();
    }



//    $scope.pubMedChanged = function(pubmed) {
//        $scope.pubMedOnline = {};
//
//        var localResults = filterFilter($scope.biblios, pubmed).length;
//        if(localResults) {
//            $scope.searchOnline = false;
//        } else {
//            $scope.searchOnline =  true;
//        }
//    }

//    $scope.searchPubMed = function(pubmedId) {
//        $http.get(baseUrl + '?q=ajax/pubmed/' + pubmedId).success(function(pubMeds) {
//            console.log(pubMeds)
//            $scope.pubMedOnline = pubMeds;
//        });
//    }
//
//    $scope.addNewCitation = function(pubmedId) {
//
//    }
//    $scope.addExistingCitation = function(biblio) {
//        $scope.sendNidToEditor(biblio.nid);
//    }


//
//    $scope.filterPubMed = function(item) {
//        // 23061930
//        if(!$scope.pubmed || $scope.pubmed == "") {
//            console.log("no pubmed");
//            return true;
//        } else {
//            console.log("has pubmed");
//            return item.pubmedId.indexOf($scope.pubmed) == 0;
//        }
//    };

//    $scope.filterByPubMed = function(item){
//        console.log("filtering item");
//        console.log(item);
////        if(pubmed || pubmed == "") {
//        return true;
////        } else {
////            return item.pubmedId.indexOf(pubmed) == 0;
////        }
//
//    }
}
