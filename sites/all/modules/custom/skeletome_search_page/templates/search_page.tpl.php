<style type="text/css">
    em {
        background-color: yellow;
    }
</style>
<div ng-controller="SearchCtrl" ng-init="init()">
    <!--<div class="row">
        <div class="span12">
            <section>
                <div class="section-segment section-segment-header">
                    Searching
                </div>

                <div ng-repeat="boneDysplasia in model.boneDysplasias">
                    <div class="section-segment">
                        {{ boneDysplasia.title }}
                    </div>
                </div>

                <div ng-repeat="gene in model.genes">
                    <div class="section-segment">
                        {{ gene.title }}
                    </div>
                </div>

                <div ng-repeat="clinicalFeature in model.clinicalFeatures">
                    <div class="section-segment">
                        {{ clinicalFeature.name }}
                    </div>
                </div>

                <div ng-repeat="group in model.groups">
                    <div class="section-segment">
                        {{ group.title }}
                    </div>
                </div>
            </section>
        </div>
    </div>-->

    <div class="row">
        <div class="span12">
            <div class="page-heading">
                <h1>Search Results</h1>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="span8">
            <section ng-show="!model.results.length && !model.isLoading">
                <div class="section-segment">
                    No results.
                </div>
            </section>
            <section ng-repeat="result in model.results">

                <a class="section-segment section-segment-header" href="?q=node/{{ result.node.entity_id }}">
                    <div class="section-segment-header-buttons pull-right">
                        <i class="icon-chevron-right"></i>
                        <i class="icon-chevron-right icon-white"></i>
                    </div>

                    <h3 ng-bind-html-unsafe="result.title"></h3>
                </a>
                <div class="section-segment">
                    <b>Abstract </b>
                    <span ng-bind-html-unsafe="result.snippets.content[0]"></span>
                </div>
                <div class="section-segment">
                    <b>Clinical Features</b>

                    <span ng-repeat="clinicalFeature in model.clinicalFeatures"><em>{{ clinicalFeature.name }}, </em></span>
                    <span ng-repeat="clinicalFeature in result.clinical_features">{{ clinicalFeature.name }}, </span>...
                </div>
            </section>

            <section ng-show="model.isLoading">
                <div class="section-segment">
                    <div ng-show="model.isLoading" class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>
            </section>
            <section ng-show="model.moreResults && !model.isLoading">
                <div class="section-segment">
                    <a class="btn btn-reveal" href="" ng-click="loadMore()">Show More</a>
                </div>
            </section>
        </div>
        <div class="span4">
            <section>
                <div class="section-segment section-segment-header">
                    <h3>Filter Clinical Features</h3>
                </div>

                <div class="section-segment" ng-show="!model.results.length && !model.isLoading">
                    No filters.
                </div>

                <div ng-show="model.isLoading" class="section-segment">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>


                <div ng-show="!model.isLoading && model.results.length > 0" ng-repeat="facet in model.facets | limitTo:20">
                    <a class="section-segment" href="" ng-click="addClinicalFeature(facet)">
                        <span class="label pull-right">{{ facet.count }}</span>

                        <i class="icon-plus"></i> {{ facet.name }}
                    </a>
                </div>
                <div ng-show="model.results.length > 0" class="section-segment">
                    <a class="btn btn-reveal" href="" ng-click="loadMore()">Show More</a>
                </div>
            </section>
        </div>
    </div>
</div>