<div ng-controller="DescriptionCtrl">
    <section style="margin-bottom: 14px">

        <div class="section-segment section-segment-headers section-segment-header-editors" ng-class="{ 'section-segment-editing': model.isEditingDescription }">
            <?php
            if ($isAdmin || $isCurator || $isEditor): ?>
            <div class="section-segment-header-buttons pull-right">

                    <!-- is Editing Description -->
                <span ng-show="!model.isEditingDescription">
                    <a ng-show="!showEditDescription"
                       href class="btn btn-edit"
                       ng-click="editDescription()">
                        <i class="ficon-pencil"></i> Edit
                    </a>
                </span>

                    <!-- Not Editing Description -->
                <span ng-show="model.isEditingDescription">
                    <a href class="btn btn-save"
                       ng-click="saveEditedDescription(model.editedDescription)">
                        <i class="ficon-ok icon-white"></i> Save
                    </a>

                    <a href class="btn btn-cancel"
                       ng-click="cancelEditingDescription()">
                        <i class="ficon-remove"></i> Cancel
                    </a>
                </span>

            </div>
            <?php endif; ?>

            <div ng-show="!model.isEditingDescription">
                <b><i class="ficon-user"></i> Contributors</b>
                <span ng-repeat="editor in editors">
                    <a href="?q=profile-page/{{ editor.uid }}">{{ editor.name | capitalize }}</a><span ng-show=" ! $last ">,</span><span ng-show="$last && provider.length">, {{ provider }}</span>
                </span>
            </div>

            <?php if(isset($user->name)):?>
            <div ng-show="model.isEditingDescription">
                <b><i class="ficon-user"></i> You are editing</b> <span style="color: #ccc">(<?php echo $user->name; ?>)</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="section-segment alert alert-success" cm-alert="description.isLoading">
            <i class="ficon-ok"></i> Description saved.
        </div>

        <div ng-show="model.isEditingDescription && model.statementPackage" class="section-segment section-segment-editing">
            <div>
                <b>Step 1 - Add to Abstract</b>
            </div>
            <div>
                You can copy and paste this text into the Abstract.
            </div>
            <div ng-bind-html-unsafe="model.statementPackage.text" class="alert alert-info">
                Statement text
            </div>

            <div>
                <b>Step 2 - Choose which users to acknowledge.</b>
            </div>
            <div ng-repeat="user in model.statementPackage.users">
                <input type="checkbox" ng-model="user.approved" ng-init="user.approved = true"/> {{ user.name }}
            </div>

        </div>

        <div class="section-segment" ng-class="{ 'section-segment-nopadding': model.isEditingDescription }">

            <!-- is Editing Description -->
            <div ng-show="model.isEditingDescription">
                <textarea ck-editor height="800px" ng-model="model.editedDescription"></textarea>
            </div>


            <!-- Not Editing Description -->
            <div ng-show="!model.isEditingDescription" class="description-text">

                <div class="alert alert-stub" ng-show="provider && !model.isEditingDescription">
                    <i class="ficon-info-sign"></i> <em>This stub is sourced from {{ provider }}</em>.
                    <div style="font-size: 12px" ng-bind-html-unsafe="reference">
                    </div>
                </div>

                <!-- is Loading -->
                <div ng-show="description.isLoading" class="refreshing-box">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>

                <p class="muted" ng-show="!description.safe_value.length">There is currently no
                    description of '{{ master.gene.title || boneDysplasia.title }}'.</p>

                <!--  | truncate:descriptionLength -->
                <div ng-show="description.safe_value.length && !description.isLoading">
                    <div ng-bind-html-unsafe="description.safe_value | truncate:descriptionLength">
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
                    <div ng-show="description.value.length > defaultDescriptionLength">
                        <button ng-show="isHidingDescription" ng-click="showDescription()" class="btn btn-reveal" >Show All</button>
                        <button ng-show="!isHidingDescription" ng-click="hideDescription()" class="btn btn-reveal" >Hide</button>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>