function SparqlCtrl($scope, $http, $q) {
    $scope.queries = dataQueries;

    // Default
    $scope.resultLimit = 30;
    $scope.conceptData = {};

    $scope.percentFinished = 0;

    $scope.querySelected = function(key) {
        // do an ajax request wit hthe key
        $http.get('data/query.json?query=' + key).success(function(data) {
            $scope.query = data;

            // Get the concepts
            angular.forEach($scope.query.queryConcepts, function(queryConcept, queryConceptIndex) {
                // Load the query concepts
                $http.get('data/concepts.json?uri=' + queryConcept.uri + "&type=" + queryConcept.type + "&label=" + queryConcept.labelProperty).success(function(data) {
                    // store this concept data
                    $scope.conceptData[queryConcept.id] = data;
                });
            })


            // Clear out the chosen concepts, and the results
            $scope.chosenConcepts = {};
            $scope.results = {};

            // we need to split this
            $scope.tokenQueryText = $scope.query.text.split(/["<<"">>"]+/);

            $scope.queryParts = [];

            angular.forEach($scope.tokenQueryText, function(token, tokenIndex) {
                if(tokenIndex %2 != 0) {
                    // this is an input
                    $scope.queryParts.push({
                        type:'input',
                        label:token.split("|")[0],
                        id: token.split("|")[1]
                    })
                } else {
                    // this is text
                    $scope.queryParts.push({
                        type:'text',
                        label: token.trim()
                    });
                }
            });

        })
    }

    $scope.findQueryTerm = function(value, id) {
        var defer = $q.defer();

        var results = [];
        angular.forEach($scope.conceptData[id], function(concept, conceptIndex) {
            if(concept.label.toLowerCase().indexOf(value.toLowerCase()) != -1) {
                results.push(concept);
            }
        });
        defer.resolve(results);
        return defer.promise;
    }

    $scope.$watch('chosenConcepts', function(chosenConcepts) {
        console.log("chosenConcepts changed");
        // build out query

        if(chosenConcepts) {
            $scope.sparql = $scope.query.sparqlQuery;

            angular.forEach($scope.chosenConcepts, function(content, key) {
                if(content) {
                    var re = new RegExp("<<" + key + ">>", "gi");
                    $scope.sparql = $scope.sparql.replace(re, "<" + content.uri + ">");
                }
            })

            // Work out percent complete
            var fieldsComplete = 0;
            var fieldsTotal = 0;
            angular.forEach($scope.queryParts, function(queryPart, queryPartIndex) {
                if(angular.isDefined(queryPart.id)) {
                    fieldsTotal++;
                    if(!angular.isDefined($scope.chosenConcepts[queryPart.id]) || $scope.chosenConcepts[queryPart.id] == null) {

                    } else {
                        fieldsComplete++;
                    }
                }
            });

            $scope.percentFinished = fieldsComplete / fieldsTotal * 100;

            $scope.results = {};
        }
    }, true);

    $scope.go = function(sparql) {

        $scope.resultLimit = 100;
        var isComplete = true;
        angular.forEach($scope.queryParts, function(queryPart, queryPartIndex) {
            if(angular.isDefined(queryPart.id)) {
                if(!angular.isDefined($scope.chosenConcepts[queryPart.id]) || $scope.chosenConcepts[queryPart.id] == null) {
                    isComplete = false;
                }
            }
        });

        if(!isComplete) {
            alert("Please fill in all query options before submitting");
        } else {

            // todo: Do the post in here
//            $http.post('URL', {query: $scope.sparql}).success(function(dataResult) {
            $scope.results = dataResult;
//            });

        }
    }

    $scope.showMore = function() {
        $scope.resultLimit += 150;
    }


    for (var k in $scope.queries) {
        $scope.selectedQuery = $scope.queries[k];
        $scope.querySelected(k);
        break
    }


//    $scope.concepts = dataConcepts;
//    $scope.result = dataResult;
}