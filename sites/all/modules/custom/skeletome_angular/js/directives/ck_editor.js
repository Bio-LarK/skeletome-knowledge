myApp.directive('ckEditor', function() {
    return {
        require: '?ngModel',
        link: function(scope, elm, attr, ngModel) {

            CKEDITOR.timestamp = (new Date()).toString() ;

            var config = {
//                height: '800px',

                on :
                {
                    // Maximize the editor on start-up.
                    'instanceReady' : function( evt )
                    {
                        console.log("instance is ready!");
                        setTimeout(function() {
                                console.log("looking for modal inner");
                                if(jQuery(elm).closest('.modal-body-inner').length) {
                                    console.log("inside a modal inner");
                                    evt.editor.resize("100%", jQuery('.modal-body-inner').eq(0).height() - 50);
                                }

                                console.log(evt.editor);
                            }
                            , 200);
                    }
                },
                removePlugins: 'elementspath',
                forcePasteAsPlainText : true,
                extraPlugins : 'iframedialog,pubmed',
                toolbar :
                    [
                        { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
                        { name: 'tools', items : [ 'Pubmed' ] }
                    ]

            };
            if(attr.height) {
                config.height = attr.height;
            }

            var ck = CKEDITOR.replace(elm[0], config);

            if (!ngModel) return;

            ck.on('pasteState', function() {
                // While something is typing
                scope.$apply(function() {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ck.on('save', function() {
                // Triggers when a new reference is inserted.
                scope.$apply(function() {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ngModel.$render = function(value) {
                ck.setData(ngModel.$viewValue);
            };
        }
    };
});