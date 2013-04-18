<section class="section-large" ng-class="{'section-more': statementDisplayLimit < statements.length}" ng-controller="StatementCtrl">
    <div class="pull-right">
        <?php if ($user->uid): ?>
            <a ng-show="!editingStatements" class="btn btn-success " ng-click="showAddStatement()"  href><i class="icon-plus icon-white"></i> Add Statement</a>
        <?php else: ?>
            <a href class="btn btn-success" cm-popover="top" cm-popover-content="'<b>Log in</b> to Skeletome to add a statement.'"><i class="icon-plus icon-white"></i> Add Statement</a>
        <?php endif; ?>
        <?php if((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
            <a ng-show="editingStatements" href
               ng-click="hideEditStatements()"
               data-toggle="modal" role="button"
               class="btn btn-primary">
                <i class="icon-ok icon-white"></i> Done
            </a>
            <a ng-show="!editingStatements && statements.length" href
               ng-click="showEditStatements()"
               data-toggle="modal"
               role="button"
               class="btn">
                <i class="icon-pencil"></i> Edit
            </a>
        <?php endif; ?>


    </div>

    <h2>Statements</h2>
    <p class="muted" style="margin-bottom: 20px">
        Statements let you contribute your knowledge and experiences to Skeletome.
    </p>

    <p ng-cloak class="muted" ng-hide="statements.length">
        There are currently 0 statements.
    </p>



    <div ng-cloak ng-show="statements.length > 0" class="statements">

        <div class="statement" ng-repeat="statement in statements | limitTo: statementDisplayLimit" style="margin-bottom: 21px" >

            <div class="pull-left statement-profile" style="margin-right: 14px;">
            </div>
            <div class="media-body">

                <a ng-show="editingStatements"
                   ng-click="deleteStatement(statement)"
                   class="btn btn-danger pull-right" href>
                    <i class="icon-remove icon-white"></i> Delete
                </a>

                <div class="statement-user">
                    <b>{{ statement.name || "Anonymous" | capitalize }}</b>
                </div>
                <div class="statement-content">
                    <div ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'"></div>
                </div>

                <div class="statement-buttons">
                    <!-- Add Comment -->
                    <?php if ($user->uid): ?>
                        <a ng-show="!statement.showAddComment && !editingStatements" ng-click="showAddComments(statement)" href><i class="icon-plus"></i> Add Comment</a>
                        <a ng-show="statement.showAddComment" ng-click="statement.showComments = false; statement.showAddComment = false;" href><i class="icon-plus"></i> Hide Add Comment</a>
                    <?php endif; ?>


                    <!-- Show / Hide Comments -->
                    <!-- Displayed when no comments -->
                    <span ng-hide="statement.comment_count"> Comments (0)</span>
                    <!-- Displayed when there are comments -->
                                    <span ng-show="statement.comment_count">
                                        <a ng-hide="statement.showComments" ng-click="showComments(statement)" href> Show Comments ({{ statement.comments.length || statement.comment_count }})</a>
                                        <a ng-show="statement.showComments" ng-click="statement.showComments = false; statement.showAddComment = false;" href> Hide Comments ({{ statement.comments.length || statement.comment_count }})</a>
                                    </span>
                </div>

                <div ng-show="statement.showComments || statement.showAddComment" class="statement-comments">
                    <!-- New Comment -->
                    <div ng-show="statement.showAddComment" class="statement-comment statement-comment-add" >
                        <div class="pull-left statement-comment-profile"></div>

                        <div class="media-body">

                            <div>
                                <b>{{ user.name | capitalize }}</b>
                            </div>

                            <div class="statement-comments-new">
                                <textarea cm-focus="statement.showAddComment" cm-return="addComment(statement, statement.newComment)" ng-model="statement.newComment" placeholder="Write a comment about this statement." class="full-width"></textarea>
                                <div class="clearfix">
                                    <!--<a ng-click="addComment(statement, statement.newComment)" href class="btn btn-success pull-right"><i class="icon-plus icon-white"></i> Add Comment</a>-->
                                </div>
                                <div class="media-body">
                                    <a ng-click="addComment(statement, statement.newComment)" class="btn btn-success pull-right" href>Add Comment</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /New Comment -->

                    <div ng-show="statement.showComments" ng-repeat="comment in statement.comments" class="statement-comment" >
                        <div class="pull-left statement-comment-profile"></div>

                        <div class="media-body">
                            <a ng-show="editingStatements"
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

    <div class="clearfix" style="text-align: center">
        <a ng-show="statementDisplayLimit < statements.length" ng-click="statementDisplayLimit = statements.length" href class="btn btn-more"><i class="icon-chevron-down icon-black"></i> Show All ({{statements.length - statementDisplayLimit}})</a>
        <a ng-show="statementDisplayLimit == statements.length" ng-click="statementDisplayLimit = 2" href class="btn btn-more"><i class="icon-chevron-up icon-black"></i> Show Less</a>
    </div>
</section>