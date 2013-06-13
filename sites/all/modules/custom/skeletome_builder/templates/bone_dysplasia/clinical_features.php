
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
                            <a href ng-click="cancelClinicalFeatures()" class="btn btn-cancel">
                                <i class="ficon-remove"></i> Cancel
                            </a>

                            <a href ng-click="saveClinicalFeatures()" class="btn btn-save">
                                <i class="ficon-ok"></i> Save
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
        <h2>Clinical Features ({{ model.clinicalFeatures.length }})</h2>
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
            <div class="section-segment section-segment-editing">
                <form style="margin-bottom: 0">
                    <search model="model.edit.clinicalFeatureQuery" placeholder="Find a Clinical Feature to Add or Remove" change="searchForClinicalFeature(model.edit.clinicalFeatureQuery)" placeholder="Search for a Clinical Feature"></search>
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

            <!-- Search Results -->
            <div ng-switch on="model.edit.clinicalFeaturesSearchResultsState">
                <div ng-switch-when="isLoading">
                    <div class="section-segment section-segment-editing refreshing-box" ng-show="model.edit.clinicalFeaturesSearchResultsState">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>
                <div ng-switch-when="isDisplaying">
                    <div ng-show="model.edit.clinicalFeatureQuery.length" ng-repeat="result in model.edit.clinicalFeaturesSearchResults">
                        <div style="overflow: hidden" class="section-segment section-segment-editing">
                            <div style="width: 60%; float: left" ng-bind-html-unsafe="result.name | truncate:40 | capitalize | highlight:model.edit.clinicalFeatureQuery">
                                {{ result.name | truncate:40 | highlight:model.edit.clinicalFeatureQuery | capitalize }}
                            </div>

                            <a ng-show="result.added" ng-click="toggleClinicalFeatureResult(result)" class="btn btn-remove" href="">Remove</a>
                            <a ng-show="!result.added" ng-click="toggleClinicalFeatureResult(result)" class="btn btn-add" href="">Add</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Clinical Features -->
            <div ng-show="!model.edit.clinicalFeatureQuery.length" ng-repeat="clinicalFeature in model.edit.clinicalFeatures | filter:model.edit.clinicalFeatureQuery">
                <div style="overflow: hidden" class="section-segment section-segment-editing" href="?q=node/{{ model.boneDysplasia.nid }}/clinical-feature/{{clinicalFeature.tid}}">

                    <div style="width: 60%; float: left">
                        {{clinicalFeature.name | truncate:40 | capitalize}}
                    </div>

                    <a ng-show="clinicalFeature.added" ng-click="toggleClinicalFeature(clinicalFeature)" class="btn btn-remove" href="">Remove</a>
                    <a ng-show="!clinicalFeature.added" ng-click="toggleClinicalFeature(clinicalFeature)" class="btn btn-add" href="">Add</a>
                </div>
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