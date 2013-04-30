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
                <?php if((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                    <span ng-show="!model.isAddingStatement">
                        <a ng-show="isEditingStatements" href
                           ng-click="hideEditStatements()"
                           data-toggle="modal" role="button"
                           class="btn btn-info">
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
            <h2>Statements</h2>
        </div>

        <div ng-show="model.isAddingStatement" class="section-segment section-segment-editor statement-new">
            <div ng-model="model.newStatement" ck-editor></div>
        </div>

        <?php if (!$user->uid): ?>
            <div class="alert alert-info" style="border-radius: 0; text-align: center; margin-bottom: 0">
                <div>
                    Statements let you <b>contribute your knowledge</b> about '{{ boneDysplasia.title || gene.title }}'.
                </div>
                <div style="margin-top: 7px">
                    <a class="btn btn-info" cm-popover cm-popover-content="loginForm">Login In</a>
                    <a class="btn btn-info" href="?q=user/register">Register</a>
                </div>
            </div>
        <?php else: ?>
            <div ng-show="!model.isAddingStatement" class="section-segment muted">
                <i class="icon-info-sign"></i> Statements let you <b>contribute your knowledge</b> about '{{ boneDysplasia.title || gene.title }}'.
            </div>
        <?php endif; ?>

        <div ng-show="model.isloadingNewStatement" class="section-segment">
            <div class="refreshing-box">
                <i class="icon-refresh icon-refreshing"></i>
            </div>
        </div>

        <div class="section-segment alert alert-success" cm-alert="model.isloadingNewStatement">
            New statement saved.
        </div>

        <div ng-cloak ng-hide="statements.length" class="section-segment muted">
            No statements.
        </div>



        <!-- Actual list of statements statements -->
        <div ng-cloak>
            <div class="section-segment section-segment-nopadding" ng-repeat="statement in statements" >

                <!-- Statement text -->
                <div name="statement-{{ statement.nid }}" class="statement-content">

                    <!-- User info -->
                    <div class="statement-content-user">


                        <div class="statement-content-user-inner">
                            <a ng-show="isEditingStatements"
                               ng-click="deleteStatement(statement)"
                               class="btn btn-danger" href>
                                <i class="icon-remove icon-white"></i> Delete
                            </a>

                            <div>
                                <b>{{ statement.name || "Anonymous" | capitalize }}</b>
                            </div>

                            <div class="statement-buttons">
                                <a ng-click="showComments(statement)" href>
                                    <i class="icon-comment"></i> {{ statement.comments.length || statement.comment_count }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Text -->
                    <div class="statement-content-text" ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                    </div>
                </div>

                <!-- List of comments -->
                <div ng-show="statement.isShowingComments" class="comments">

                    <div class="comment-content" ng-repeat="comment in statement.comments">

                        <!-- User info -->
                        <div class="comment-content-user">
                            <div class="segment-padding comment-content-user-inner">

                                <a ng-show="isEditingStatements"
                                   ng-click="deleteStatement(statement)"
                                   class="btn btn-danger" href>
                                    <i class="icon-remove icon-white"></i> Delete
                                </a>

                                <b>{{ comment.name || "Anonymous" | capitalize }}</b>
                            </div>
                        </div>

                        <div class="segment-padding comment-content-text" ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></div>
                    </div>


                    <div class="comment-content segment-padding" ng-show="statement.isLoadingComments">
                        <div class="refreshing-box">
                            <i class="icon-refresh icon-refreshing"></i>
                        </div>
                    </div>

                    <!-- New Comment -->
                    <?php if ($user->uid): ?>
                        <div class="comment-content segment-padding comment-new">
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

            </div>


            <div class="statement"  >

                <div class="media-body">



                    <div class="statement-user">

                    </div>
                    <div class="statement-content">

                    </div>



                    <div ng-show="statement.showComments || statement.showAddComment" class="statement-comments">
                        <!-- New Comment -->
                        <div ng-show="statement.showAddComment" class="statement-comment statement-comment-add" >

                            <div class="media-body">

                                <!--<div>
                                    <b>{{ user.name | capitalize }}</b>
                                </div>-->

                            </div>
                        </div>
                        <!-- /New Comment -->

                        <div ng-show="statement.showComments" ng-repeat="comment in statement.comments" class="statement-comment" >
                            <div class="pull-left statement-comment-profile"></div>

                            <div class="media-body">
                                <a ng-show="isEditingStatements"
                                   ng-click="deleteCommentFromStatement(comment, statement)" class="btn btn-danger pull-right" href>
                                    <i class="icon-remove icon-white"></i> Delete
                                </a>


                                <div>
                                    <b>{{ comment.name || "Anonymous" | capitalize }}</b>
                                </div>
                                <div ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
</div>