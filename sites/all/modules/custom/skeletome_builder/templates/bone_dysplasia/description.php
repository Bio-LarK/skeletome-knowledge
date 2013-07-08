<div ng-controller="DescriptionCtrl">
    <section style="margin-bottom: 14px">
        <div class="section-segment section-segment-header" ng-class="{'section-segment-editing': model.isEditingDescription }">
            <?php
            if ($isAdmin || $isCurator || $isEditor): ?>
                <div class="section-segment-header-buttons pull-right">

                    <!-- is Editing Description -->
                <span ng-show="!model.isEditingDescription && !showEditDescription">
                    <edit-button click="editDescription()"></edit-button>
                </span>

                    <!-- Not Editing Description -->
                <span ng-show="model.isEditingDescription">
                    <save-button click="saveEditedDescription(model.editedDescription)"></save-button>
                    <cancel-button click="cancelEditingDescription()"></cancel-button>
                </span>

                </div>
            <?php endif; ?>


            <h3>Abstract</h3>

        </div>
        <div class="section-segment section-segment-headers section-segment-header-editors" ng-class="{ 'section-segment-editing': model.isEditingDescription }">


            <div ng-show="!model.isEditingDescription">
                <i style="color: rgb(118, 150, 177);" cm-tooltip cm-tooltip-content="Contributors" class="ficon-group"></i>
                <span ng-repeat="editor in editors">
                    <a class="contributor" href="?q=profile-page/{{ editor.uid }}"><i class="ficon-user"></i> {{ editor.name | capitalize }}</a>
                </span>
                <a ng-show="provider.length" cm-popover="top" cm-popover-content="<b>This abstract was originally sourced from <em>{{ provider }}</em>.</b><br/> {{ reference }}"  href class="contributor"><i class="ficon-globe"></i> {{ provider }}</a>
            </div>

            <?php if(isset($user->name)):?>
            <div ng-show="model.isEditingDescription">
                <b><i class="ficon-user"></i> You are editing</b> <span style="color: #ccc">(<?php echo $user->name; ?>)</span>
            </div>
            <?php endif; ?>
        </div>

        <cm-alert state="model.isEditingDescription" from="true" to="false">
            <i class="ficon-ok"></i> Description Saved
        </cm-alert>

        <div ng-show="model.isEditingDescription && model.statementPackage" class="section-segment section-segment-editing">
            <div>
                <b>Add to Abstract</b>
            </div>
            <div>
                You can copy and paste this text into the Abstract.
            </div>
            <div ng-bind-html-unsafe="model.statementPackage.text" class="alert alert-info">
                Statement text
            </div>

        </div>

        <div class="section-segment" ng-class="{ 'section-segment-nopadding': model.isEditingDescription }">

            <!-- is Editing Description -->
            <div ng-show="model.isEditingDescription">
                <textarea ck-editor height="800px" ng-model="model.editedDescription"></textarea>
            </div>


            <!-- Not Editing Description -->
            <div ng-show="!model.isEditingDescription" class="description-text">

                <!--<div class="alert alert-info" ng-show="provider && !model.isEditingDescription">
                    <i class="ficon-info-sign"></i> <em>This stub is sourced from {{ provider }}</em>.
                    <div style="font-size: 12px" ng-bind-html-unsafe="reference">
                    </div>
                </div>-->

                <!-- is Loading -->
                <div ng-show="description.isLoading" class="refreshing-box">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>

                <p class="muted" ng-show="!description.safe_value.length">No description.</p>

                <!--  | truncate:descriptionLength -->
                <div ng-show="description.safe_value.length && !description.isLoading">
                    <div ng-bind-html-unsafe="description.safe_value | truncate:model.descriptionLength">
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
        <cm-reveal model="description.safe_value" showing-count="model.descriptionLength" default-count="1000"></cm-reveal>

    </section>
</div>