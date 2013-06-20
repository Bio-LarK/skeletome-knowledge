function StatementCtrl($scope, $http) {

    $scope.init = function() {
        $scope.defaultStatementDisplayLimit = 6;
        $scope.setupStatements();
    }



    $scope.setupStatements = function() {
        // Set the initial state
        $scope.model.statementsState = "isDisplaying";
        // Setup the default length
        $scope.statementDisplayLimit = $scope.defaultStatementDisplayLimit;
        $scope.isHidingStatements =  $scope.statements.length > $scope.defaultStatementDisplayLimit;

    }

    /**
     * Enter editing mode for the statements
     */
    $scope.editStatements = function() {
        $scope.model.statementsState = "isEditing";
        $scope.model.edit.statements = angular.copy($scope.statements);

        angular.forEach($scope.model.edit.statements, function(statement, index) {
            if(angular.isUndefined(statement.comments)) {
                statement.isLoadingComments = true;
                $http.get('?q=ajax/statement/' + statement.nid + '/comments').success(function(data){
                    statement.isLoadingComments = false;
                    statement.comments = data;
                });
            }
        });

    }
    /**
     * Enter approving mode for the statements
     */
    $scope.approveStatements = function() {
        $scope.model.statementsState = "isApproving";
        $scope.model.edit.statements = [];
        angular.forEach($scope.statements, function(statement, index) {
            if(!angular.isDefined(statement.field_statement_approved_time.und)) {
                console.log("time is undefined");
                $scope.model.edit.statements.push(statement);
                if(angular.isUndefined(statement.comments)) {
                    statement.isLoadingComments = true;
                    $http.get('?q=ajax/statement/' + statement.nid + '/comments').success(function(data){
                        statement.isLoadingComments = false;
                        statement.comments = data;
                    });
                }
            }

        });
    }
    /**
     * Return to displaying mode
     */
    $scope.cancelStatements = function() {
        $scope.model.statementsState = "isDisplaying";
    }
    /**
     * Enter add statement mode
     */
    $scope.addStatement = function() {
        $scope.model.statementsState = "isAdding";
    }

    /**
     * Remove a statement
     * @param statement
     */
    $scope.removeStatement = function(statement) {
        var index = $scope.model.edit.statements.indexOf(statement);
        $scope.model.edit.statements.splice(index, 1);
    }
    /**
     * Remove a comment from a statement
     * @param comment
     * @param statement
     */
    $scope.removeCommentFromStatement = function(comment, statement) {
        var index = statement.comments.indexOf(comment);
        statement.comments.splice(index, 1);
    }

    /**
     * Save Statement is in parent scope
     */


    /**
     * Show more statements
     */
    $scope.showStatements = function() {
        $scope.isHidingStatements = false;
        $scope.statementDisplayLimit = $scope.statements.length;
    }

    /**
     * Show less statements
     */
    $scope.hideStatements = function() {
        $scope.isHidingStatements = true;
        $scope.statementDisplayLimit = $scope.defaultStatementDisplayLimit;
    }

    $scope.showApproveStatement = function(statement) {
        $scope.model.statementsState = "isLoading";
        $http.get('?q=ajax/statement/' + statement.nid + '/comments').success(function(data){
            $scope.model.statementsState = "isApproving";
            statement.comments = data;
            $scope.model.approveStatement = statement;
            if(data.length == 0) {
                $scope.addStatementToDescription($scope.model.approveStatement);
            } else {
                $scope.isShowingApproveStatement = true;
            }
        });
    }

    $scope.approvedCommentUsers = function() {
        if(angular.isDefined($scope.model.approveStatement)) {
            var usernames = [$scope.model.approveStatement.name];

            angular.forEach($scope.model.approveStatement.comments, function(comment, index) {
                if(comment.approved) {
                    if(usernames.indexOf(comment.name) == -1) {
                        usernames.push(comment.name);
                    }
                }
            });
            return usernames.join(" ");
        }
        return "";
    }

    /**
     * Adds a statement to the description
     * @param statement
     */
    $scope.addStatementToDescription = function(statement) {
        $scope.isShowingApproveStatement = false;
        $scope.model.statementsState = "isDisplaying";

        $scope.model.editedDescription = $scope.description.value;
        $scope.model.isEditingDescription = true;

        $scope.model.statementPackage = {
            nid: statement.nid,
            users:  [],
            text:   "",
            statement: statement
        };

        $scope.model.statementPackage.users.push(statement.uid);
        $scope.model.statementPackage.text += statement.body.und[0].value;
        angular.forEach(statement.comments, function(comment, index) {
           if(comment.approved) {
               if($scope.model.statementPackage.users.indexOf(comment.uid) == -1) {
                   $scope.model.statementPackage.users.push(comment.uid);
               }
               $scope.model.statementPackage.text += comment.comment_body.und[0].value + "<br/>";
           }
        });
    }



    /**
     * Hide the area for statement creation
     * @param newStatement      The statement object to clear
     */
    $scope.cancelStatement = function(newStatement) {
        $scope.model.isAddingStatement = false;
        newStatement = "";
    }

    /**
     * Show the comments for a statement
     * @param statement
     */
    $scope.showComments = function(statement) {
        statement.isShowingComments = !statement.isShowingComments;

        if(angular.isUndefined(statement.comments)) {
            statement.isLoadingComments = true;
            $http.get('?q=ajax/statement/' + statement.nid + '/comments').success(function(data){
                statement.isLoadingComments = false;
                statement.comments = data;
            });
        }
    }



    $scope.deleteCommentFromStatement = function(comment, statement) {
        statement.comments.splice(statement.comments.indexOf(comment), 1);

        $http.get('?q=ajax/statement/' + statement.nid + '/comment/' + comment.cid + '/remove', {
        }).success(function(data) {
        });
    }
    $scope.showEditStatements = function() {
        $scope.isEditingStatements = true;
        angular.forEach($scope.statements, function(statement, index) {
            if(statement.comments && statement.comments.length) {
                statement.showComments = true;
            }
        });
    }
    $scope.hideEditStatements = function() {
        $scope.isEditingStatements = false;
        angular.forEach($scope.statements, function(statement, index) {
            statement.showComments = false;
        });
    }
    $scope.deleteStatement = function(statement) {
        $scope.statements.splice($scope.statements.indexOf(statement), 1);
        $http.get('?q=ajax/statement/' + statement.nid + '/remove', {
        }).success(function(data) {
            });
    }


    /**
     * Adds a comment to a statement
     * @param statement
     * @param comment
     */
    $scope.addComment = function(statement, comment) {

        statement.isLoadingComments = true;

        // Clear the text box
        var commentText = angular.copy(comment);
        statement.newComment = "";

        $http.post('?q=ajax/statement/' + statement.nid + '/comment/add', {
            comment_text: commentText
        }).success(function(data) {
                statement.isLoadingComments = false;
                statement.comments.push(data);
            });
    }
    $scope.cancelComment = function(statement) {
        statement.newComment = "";
        statement.isShowingComments = false;
    }

}