(function ($) {
    $(function () {
        var FileUpload = {};
        
        FileUpload.submitUploadFile = function (type) {
            FileUpload.target = "upload.behind.php";
            var form = $('#' + type + '-form');
            $(form).ajaxSubmit({
                url: FileUpload.target,
                type: 'POST',
                dataType: 'html',
                success: function (data) {
                    $('#uploaded-files-container').prepend(data);
                    $(form).find('.upload-indicator').hide();
                    $(form).find('.submit-button').button('reset');
                },
                error: function(jqXHR) {
                    $('#errors').append('<p>' + jqXHR.responseText + '</p>');
                    $(form).find('.upload-indicator').hide();
                }
            });
        };
        
        $('.choose-file').change( function() {
            $('#errors').children().remove();
        });
        
        $('.submit-button').click( function(){
            $('#errors').children().remove();
            if ($('div.tab-pane.active').find('.choose-file').val() == '')
            {
                $('#errors').append('<p>No file chosen.</p>');
                return false;
            }
            var type = $('div.tab-pane.active').attr('id');
            $('form#' + type + '-form').find('img.upload-indicator').fadeIn();
            $(this).button('loading');
            FileUpload.submitUploadFile(type);
        });
        
        // on start
        $('form').ajaxForm();
        $('.tabs').tabs();
        $('.submit-button').button().click(function(e) {
            e.preventDefault();
        });
    });
}(jQuery));