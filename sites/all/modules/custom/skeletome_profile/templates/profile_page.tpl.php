<style type="text/css">
    .profile-page-picture {
        width: 100%;
        display: block;
    }
    .section-segment.section-segment-editing {
        background-color: rgb(255, 251, 224);
    }
    .section-segment-editing input, .section-segment-editing textarea {
        border: 1px solid rgb(241, 219, 78);
    }
    .section-segment-editing input:focus, .section-segment-editing textarea:focus {
        outline: none;
        border: 1px solid rgb(255, 234, 82);
        box-shadow: none;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
    }
    .section-segment-editing textarea {
        height: 100px;;
    }

    .edit-picture-button {
        position: absolute;
        top: 20px;
        right: 20px;
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
                <h1><img class="type-logo" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/user.svg"/>
                    {{ user.name }}
                </h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="span4">
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

                    <div style="background-color: white;">

                    </div>

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
        <div class="span8">
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
                    <h2>Recent Activity</2>
                </div>
                <div ng-show="!activity.length" class="section-segment muted">
                    No recent activity.
                </div>
                <div ng-repeat="item in activity | limitTo:recentActivityDisplayLimit">

                    <a ng-show="!item.cid" class="section-segment" href="?q=node/{{ item.target_nid }}">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        <p><img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/article-icon.png"/> <b>Statement</b> added to <b>{{ item.target_title }}</b> <span class="muted" style="margin-left: 7px">{{ item.created*1000 | date:'MMM d, y' }}</span></p>

                        <div>
                            <span>"</span><span ng-bind-html-unsafe="item.body | truncate:200"></span><span>"</span>
                        </div>

                    </a>

                    <a ng-show="item.cid" class="section-segment" href="?q=node/{{ item.target_nid }}">
                        <i class="icon-chevron-right pull-right"></i>
                        <i class="icon-chevron-right icon-white pull-right"></i>

                        <p><img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_profile'); ?>/img/comment-icon.png"/> <b>Comment</b> added to a statement on <b>{{ item.target_title }}</b> <span class="muted" style="margin-left: 7px">{{ item.created*1000 | date:'MMM d, y' }}</span></p>

                        <div>
                            <span>"</span><span ng-bind-html-unsafe="item.body | truncate:200"></span><span>"</span>
                        </div>
                        <div >
                        </div>

                    </a>
                </div>

            </section>
        </div>
    </div>
</div>

