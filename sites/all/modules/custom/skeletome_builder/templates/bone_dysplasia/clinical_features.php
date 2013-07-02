
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

                            <save-button click="saveClinicalFeatures()"></save-button>

                            <cancel-button click="cancelClinicalFeatures()"></cancel-button>

                            <div class="header-divider"></div>

                            <a ng-click="showAddClinicalFeature()" class="btn btn-cancel" href >
                                <i class="ficon-plus-sign"></i> Add
                            </a>

                        </div>
                        <div ng-switch-when="isDisplaying">
                            <edit-button click="editClinicalFeatures()"></edit-button>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div ng-switch on="model.clinicalFeaturesState">
            <div ng-switch-when="isEditing">
                <h2><i>Editing Clinical Features ({{ model.edit.clinicalFeatures.length }})</i></h2>
            </div>
            <div ng-switch-when="isDisplaying">
                <h2>Clinical Features ({{ model.clinicalFeatures.length }})</h2>
            </div>
        </div>

    </div>

    <cm-alert state="model.clinicalFeaturesState" from="isLoading" to="isDisplaying">
        <i class="ficon-ok"></i> Clinical Features Updated.
    </cm-alert>

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
                <search model="model.edit.clinicalFeatureQuery" placeholder="Find a Clinical Feature"  placeholder="Search for a Clinical Feature"></search>
            </div>

            <!-- Existing Clinical Features -->
            <remove-list list-model="model.edit.clinicalFeatures" filter-by="model.edit.clinicalFeatureQuery"></remove-list>

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

                <div ng-repeat="clinicalFeature in model.clinicalFeatures | filter:model.clinicalFeatureFilter | orderBy:'information_content' | limitTo:model.clinicalFeaturesDisplayLimit">
                    <a  style="overflow: hidden" class="section-segment" href="?q=taxonomy/term/{{clinicalFeature.tid}}">
                        <i class="ficon-angle-right pull-right"></i>

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
                <cm-reveal model="model.clinicalFeatures" showing-count="model.clinicalFeaturesDisplayLimit" default-count="10"></cm-reveal>
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
        <lookup url="?q=ajax/clinical-features/search/" placeholder="Search for a Clinical Feature" query="model.edit.addClinicalFeatureQuery" results="model.edit.clinicalFeaturesSearchResults" is-loading="model.edit.clinicalFeatureLoading"></lookup>
    </div>
    <add-list list-model="model.edit.clinicalFeaturesSearchResults" add-to-model="model.edit.clinicalFeatures"></add-list>

</my-modal>
