<?php
// Create some user access variables
$isRegistered = isset($user->uid);
$isCurator = is_array($user->roles) && in_array('sk_curator', $user->roles);
$isEditor = is_array($user->roles) && in_array('sk_editor', $user->roles);
$isAdmin = user_access('administer site configuration');
?>

<div ng-controller="StatementCtrl">
    <?php if ($user->uid): ?>

    <?php endif; ?>

    <!-- The statements sections -->
    <section class="statements">
        <a name="statements"></a>

        <div class="section-segment section-segment-header">
            <!-- BUTTONS! -->
            <div class="pull-right section-segment-header-buttons">

                <!-- Show Add statement button -->
                <?php if ($user->uid): ?>
                    <a ng-show="!model.isAddingStatement && !isEditingStatements" class="btn btn-success " ng-click="showAddStatement()"  href>
                        <i class="icon-plus icon-white"></i> Add Statement
                    </a>
                <?php endif; ?>

                <!-- Add statement buttons -->
                <span ng-show="model.isAddingStatement">
                    <button ng-disabled="!model.newStatement.length" ng-click="saveStatement(model.newStatement)" class="btn btn-success">
                        <i class="icon-ok icon-white"></i> Save Statement
                    </button>
                    <button ng-click="cancelStatement(model.newStatement)" class="btn">
                        <i class="icon-remove"></i> Cancel
                    </button>
                </span>


                <!-- Edit buttons -->
                <?php if($isAdmin || $isCurator): ?>
                    <span ng-show="!model.isAddingStatement">
                        <a ng-show="isEditingStatements" href
                           ng-click="hideEditStatements()"
                           data-toggle="modal" role="button"
                           class="btn btn-primary">
                            <i class="icon-ok icon-white"></i> Done
                        </a>

                        <a ng-show="!isEditingStatements" href
                           ng-click="showEditStatements()"
                           data-toggle="modal"
                           role="button"
                           class="btn">
                            <i class="icon-pencil"></i> Edit
                        </a>
                    </span>

                <?php endif; ?>

            </div>

            <!-- HEADING -->
            <h2>Statements ({{ statements.length }})</h2>
        </div>

        <div ng-show="model.isAddingStatement" class="section-segment section-segment-editor statement-new">
            <div ng-model="model.newStatement" ck-editor></div>
        </div>

        <?php if (!$user->uid): ?>
            <div class="section-segment alert alert-info" style="border-radius: 0; margin-bottom: 0">
                <div>
                    Statements let you <b>contribute your knowledge</b>.
                </div>
                <div style="margin-top: 7px">
                    <a class="btn btn-info" cm-popover cm-popover-content="loginForm">Login In</a>
                    <a class="btn btn-info" href="?q=user/register">Register</a>
                </div>
            </div>
        <?php else: ?>
            <div ng-show="!model.isAddingStatement" class="section-segment muted">
                <i class="ficon-info-sign"></i> Statements let you <b>contribute your knowledge</b> to Skeletome.
            </div>
        <?php endif; ?>

        <div ng-show="model.isloadingNewStatement" class="section-segment">
            <div class="refreshing-box">
                <i class="icon-refresh icon-refreshing"></i>
            </div>
        </div>

        <div class="section-segment alert alert-success" cm-alert="model.isloadingNewStatement">
            <i class="icon-ok"></i> New statement saved.
        </div>

        <div ng-cloak ng-hide="statements.length" class="section-segment muted">
            No statements.
        </div>



        <!-- Actual list of statements statements -->
        <div ng-cloak>
            <div ng-repeat="statement in statements">

                <div class="section-segment section-segment-statement" ng-click="showComments(statement)">

                    <div class="section-segment-statement-icons">
                        <span ng-show="!statement.isShowingComments">
                            <i class="icon-chevron-right icon-chevron-turn-down"></i>
                            <i class="icon-chevron-right icon-white icon-chevron-turn-down"></i>
                        </span>

                        <span ng-show="statement.isShowingComments">
                            <i class="icon-chevron-up"></i>
                            <i class="icon-chevron-up icon-white "></i>
                        </span>
                    </div>

                    <div class="section-segment-statement-text" ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                    </div>

                    <div class="section-segment-statement-interaction" ng-show="statement.isShowingComments || isEditingStatements">

                        <span class="label">
                            <i class="icon-user icon-white"></i> {{ statement.name || "Anonymous" | capitalize }}
                        </span>

                        <span class="label">
                            <i class="icon-comment icon-white"></i> {{ statement.comments.length || statement.comment_count }}
                        </span>

                        <span style="font-size: 11.844px; line-height: 14px; float: right; color: #bbb; margin-right: 30px;">
                            {{ statement.created*1000 | date:'MMM d, y - h:mm a' }}
                        </span>

                        <a ng-show="isEditingStatements"
                           ng-click="deleteStatement(statement)"
                           class="btn btn-danger pull-left" href style="color: white; margin-right: 14px;">
                            <i class="icon-remove icon-white"></i> Delete
                        </a>

                    </div>
                </div>

                <!-- List of comments -->
                <div ng-show="statement.isShowingComments">

                    <div ng-repeat="comment in statement.comments">
                        <div class="section-segment section-segment-inner-tabbed" ng-class="{'section-segment-inner-tabbed-shadow': $index == 0 && !statement.isLoadingComments }">
                            <div ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></div>

                            <div class="section-segment-statement-interaction" ng-show="statement.isShowingComments">
                                <a ng-show="isEditingStatements"
                                   ng-click="deleteCommentFromStatement(comment, statement)"
                                   class="btn btn-danger" href style="color: white; margin-right: 14px;">
                                    <i class="icon-remove icon-white"></i> Delete
                                </a>

                                <span class="label">
                                    <i class="icon-user icon-white"></i> {{ comment.name || "Anonymous" | capitalize }}
                                </span>

                                <span style="font-size: 11.844px; line-height: 14px; float: right; color: #bbb; margin-right: 45px;">
                                    {{ comment.created*1000 | date:'MMM d, y - h:mm a' }}
                                </span>

                            </div>
                        </div>
                    </div>

                    <div class="section-segment section-segment-inner-tabbed section-segment-inner-tabbed-shadow" ng-show="statement.isLoadingComments">
                        <div class="refreshing-box">
                            <i class="icon-refresh icon-refreshing"></i>
                        </div>
                    </div>

                    <!-- New Comment -->
                    <?php if ($user->uid): ?>
                    <div ng-show="!isEditingStatements" class="section-segment section-segment-inner-tabbed comment-new" ng-class="{ 'section-segment-inner-tabbed-shadow': !statement.comments.length && !statement.isLoadingComments }">
                        <textarea cm-focus="statement.showAddComment"
                                  cm-return="addComment(statement, statement.newComment)"
                                  ng-model="statement.newComment"
                                  placeholder="Write a comment about this statement."
                                  class="full-width">
                        </textarea>
                        <div class="pull-right">
                            <button ng-disabled="!statement.newComment.length" ng-click="addComment(statement, statement.newComment)" class="btn btn-success">
                                <i class="icon-ok icon-white"></i> Post
                            </button>
                            <button class="btn" ng-click="cancelComment(statement)">
                                <i class="icon-remove"></i> Cancel
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>


                    <!-- Statement text -->
                    <!--<div name="statement-{{ statement.nid }}" class="statement-content">-->

                        <!-- User info -->
                        <!--<div class="statement-content-user">


                            <div class="statement-content-user-inner">
                                <a ng-show="isEditingStatements"
                                   ng-click="deleteStatement(statement)"
                                   class="btn btn-danger" href>
                                    <i class="icon-remove icon-white"></i> Delete
                                </a>


                            </div>
                        </div>-->

                        <!-- Text -->





                        <!--<div class="statement-content-text" ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                        </div>-->

                        <!--<div class="statement-content-user">
                            {{ statement.name || "Anonymous" | capitalize }}
                            <a ng-click="showComments(statement)" href>
                                <i class="icon-comment"></i> {{ statement.comments.length || statement.comment_count }}
                            </a>
                        </div>-->

                    <!--</div>-->

                    <!-- List of comments -->
            </div>
        </div>
    </section>
</div>