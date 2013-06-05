<style type="text/css">
    .profile-page-picture {
        width: 100%;
        display: block;
    }


    .edit-picture-button {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 100;;
    }
    .isEditing.edit-picture-button {
        position: inherit;
        float: right;
    }
    .profile-page-picture {

    }
</style>
<?php
    global $user;
    $canEdit = ((arg(1) == $user->uid) || user_access('administer site configuration'));;
?>
<div ng-controller="ProfileCtrl" ng-init="init()">

    <div class="row">
        <div class="span12">
            <div class="page-heading">
                <?php if($canEdit): ?>
                <div class="pull-right">
                    <!-- LinkedIn Buttons -->
                    <a ng-click="showImportFromLinkedIn()" ng-show="linkedIn.isAuthenticated" class="btn btn-white btn-global" href="" ><i class="icon-download-alt"></i> Import from <img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/linkedin.png"/></a>
                    <a ng-show="!linkedIn.isAuthenticated" ng-click="linkedIn.disabled = true" ng-disabled="{{ linkedIn.disabled }}" class="btn btn-white btn-global" href="{{ linkedIn.disabled && '' || linkedIn.authUrl}}"><i class="icon-download-alt"></i> Import from <img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/linkedin.png"/></a>

                    <!-- Import from Orcid Button (no authentication needed) -->
                    <a ng-click="showImportFromOrcid()" href="" class="btn btn-white btn-global"><i class="icon-download-alt"></i> Import from <img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/orcid-logo.png"/></a>
                </div>
                <?php endif; ?>

                <h1>
                    <img class="type-logo" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/user.svg"/>
                    {{ user.name | capitalize }}
                </h1>
            </div>
        </div>
    </div>

    <!-- Import from Orcid -->
    <div ng-show="isShowingImportFromOrcid" class="row">
        <div class="span12">
            <section>
                <div class="section-segment section-segment-header">
                    <h3>Import from Orcid</h3>
                </div>

                <div class="section-segment" ng-show="isLoadingImportFromOrcid">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>

                <div ng-show="!isLoadingImportFromOrcid">
                    <div class="section-segment">
                        Orcid ID <input type="text" ng-model="orcidId" placeholder="0000-0000-0000-0000"/>
                    </div>
                    <div class="section-segment">
                        <input type="checkbox" name="vehicle" ng-model="orcidImportFields.biography" checked="checked"> Biography
                    </div>
                    <div class="section-segment">
                        <input type="checkbox" name="vehicle" ng-model="orcidImportFields.works" checked="checked"> Works / Publications
                    </div>
                    <div class="section-segment" >
                        <a class="btn btn-success" href="" ng-disabled="!orcidId.length" ng-click="importFromOrcid(orcidId)"><i class="icon-download icon-white"></i> Import Now</a>
                        <a class="btn" href="" ng-click="hideImportFromOrcid()">Cancel</a>
                    </div>
                </div>

            </section>
        </div>
    </div>


    <!-- Import from LinkedIn -->
    <div ng-show="linkedIn.justGranted || isShowingImportFromLinkedIn" class="row">
        <div class="span12">
            <section>
                <div class="section-segment section-segment-header">
                    <h3>Import from LinkedIn</h3>
                </div>
                <div class="section-segment" ng-show="isLoadingImportFromLinkedIn">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>

                <div ng-show="!isLoadingImportFromLinkedIn">
                    <div class="section-segment">
                        <input type="checkbox" name="vehicle" ng-model="linkedInImportFields.summary" checked="checked"> Summary / Biography
                    </div>
                    <div class="section-segment">
                        <input type="checkbox" name="vehicle" ng-model="linkedInImportFields.position" checked="checked"> Position
                    </div>
                    <div class="section-segment">
                        <input type="checkbox" name="vehicle" ng-model="linkedInImportFields.location" checked="checked"> Location
                    </div>

                    <div class="section-segment">
                        <a class="btn btn-success" href="" ng-click="importFromLinkedIn()"><i class="icon-download icon-white"></i> Import Now</a>
                        <a class="btn" href="" ng-click="hideImportFromLinkedIn()">Cancel</a>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <div class="row">
        <div class="span3">
            <section>

                <div class="section-segment" ng-class="{ 'section-segment-editing': detailsState=='isEditing', 'section-segment-nopadding': detailsState=='isDisplaying' }" style="position: relative;">

                    <?php if($canEdit): ?>
                    <div ng-class="{'media-body': detailsState=='isEditing'}">
                        <div class="edit-picture-button" ng-class="{'isEditing': detailsState=='isEditing'}">
                            <div ng-switch on="detailsState">
                                <div ng-switch-when="isLoading">
                                </div>
                                <div ng-switch-when="isEditing">
                                    <a ng-click="saveDetails(edit.profile)" href role="button" class="btn btn-success">
                                        <i class="icon-ok icon-white"></i> Save
                                    </a>
                                    <a ng-click="cancelDetails()" href role="button" class="btn">
                                        <i class="icon-remove"></i> Cancel
                                    </a>

                                </div>
                                <div ng-switch-when="isDisplaying">
                                    <a ng-click="editDetails()" href data-toggle="modal" role="button" class="btn">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div ng-switch on="detailsState">
                        <div ng-switch-when="isLoading">
                            <div class="refreshing-box">
                                <i class="icon-refresh icon-refreshing"></i>
                            </div>
                        </div>
                        <div ng-switch-when="isEditing">
                            <div class="dropzone" ng-model="profilePics.files"
                                 drop-zone-upload="?q=upload/images" drop-zone-message="<b>Drop Profile picture</b> in here to upload (or click here).">
                            </div>
                            <img ng-show="!edit.profile.field_profile_image.und.length" style="opacity: 0.5" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/user_black.svg" class="profile-page-picture"/>
                            <img ng-show="edit.profile.field_profile_image.und.length"  ng-src="{{ edit.profile.field_profile_image.und[0].full_url }}" alt=""  class="profile-page-picture"/>
                        </div>
                        <div ng-switch-when="isDisplaying">
                            <div ng-show="!profile.field_profile_image.und.length" style="padding: 20px">
                                <img class="profile-page-picture" style="opacity: 0.5" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/user_black.svg"/>
                            </div>

                            <img class="profile-page-picture" ng-show="profile.field_profile_image.und.length"  ng-src="{{ profile.field_profile_image.und[0].full_url }}" alt=""/>
                        </div>
                    </div>

                    <!--<img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/templates/andreaszankl.jpg"/>-->
                </div>
                <!--<div class="section-segment sectioh-segment-header">
                    <h3>Details</h3>
                </div>-->

                <div ng-repeat="role in roles" class="section-segment">
                    <b>Skeletome</b> {{ role }}
                </div>

                <div class="section-segment">
                    <b>Member Since</b> {{ user.created*1000 | date:'MMM d, y'}}
                </div>

            </section>

            <section>
                <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': professionalState=='isEditing' }">
                    <?php if($canEdit): ?>
                    <div class="section-segment-header-buttons">
                        <div class="pull-right">
                            <div ng-switch on="professionalState">
                                <div ng-switch-when="isLoading">
                                </div>
                                <div ng-switch-when="isEditing">
                                    <a ng-click="saveProfessional(edit.profile)" href role="button" class="btn btn-success">
                                        <i class="icon-ok icon-white"></i> Save
                                    </a>
                                    <a ng-click="cancelProfessional()" href role="button" class="btn">
                                        <i class="icon-remove"></i> Cancel
                                    </a>
                                </div>
                                <div ng-switch-when="isDisplaying">
                                    <a ng-click="editProfessional()" href data-toggle="modal" role="button" class="btn">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <h3>Professional</h3>
                </div>

                <div ng-switch on="professionalState">
                    <div ng-switch-when="isLoading">
                        <div class="section-segment refreshing-box">
                            <i class="icon-refresh icon-refreshing"></i>
                        </div>
                    </div>
                    <div ng-switch-when="isEditing">
                        <div class="section-segment section-segment-editing">
                            <b>Position</b>
                            <div>
                                <input class="full-width" ng-model="edit.profile.field_profile_position.und[0].value" type="text"/>
                            </div>
                        </div>

                        <div class="section-segment section-segment-editing">
                            <b>Location</b>
                            <div>
                                <input class="full-width" ng-model="edit.profile.field_profile_location.und[0].value" type="text"/>
                            </div>
                        </div>

                    </div>
                    <div ng-switch-when="isDisplaying">
                        <div class="section-segment">
                            <b>Position</b>
                            <span ng-show="profile.field_profile_position.und[0].value.length">
                                {{ profile.field_profile_position.und[0].value }}
                            </span>
                            <span ng-show="!profile.field_profile_position.und[0].value.length" class="muted">
                                Not recorded.
                            </span>
                        </div>

                        <div class="section-segment">
                            <b>Location</b>
                            <span ng-show="profile.field_profile_location.und[0].value.length">
                                {{ profile.field_profile_location.und[0].value }}
                            </span>
                            <span ng-show="!profile.field_profile_location.und[0].value.length" class="muted">
                                Not recorded.
                            </span>

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="span6">
            <section>
                <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': biographyState=='isEditing' }">
                    <?php if($canEdit): ?>
                    <div class="section-segment-header-buttons">
                        <div class="pull-right">
                            <div ng-switch on="biographyState">
                                <div ng-switch-when="isLoading">
                                </div>
                                <div ng-switch-when="isEditing">
                                    <a ng-click="saveBiography(edit.profile)" href role="button" class="btn btn-success">
                                        <i class="icon-ok icon-white"></i> Save
                                    </a>
                                    <a ng-click="cancelBiography()" href role="button" class="btn">
                                        <i class="icon-remove"></i> Cancel
                                    </a>
                                </div>
                                <div ng-switch-when="isDisplaying">
                                    <a ng-click="editBiography()" href data-toggle="modal" role="button" class="btn">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <h2>Biography</2>
                </div>

                <div ng-switch on="biographyState">
                    <div ng-switch-when="isLoading">
                        <div class="section-segment refreshing-box">
                            <i class="icon-refresh icon-refreshing"></i>
                        </div>
                    </div>
                    <div ng-switch-when="isEditing">
                        <div class="section-segment section-segment-nopadding">
                            <textarea ck-editor height="800px" ng-model="edit.profile.body.und[0].value"></textarea>
                        </div>
                    </div>
                    <div ng-switch-when="isDisplaying">
                        <div class="section-segment">

                            <div ng-show="profile.body.und[0].safe_value.length" ng-bind-html-unsafe="profile.body.und[0].safe_value">
                            </div>
                            <div ng-show="!profile.body.und[0].safe_value.length" class="muted">
                                This user has no biography.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="section-segment section-segment-header">
                    <h3>Publications</h3>
                </div>

                <div ng-switch on="publicationsState">
                    <div ng-switch-when="isLoading">
                        <div class="section-segment refreshing-box">
                            <i class="icon-refresh icon-refreshing"></i>
                        </div>
                    </div>
                    <div ng-switch-when="isEditing">
                    </div>
                    <div ng-switch-when="isDisplaying">
                        <div class="section-segment muted media-body" ng-show="!profile.field_profile_publications.und.length">
                            <a ng-click="showImportFromOrcid()" href="" class="btn btn-white btn-global pull-right"><i class="icon-download-alt"></i> Import from <img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/orcid-logo.png"/></a>

                            No publications.
                        </div>
                        <div ng-repeat="publication in profile.field_profile_publications.und | limitTo:publicationDisplayLimit" class="section-segment" ng-bind-html-unsafe="publication.value">
                        </div>
                        <div class="section-segment" ng-show="profile.field_profile_publications.und.length">
                            <a ng-show="isHidingPublications" ng-click="showAllPublications()" class="btn btn-reveal" href="">Show All</a>
                            <a ng-show="!isHidingPublications" ng-click="hidePublications()" class="btn btn-reveal" href="">Show Less</a>
                        </div>
                    </div>
                </div>
            </section>


            <section>
                <div class="section-segment section-segment-header">
                    <h2>Recent Activity</2>
                </div>
                <div ng-show="!activity.length" class="section-segment muted">
                    No recent activity.
                </div>
                <div ng-repeat="item in activity | limitTo:recentActivityDisplayLimit">

                    <a ng-show="!item.cid" class="section-segment" href="?q=node/{{ item.target_nid }}">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        <p><i class="icon-statement"></i> <b>Statement</b> added to <b>{{ item.target_title }}</b> <span class="muted" style="margin-left: 7px">{{ item.created*1000 | date:'MMM d, y' }}</span></p>

                        <div>
                            <span>"</span><span ng-bind-html-unsafe="item.body | truncate:200"></span><span>"</span>
                        </div>

                    </a>

                    <a ng-show="item.cid" class="section-segment" href="?q=node/{{ item.target_nid }}">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        <p><i class="ficon-comment"></i> <b>Comment</b> added to a statement on <b>{{ item.target_title }}</b> <span class="muted" style="margin-left: 7px">{{ item.created*1000 | date:'MMM d, y' }}</span></p>

                        <div>
                            <span>"</span><span ng-bind-html-unsafe="item.body | truncate:200"></span><span>"</span>
                        </div>
                        <div >
                        </div>

                    </a>
                </div>

            </section>
        </div>
        <div class="span3">
            <section ng-show="contributed.length">
                <div class="section-segment section-segment-header">
                    <h3>Contributed to Pages</h3>
                </div>

                <div ng-repeat="page in contributed | limitTo:contributedDisplayLimit">
                    <a href="?q=node/{{ page.nid }}" class="section-segment">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        {{ page.title }}
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>

