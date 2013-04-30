<div ng-controller="DescriptionCtrl">
    <section style="margin-bottom: 14px">

        <div class="section-segment section-segment-header">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="section-segment-header-buttons pull-right">

                    <!-- is Editing Description -->
                <span ng-show="!isEditingDescription">
                    <a ng-show="!showEditDescription"
                       href class="btn"
                       ng-click="editDescription()">
                        <i class="icon-pencil"></i> Edit
                    </a>
                </span>

                    <!-- Not Editing Description -->
                <span ng-show="isEditingDescription">
                    <a href class="btn btn-success"
                       ng-click="saveEditedDescription(editedDescription)">
                        <i class="icon-ok icon-white"></i> Save Description
                    </a>

                    <a ng-show="isEditingDescription"
                       href class="btn"
                       ng-click="cancelEditingDescription()">
                        <i class="icon-remove"></i> Cancel
                    </a>
                </span>

                </div>
            <?php endif; ?>

            <div>
                <b>Contributors</b>
                            <span ng-repeat="editor in editors">
                                {{ editor.name | capitalize }},
                            </span>
                <span>Gene Reviews</span>
            </div>
        </div>

        <div class="section-segment alert alert-info" ng-show="!isEditingDescription">
            <div style="margin-bottom: 7px;">
                <i class="icon-info-sign"></i> <b>This is a stub sourced from GeneReviews</b>.
            </div>
            <div style="font-size: 12px">
                Francomano, CA, (Updated January 9, 2006). Achondroplasia. In: Pagon RA, Bird TD, Dolan CR, et al. editors GeneReviews [Internet]. Copyright, University of Washington, Seattle. 1997-2011. Available at http://www.ncbi.nlm.nih.gov/pubmed/20301295
            </div>

        </div>
        <div class="section-segment alert alert-success" cm-alert="description.isLoading">
            Description saved.
        </div>

        <div class="section-segment" ng-class="{ 'section-segment-nopadding': isEditingDescription }">

            <!-- is Editing Description -->
            <div ng-show="isEditingDescription">
                <textarea ck-editor height="800px" ng-model="editedDescription"></textarea>
            </div>


            <!-- Not Editing Description -->
            <div ng-show="!isEditingDescription" class="description-text">
                <!-- is Loading -->
                <div ng-show="description.isLoading" class="refreshing-box">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>

                <p class="muted" ng-show="!description.safe_value.length">There is currently no
                    description of '{{ master.gene.title || boneDysplasia.title }}'.</p>

                <!--  | truncate:descriptionLength -->
                <div ng-show="description.safe_value.length && !description.isLoading">
                    <div ng-bind-html-unsafe="description.safe_value">
                        <?php echo render($content); ?>
                    </div>

                    <!--<div class="clearfix" style="text-align: center">
                        <a href ng-show="boneDysplasia.body.und[0].safe_value.length > descriptionLength"
                           class="btn btn-more"
                           ng-click="descriptionLength=boneDysplasia.body.und[0].safe_value.length"><i
                                class="icon-chevron-down icon-black"></i> Show All</a>
                        <a href ng-show="descriptionLength == boneDysplasia.body.und[0].safe_value.length"
                           class="btn btn-more" ng-click="descriptionLength=1000"><i
                                class="icon-chevron-up icon-black"></i> Hide</a>
                    </div>-->
                </div>
            </div>
        </div>
    </section>
</div>