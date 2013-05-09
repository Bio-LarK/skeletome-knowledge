myApp.factory('drupalContent', function() {
    return {
        mergeTermArrays : function(termArray1, termArray2) {
            termArray2 = angular.copy(termArray2);
            angular.forEach(termArray1, function(term1, key1){
                angular.forEach(termArray2, function(term2, key2){
                    if(term1.tid == term2.tid) {
                        termArray2.splice(key2, 1);
                        return false;
                    }
                });
            });
            return termArray1.concat(termArray2);
        },
        mergeNodeArrays : function(nodeArray1, nodeArray2) {
            nodeArray2 = angular.copy(nodeArray2);
            angular.forEach(nodeArray1, function(node1, key1){
                angular.forEach(nodeArray2, function(node2, key2){
                    if(node1.nid == node2.nid) {
                        nodeArray2.splice(key2, 1);
                        return false;
                    }
                });
            });
            return nodeArray1.concat(nodeArray2);
        },
        markAsAdded: function(myArray) {
            angular.forEach(myArray, function(element, key) {
                element.added = true;
            });
            return myArray;
        },
        sortUniqueTerms: function(arr) {
            arr = arr.sort(function (a, b) {
                return +(a.tid) - +(b.tid);
            });
            var ret = [arr[0]];
            for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
                if (arr[i-1].tid !== arr[i].tid) {
                    ret.push(arr[i]);
                }
            }
            return ret;
        },
        sortUniqueNodes: function(arr) {
            arr = arr.sort(function (a, b) {
                return +(a.nid) - +(b.nid);
            });
            var ret = [arr[0]];
            for (var i = 1; i < arr.length; i++) { // start loop at 1 as element 0 can never be a duplicate
                if (arr[i-1].nid !== arr[i].nid) {
                    ret.push(arr[i]);
                }
            }
            return ret;
        }
    };
});

myApp.factory('autocomplete', function($http) {
    return {
        clinicalFeatures : function(name) {
            return $http.get('?q=ajax/autocomplete/clinical-feature/' + name);
        }
    }
});




























