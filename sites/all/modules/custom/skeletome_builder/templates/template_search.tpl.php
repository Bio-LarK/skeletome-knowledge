<style type="text/css">
    .breadcrumb {
        display: none;
    }
</style>


<div ng-controller="SearchCtrl" ng-init="init()" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">


    <div class="search_heading">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <h2>Searching for '{{ query }}'
                        <small>{{ totalCount() }} Results</small>
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <div class="search_scope">
        <div class="container">
            <div class="row">
                <div class="span12">

                    <a href="?q=search/all/{{ query }}" class="btn btn-clear btn-pill" ng-class="{'btn-grey': type == 'all'}" href >
                        All ({{ totalCount() }})
                    </a>

                    <a href="?q=search/bone-dysplasias/{{ query }}" class="btn btn-clear btn-pill" ng-class="{'btn-grey': type == 'bone-dysplasias'}" href >
                        Bone Dysplasias ({{ counts.bone_dysplasia }})
                        <img class="down"  src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/down-search.png"/>
                    </a>

                    <a href="?q=search/groups/{{ query }}" class="btn btn-clear btn-pill" ng-class="{'btn-grey': type == 'groups'}" href >
                        Groups ({{ counts.group }})
                        <img class="down" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/down-search.png"/>
                    </a>

                    <a href="?q=search/clinical-features/{{ query }}" class="btn btn-clear btn-pill" ng-class="{'btn-grey': type == 'clinical-features'}" href>
                        Clinical Features ({{ counts.clinical_feature }})
                        <img class="down" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/down-search.png"/>
                    </a>

                    <a href="?q=search/genes/{{ query }}" class="btn btn-clear btn-pill" ng-class="{'btn-grey': type == 'genes'}" href>
                        Genes ({{ counts.gene }})
                        <img class="down" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/down-search.png"/>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="search_filters" ng-show="type == 'bone-dysplasias'" >
        <div class="container">
            <div class="row">
                <div class="span12">
                    <div>
                        <a class="btn btn-primary" href ng-click="showRefineClinicalFeatures()">
                            <i class="icon-plus icon-white"></i> Clinical Features
                        </a>
                        <a class="btn btn-primary" href ng-click="showRefineGenes()">
                            <i class="icon-plus icon-white"></i> Genes
                        </a>
                        <a class="btn btn-primary" href ng-click="showRefineGroups()">
                            <i class="icon-plus icon-white"></i> Groups
                        </a>

                        <span ng-show="!filters.length" ng-switch on="type" style="margin-left: 10px;" class="muted">
                            <span ng-switch-when="groups">Refine your search to find Groups with Bone Dysplasias.</span>
                            <span ng-switch-when="bone-dysplasias">Refine your search to find Bone Dysplasias with specific Genes, Groups or Clinical Features.</span>
                            <span ng-switch-when="clinical-features">Refine your search to find Clinical Features for specific Genes or Bone Dysplasias.</span>
                            <span ng-switch-when="genes">Refine your search to find Genes for specific Bone Dysplasias, Clinical Features or Groups.</span>
                        </span>

                        <span ng-repeat="filter in filters"  >
                            <span ng-click="removeFilter(filter)" class="btn btn-filter">
                                <i class="icon-remove"></i> {{ filter.title }} {{ filter.name }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="container">
        <div class="row">
            <div class="span12" style="padding: 10px; padding-top: 20px;">
                <h3>Displaying {{ selectedCount() }} results.</h3>
            </div>


        </div>

        <div class="row" ng-repeat="result in results">
            <div class="span8 search_results">
                <h4>
                    <a ng-show="result.title" href="?q=node/{{result.vid}}">{{ result.title }}</a>
                    <a ng-show="!result.title" href="?q=taxonomy/term/{{result.tid}}">{{ result.name }}</a>
                </h4>
                <p ng-bind-html-unsafe="result.body.und[0].value | truncate:200">
                </p>
            </div>
        </div>
        <div class="row">
            <div class="span12">
                <a ng-click="displayCount = displayCount + 20" ng-show="displayCount < filteredResults.length" class="btn btn-primary" href>Show More</a>
            </div>
        </div>
    </div>




    <!--<div cm-modal="showEditingPanel" ng-switch on="filterType" class="modal modal-dark fade hide" tabindex="-1" role="dialog"-->
    <div cm-modal="showEditingPanel" ng-switch on="filterType" class="modal modal-dark hide" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-switch" ng-switch-when="group">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Add a Clinical Feature</h3>
            </div>
            <!-- /Modal Header -->

            <!-- Modal Body -->
            <div class="modal-body">

<!--                <div class="modal-body-inner">-->

                    <form>
                        <search model="search" placeholder="Search for a Clinical Feature"></search>
                    </form>


                    <table class="table table-center">
                        <tr>
                            <th>Clinical Feature</th>
                            <th>Action</th>
                        </tr>

                        <tr ng-repeat="group in filteredGroups() | filter:search">
                            <td>{{ group.name | truncate:40}}</td>

                            <td>
                                <a ng-show="!group.filter" role="button" class="btn btn-success pull-right" ng-click="closeRefinePanel(); filterBy(group)"><i class="icon-plus icon-white"></i> Add</a>
                                <a ng-show="group.filter" role="button" class="btn btn-danger pull-right" ng-click="removeFilter(group)"><i class="icon-remove icon-white"></i> Remove</a>
                            </td>
                        </tr>
                    </table>

<!--                </div>-->

            </div>
            <!-- /Modal Body -->

            <!-- Modal Footer -->
            <!--<div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-primary" ng-click="closeRefinePanel()"><i class="icon-ok icon-white"></i> Done</a>
            </div>-->
            <!-- /Modal Footer -->
        </div>

        <div class="modal-switch" ng-switch-when="genes">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Refine Search</h3>
            </div>

            <div class="modal-body">

                    <p>Edit Clinical Features to attached to '{{boneDysplasia.title}}'.</p>
                    <form>
                        <search model="search" placeholder="Search for a Clinical Feature"></search>
                    </form>


                    <table class="table table-center">
                        <tr>
                            <th>Gene</th>
                            <th>Gene Mutation</th>
                            <th>Action</th>
                        </tr>

                        <tr ng-repeat="geneMutation in filteredGenes() | filter:search">
                            <td><b>{{ geneMutation.gene.title }}</b></td>
                            <td><i>{{ geneMutation.title }}</i></td>
                            <td>
                                <a ng-show="!geneMutation.filter" role="button" class="btn btn-success pull-right" ng-click="closeRefinePanel(); ; filterBy(geneMutation)"><i class="icon-plus icon-white"></i> Add</a>
                                <a ng-show="geneMutation.filter" role="button" class="btn btn-danger pull-right" ng-click="removeFilter(geneMutation)"><i class="icon-remove icon-white"></i> Remove</a>
                            </td>
                        </tr>
                    </table>

            </div>
            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-primary" ng-click="closeRefinePanel()"><i class="icon-ok icon-white"></i> Search</a>
            </div>
        </div>

        <div class="modal-switch" ng-switch-when="clinicalFeature">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Refine Search</h3>
            </div>

            <div class="modal-body">
                    <div>
                        <span ng-repeat="filter in newFilters" >
                            <span ng-click="removeFilter(filter)" class="btn btn-filter" style="margin-bottom: 10px">
                                <i class="icon-remove"></i> {{ filter.title || filter.name }}
                            </span>
                        </span>
                    </div>
<!--                <div class="modal-body-inner">-->

                    <form>
                        <search model="search" placeholder="Search for a Clinical Feature" change="clinicalFeatureDisplayCount = 40"></search>
                    </form>


                    <table class="table table-center">
                        <tr>
                            <th>Clinical Feature</th>
                            <th>Action</th>
                        </tr>

                        <tr ng-repeat="clinicalFeature in clinicalFeatures | filter:search">
                            <td>{{ clinicalFeature.name | truncate:40}}</td>

                            <td>
                                <a ng-show="!clinicalFeature.filter" role="button" class="btn btn-success pull-right" ng-click="filterBy(clinicalFeature)"><i class="icon-plus icon-white"></i> Add</a>
                                <a ng-show="clinicalFeature.filter" role="button" class="btn btn-danger pull-right" ng-click="removeFilter(clinicalFeature)"><i class="icon-remove icon-white"></i> Remove</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a ng-show="" ng-click="clinicalFeatureDisplayCount = clinicalFeatureDisplayCount + 10" class="btn btn-primary" href>
                                    Show More
                                </a>
                            </td>
                        </tr>
                    </table>

<!--                </div>-->

            </div>
            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-primary" ng-click="closeRefinePanel()"><i class="icon-search icon-white"></i> Search</a>
            </div>
        </div>
    </div>
</div>


