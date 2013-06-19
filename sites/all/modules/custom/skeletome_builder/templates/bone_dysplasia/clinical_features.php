
<!-- Clinical Features -->
<section id="clinical_features" class="block">
    <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': model.clinicalFeaturesState == 'isEditing' }">
        <div class="section-segment-header-buttons">
            <?php if ($isAdmin || $isCurator): ?>
                <div class="pull-right">
                    <div ng-switch on="model.clinicalFeaturesState">
                        <div ng-switch-when="isLoading">

                        </div>
                        <div ng-switch-when="isEditing">

                            <a href ng-click="saveClinicalFeatures()" class="btn btn-save">
                                <i class="ficon-ok"></i> Save
                            </a>

                            <a href ng-click="cancelClinicalFeatures()" class="btn btn-cancel">
                                <i class="ficon-remove"></i> Cancel
                            </a>

                            <div class="header-divider"></div>

                            <a ng-click="showAddClinicalFeature()" class="btn btn-cancel" href >
                                <i class="ficon-plus-sign"></i> Add
                            </a>

                        </div>
                        <div ng-switch-when="isDisplaying">
                            <a href ng-click="editClinicalFeatures()" class="btn btn-edit">
                                <i class="ficon-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div ng-switch on="model.clinicalFeaturesState">
            <div ng-switch-when="isEditing">
                <h2><i>Editing Clinical Features ({{ model.clinicalFeatures.length }})</i></h2>
            </div>
            <div ng-switch-when="isDisplaying">
                <h2>Clinical Features ({{ model.clinicalFeatures.length }})</h2>
            </div>
        </div>

    </div>

    <div class="section-segment alert alert-success" cm-alert="model.clinicalFeaturesState">
        <i class="ficon-ok"></i> Clinical Features saved.
    </div>

    <div ng-switch on="model.clinicalFeaturesState">
        <div ng-switch-when="isLoading">
            <div class="section-segment">
                <div class="refreshing-box">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>
            </div>
        </div>
        <div ng-switch-when="isEditing">

            <!-- Search form -->
            <!--  -->
            <div class="section-segment section-segment-editing">
                <form style="margin-bottom: 0">
                    <search model="model.edit.clinicalFeatureQuery" placeholder="Find a Clinical Feature"  placeholder="Search for a Clinical Feature"></search>
                </form>
            </div>

            <!-- Header -->
            <div class="section-segment section-segment-editing">
                <div>
                    <div style="width: 60%; display: inline-block">
                        <b>Feature</b>
                    </div>
                    <div style="width: 35%; display: inline-block">
                        <!--<b>Information Content</b> <i class="icon-question-sign" cm-tooltip="top" cm-tooltip-content="Information content."></i>-->
                    </div>
                </div>
            </div>

            <!-- Existing Clinical Features -->

            <div ng-repeat="clinicalFeature in model.edit.clinicalFeatures | filter:model.edit.clinicalFeatureQuery | orderBy:'-information_content'">
                <a ng-click="removeClinicalFeature(clinicalFeature)" class="section-segment section-segment-editing">

                    <span class="btn btn-remove" href=""><i class="ficon-remove"></i></span>

                    <span >
                        {{clinicalFeature.name | truncate:100 | capitalize}}
                    </span>
                </a>
            </div>

        </div>

        <div ng-switch-when="isDisplaying">

            <div class="section-segment">
                <form ng-show="model.clinicalFeatures.length" style="margin-bottom: 0">
                    <search model="model.clinicalFeatureFilter" placeholder="Search for a Clinical Feature"></search>
                </form>
            </div>

            <div>
                <div class="section-segment" ng-show="model.clinicalFeatures.length">
                    <div>
                        <div style="width: 60%; display: inline-block">
                            <b>Feature</b>
                        </div>
                        <div style="width: 35%; display: inline-block">
                            <b>Information Content</b> <i class="icon-question-sign" cm-tooltip="top" cm-tooltip-content="Information content."></i>
                        </div>
                    </div>
                </div>

                <div ng-repeat="clinicalFeature in model.clinicalFeatures | filter:model.clinicalFeatureFilter | orderBy:'-information_content'">
                    <a  style="overflow: hidden" class="section-segment" href="?q=node/{{ model.boneDysplasia.nid }}/clinical-feature/{{clinicalFeature.tid}}">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        <div style="width: 60%; float: left">
                            {{clinicalFeature.name | truncate:40 | capitalize}}
                        </div>

                        <div style="width: 35%; float: left">
                            <div class="progress">
                                <div class="bar" style="width:{{ clinicalFeature.information_content }}%"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<my-modal visible="isAddingClinicalFeature">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Add Clinical Feature</h3>
    </div>

    <div class="section-segment">
        <p>
            Search for a Clinical Feature e.g. 'Dwarfism'
        </p>
        <search model="model.edit.addClinicalFeatureQuery" change="searchForClinicalFeature(model.edit.addClinicalFeatureQuery)" placeholder="Search for a Clinical Feature"  placeholder="Search for a Clinical Feature"></search>
    </div>
    <div>
        <!-- Search Results -->
        <div ng-show="model.edit.addClinicalFeatureQuery.length" ng-switch on="model.edit.clinicalFeaturesSearchResultsState">
            <div ng-switch-when="isLoading">
                <div class="section-segment refreshing-box" ng-show="model.edit.clinicalFeaturesSearchResultsState">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>
            </div>
            <div ng-switch-when="isDisplaying">
                <div ng-repeat="result in model.edit.clinicalFeaturesSearchResults">
                    <a ng-show="!result.added" href class="section-segment" ng-click="addClinicalFeature(result)">
                        <span class="btn btn-add">
                            <i class="ficon-ok"></i>
                        </span>
                        <span ng-bind-html-unsafe="result.name | truncate:40 | highlight:model.edit.addClinicalFeatureQuery">
                        </span>
                    </a>

                    <div class="section-segment" ng-show="result.added">
                        <span class="btn btn-added">
                            <i class="ficon-ok"></i>
                        </span>
                        <span ng-bind-html-unsafe="result.name | truncate:40 | highlight:model.edit.addClinicalFeatureQuery"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--<div class="section-segment media-body">
        <a class="btn btn-save" href="" ng-enabled="orcidId.length >= 16" ng-click="importFromOrcid(orcidId)">Import</a>
        <a class="btn btn-cancel" href="" ng-click="hideImportFromOrcid()">Cancel</a>
    </div>-->
</my-modal>
