<div ng-controller="StatementCtrl" ng-init="init()">

    <!-- The statements sections -->
    <section class="statements">
        <a name="statements"></a>

        <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': model.statementsState == 'isEditing' || model.statementsState == 'isAdding' }">
            <!-- BUTTONS! -->
            <div class="pull-right section-segment-header-buttons">

                <div ng-switch on="model.statementsState">
                    <div ng-switch-when="isLoading">
                    </div>

                    <div ng-switch-when="isAdding">
                        <span ng-show="model.newStatement.length">
                            <save-button  click="saveStatement(model.newStatement)"></save-button>
                        </span>

                        <cancel-button click="cancelStatements()"></cancel-button>
                    </div>

                    <div ng-switch-when="isEditing">
                        <save-button click="saveStatements(model.edit.statements)"></save-button>
                        <cancel-button click="cancelStatements()"></cancel-button>
                    </div>

                    <div ng-switch-when="isApproving">
                        <cancel-button click="cancelStatements()"></cancel-button>
                    </div>

                    <div ng-switch-when="isDisplaying">
                        <?php if ($user->uid): ?>
                            <button class="btn btn-edit " ng-click="addStatement()" >
                                <i class="ficon-plus"></i> Add Statement
                            </button>
                        <?php endif; ?>

                        <?php if($isAdmin || $isCurator): ?>
                            <div ng-show="statements.length" class="header-divider"></div>

                            <a ng-show="statements.length" ng-click="approveStatements()" href role="button" class="btn btn-edit">
                                <i class="ficon-ok"></i> Approve Statements
                            </a>

                            <edit-button click="editStatements()"></edit-button>
                        <?php endif; ?>

                    </div>
                </div>


            </div>

            <!-- HEADING -->
            <div ng-switch on="model.statementsState">
                <div ng-switch-when="isAdding">
                    <h2><i>Add a New Statement</i></h2>
                </div>
                <div ng-switch-when="isLoading">
                    <h2>Statements ({{ statements.length }})</h2>
                </div>
                <div ng-switch-when="isEditing">
                    <h2><i>Editing Statements</i></h2>
                </div>
                <div ng-switch-when="isApproving">
                    <h2><i>Approve a Statement</i></h2>
                </div>
                <div ng-switch-when="isDisplaying">
                    <h2>Statements ({{ statements.length }})</h2>
                </div>
            </div>
        </div>

        <cm-alert state="model.statementsState" from="isLoading" to="isDisplaying">
            <i class="ficon-ok"></i> Statements Updated
        </cm-alert>

        <div ng-switch on="model.statementsState">
            <div ng-switch-when="isLoading">
                <refresh-box></refresh-box>
            </div>
            <div ng-switch-when="isEditing">
                <div ng-show="!model.edit.statements.length" class="section-segment section-segment-editing"><span class="muted">No statements.</span></div>
                <div ng-repeat="statement in model.edit.statements">
                    <a ng-click="removeStatement(statement)" href class="section-segment section-segment-editing media-body">
                        <span cm-tooltip cm-tooltip-content="Delete this statement and comments" class="btn btn-remove" style="float: left;" href=""><i class="ficon-remove"></i></span>
                        <span ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                        </span>
                    </a>

                    <div ng-repeat="comment in statement.comments">
                        <a ng-click="removeCommentFromStatement(comment, statement)" href class="section-segment section-segment-editing section-segment-sub media-body" >
                            <span cm-tooltip cm-tooltip-content="Delete this comment" class="btn btn-remove" style="float: left;" href=""><i class="ficon-remove"></i></span>

                            <span ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></span>
                        </a>
                    </div>

                </div>
            </div>
            <div ng-switch-when="isApproving">
                <div ng-repeat="statement in statements">

                    <a ng-show="!statement.field_statement_approved_time.und" ng-click="showApproveStatement(statement)"  href class="section-segment media-body">
                        <span cm-tooltip cm-tooltip-content="Approve this statement" class="btn btn-add" style="float: left;">
                            <i class="ficon-ok"></i>
                        </span>

                        <span ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                        </span>
                    </a>
                    <div ng-show="statement.field_statement_approved_time.und" class="section-segment media-body">
                        <span class="btn btn-added" style="float: left;" href=""><i class="ficon-ok"></i></span>

                        <span ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                        </span>
                    </div>

                    <!--<a ng-repeat="comment in statement.comments" href class="section-segment section-segment-sub media-body">
                        <span ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></span>
                    </a>-->
                </div>

            </div>
            <div ng-switch-when="isAdding">
                <div class="section-segment section-segment-editor statement-new">
                    <div ng-model="model.newStatement" ck-editor></div>
                </div>

            </div>
            <div ng-switch-when="isDisplaying">
                <div ng-show="!statements.length" class="section-segment">
                    <span class="muted">
                        No statements.
                    </span>

                </div>

                <div ng-repeat="statement in statements | orderBy:'-created' | limitTo:model.statementDisplayLimit">

                    <div id="{{ statement.nid }}" class="section-segment section-segment-statement" ng-click="showComments(statement)">

                        <div class="section-segment-statement-icons">
                        <span ng-show="!statement.isShowingComments">
                                            <i class="ficon-angle-right pull-right"></i>

                            <!--<i class="icon-chevron-right icon-chevron-turn-down"></i>
                            <i class="icon-chevron-right icon-white icon-chevron-turn-down"></i>-->
                        </span>

                        <span ng-show="statement.isShowingComments">
                            <i class="ficon-angle-up pull-right"></i>
                            <!--<i class="icon-chevron-up"></i>
                            <i class="icon-chevron-up icon-white "></i>-->
                        </span>
                        </div>

                        <div class="section-segment-statement-text" ng-bind-html-unsafe="statement.body.und[0].safe_value || statement.body.und[0].value || 'No statement.'">
                        </div>

                        <div class="section-segment-statement-interaction" ng-show="statement.isShowingComments || isEditingStatements">

                        <span class="label">
                            <i class="ficon-user"></i> {{ statement.name || "Anonymous" | capitalize }}
                        </span>

                        <span class="label">
                            <i class="ficon-comment"></i> {{ statement.comments.length || statement.comment_count }}
                        </span>

                        <span style="font-size: 11.844px; line-height: 14px; float: right; color: #bbb; margin-right: 30px;">
                            {{ statement.created*1000 | date:'MMM d, y - h:mm a' }}
                        </span>

                        <span class="label label-success" ng-show="statement.field_statement_approved_time">
                            <i class="ficon-ok"></i> Added to Abstract {{ statement.field_statement_approved_time.und[0].value * 1000 | date:'MMM d, y - h:mm a' }}
                        </span>


                            <a ng-show="isEditingStatements"
                               ng-click="deleteStatement(statement)"
                               class="btn btn-danger pull-left" href style="color: white; margin-right: 14px;">
                                <i class="ficon-remove icon-white"></i> Delete
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
                                        <i class="ficon-remove icon-white"></i> Delete
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
                                    <save-button click="addComment(statement, statement.newComment)"></save-button>
                                    <cancel-button click="cancelComment(statement)"></cancel-button>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
                <cm-reveal model="statements" showing-count="model.statementDisplayLimit" default-count="3"></cm-reveal>
            </div>
        </div>


        <!--<?php if (!$user->uid): ?>
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

        </div>

        <div class="section-segment alert alert-success" cm-alert="model.isloadingNewStatement">
            <i class="ficon-ok"></i> New statement saved.
        </div>

        <div ng-cloak ng-hide="statements.length" class="section-segment muted">
            No statements.
        </div>

        <div ng-cloak>





        </div>-->
    </section>


    <my-modal visible="isShowingApproveStatement">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Approve statement</h3>
        </div>
        <div class="section-segment">
            {{ approvedCommentUsers() }}
        </div>
        <a href class="section-segment media-body">
            <span cm-tooltip cm-tooltip-content="Approve this statement" class="btn btn-added" style="float: left;">
                <i class="ficon-ok"></i>
            </span>

            <span ng-bind-html-unsafe="model.approveStatement.body.und[0].safe_value || model.approveStatement.body.und[0].value || 'No statement.'">
            </span>
        </a>

        <div ng-repeat="comment in model.approveStatement.comments">
            <a ng-click="comment.approved = !comment.approved" href class="section-segment section-segment-sub media-body">

                 <span ng-show="!comment.approved" cm-tooltip cm-tooltip-content="Approve this statement" class="btn btn-add" style="float: left;">
                    <i class="ficon-ok"></i>
                </span>

                <span ng-show="comment.approved" cm-tooltip cm-tooltip-content="Approve this statement" class="btn btn-added" style="float: left;">
                    <i class="ficon-ok"></i>
                </span>


                <span ng-bind-html-unsafe="comment.comment_body.und[0].value || 'No Comment'"></span>
            </a>
        </div>


        <div class="modal-footer">
            <a ng-click="addStatementToDescription(model.approveStatement)" class="btn btn-save" href=""><i class="ficon-plus"></i> Add</a>
        </div>
    </my-modal>

</div>