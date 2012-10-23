(function () {
    var RUN_ON_READY;
    RUN_ON_READY = function ($) {
        var my = {};
        my.submitUploadFile = function () {
            $('#action').val('uploadAction');
            $('#metadataUploadForm').ajaxSubmit({
//                        url: 'metadata.behind.php',
                        url: 'metadata.behind.php',
                        type: 'POST',
                        success: my.onUploadResponse
                    });
        };
        my.onValidate = function () {
            $('#action').val('validateAction');
            $('#metadataUploadForm').ajaxSubmit({
//                        url: 'metadata.behind.php',
                        url: 'metadata.behind.php',
                        type: 'POST',
                        success: my.onValidationResponse
                    });
        };
        my.onValidationResponse = function (data, textStatus, jqXH) {
//            if (data === null || data === "") {
//            }else {
//                $('.errorMsgDivs').toggle('hidden');
//            }
            $('.uploadIndicators').toggle('hidden');
            my.submitUploadFile();
        };
        my.onUploadResponse = function (data, textStatus, jqXH) {
            $('#uploadedFiles').append(data);
            $('#uploadedFiles').append('<br/>');
        };
        $('#submitButton').click(my.onValidate);
        $('.uploadIndicators').toggle('hidden');
        $('.errorMsgDivs').toggle('hidden');
        $('#metadataUploadForm').ajaxForm();
    };
    jQuery.noConflict();
    jQuery(RUN_ON_READY);
}());