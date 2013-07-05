myApp.directive('feedback', function($http) {
    return {
        restrict: 'E',
        template: '<div class="feedback">\n\n    <div ng-show="isShowingFeedbackForm">\n        <form class="feedback-form">\n        <div ng-switch on="feedbackState">\n            \n                <div ng-switch-when="isSaved">\n                    Feedback Sent\n                </div>\n                <div ng-switch-when="isLoading">\n                    <refresh-box></refresh-box>\n                </div>\n                <div ng-switch-when="isDisplaying">\n                    <div ng-show="user.uid == 0">\n                        <label for="name">Name</label>\n                        <input ng-model="name" id="name" type="text" class="full-width"/>\n                        <label for="email">Email</label>\n                        <input ng-model="feedback.email" id="email" type="text" class="full-width"/>    \n                    </div>\n                    <div ng-show="user.uid > 0">\n                        <b>Email</b> {{ user.mail }}\n                    </div>\n                    \n                    <label for="feedback">Feedback</label>\n                    <textarea ng-model="feedback.message" name="feedback" id="feedback" class="full-width" style="height: 100px"></textarea>\n                    <button ng-click="sendFeedback(feedback)" class="btn btn-save">Send Feedback</button>\n                </div>\n        </div>\n        </form>\n    </div>\n    \n\n    <!-- Feedback button -->\n    <button ng-click="toggleFeedback()" class="feedback-button">\n        Feedback\n    </button>\n    \n    \n</div>',
        replace: true,
        controller: function ($scope) {
            $scope.feedback = {};

            $scope.feedbackState = "isDisplaying";

            $scope.isShowingFeedbackForm = false;
            $scope.user =  Drupal.settings.skeletome_builder.user;
            $scope.toggleFeedback = function() {
                $scope.isShowingFeedbackForm = !$scope.isShowingFeedbackForm;
            }
            $scope.sendFeedback = function(feedback) {
                $scope.feedbackState = "isLoading";
                $http.post($scope.baseUrl + '/?q=ajax/feedback', {
                    'feedback': feedback.message,
                    'uid': $scope.user.uid
                }).success(function(data) {
                    $scope.feedbackState = "isSaved";
                    setTimeout(function() {
                        $scope.$apply(function() {
                            $scope.isShowingFeedbackForm = false;
                            $scope.feedbackState = "isDisplaying";
                        });
                    }, 1000);
                });
            }
        },
        link: function(scope, elem, attrs) {
        }
    };
});