<div ng-controller="SparqlCtrl">
    <div class="row-fluid" >
        <div class="span12">
            <div>
                <h3>1. Choose a Query</h3>
                <select class="selector" ng-change="querySelected(key)" ng-options="value for (key , value) in queries" ng-model="selectedQuery"></select>
            </div>

            <div ng-show="selectedQuery">
                <h3>2. Customise Your Query</h3>
                <div class="customizer">
                    <div ng-repeat="queryPart in queryParts">
                        <div ng-show="queryPart.type == 'text'" class="customizer-text">
                            {{ queryPart.label }}
                        </div>
                        <div ng-show="queryPart.type == 'input'" class="customizer-input">
                            <typeahead multi="false" model="chosenConcepts[queryPart.id]" placeholder="{{ queryPart.label }}" options-fn="findQueryTerm(value, queryPart.id)"></typeahead>
                        </div>
                    </div>
                    <div>
                        <textarea class="customizer-output" ng-model="sparql" rows="6" >
                        </textarea>
                    </div>
                    <div>

                        <button class="btn btn-go" ng-click="go(sparql)" ng-class="{'disabled': percentFinished != 100}">
                            Go!
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row-fluid" ng-show="results.head">
        <h3>Results</h3>
        <table class="table table-bordered">
            <tr>
                <th ng-repeat="heading in results.head.vars"> {{ heading }}</th>
            </tr>
            <tr ng-repeat="result in results.results.bindings | limitTo:resultLimit">
                <td ng-repeat="heading in results.head.vars">
                    {{ result[heading].value }}
                </td>
            </tr>
        </table>
        <button class="btn btn-info" ng-click="showMore()" ng-show="resultLimit < results.results.bindings.length">Show More</button>
        <a href="#" class="btn btn-info" ng-show="resultLimit > 100">Back to Top</a>
    </div>
</div>