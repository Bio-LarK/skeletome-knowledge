myApp.directive('dropZoneUpload', function() {
    return {
        link: function (scope, iElement, iAttrs, ngModel) {

            iAttrs.$observe('dropZoneUpload', function(value) {
                if(value && value != "") {

                    console.log("Dropdown url", iAttrs.dropZoneUpload);

                    iElement.dropzone(
                        {
                            url: iAttrs.dropZoneUpload,
                            parallelUploads: 2,
                            enqueueForUpload: true,
                            dictDefaultMessage: iAttrs.dropZoneMessage
                        }

                    );

                    var myDropzone = Dropzone.forElement(iElement[0]);
                    myDropzone.on("addedfile", function(file) {
                        console.log("file added");
                    });
                    myDropzone.on('uploadprogress', function(file, progress) {
                        console.log("progress " + progress);
                    });

                    myDropzone.on('success', function(file, response) {
                        console.log("response");

                        setTimeout(function() {
                            myDropzone.removeFile(file);
                        }, 1000);

                        if(iAttrs.ngModel) {
                            var jsonResponse = jQuery.parseJSON( response );
                            jsonResponse.added = true;
                            var model = scope.$eval(iAttrs.ngModel);
                            console.log("upload module is ", model);
                            scope.$apply(function() {
                                model.unshift(jsonResponse);
                                console.log("upload module is ", model);
                            });

                        }
                    });

                }
            });
        }
    }
})