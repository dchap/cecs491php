$(function() {
    ManualEntries.validHandler = function() {
        $.get(
            ManualEntries.target,
            { 
                station: function() {
                    return $('select[name=stations_name]').val();
                },
                date: function() {
                    return $('input[name=date_in]').val();
                },
                time: function() {
                    return $('input[name=time_in]').val();
                }
            },
            function(unique) {
                if (unique == true) {
                    ManualEntries.addRecord();
                    $('#modal-form-container').modal('hide');
                }
                else
                    $('#modal-errors').children().andSelf().fadeIn();
            },
            'json'
        );
    };

    ManualEntries.addConfirmHandler(ManualEntries.validHandler);
    ManualEntries.editClickHandler($.noop);
    ManualEntries.restoreForm($.noop);

    $('#add-new-button').live('click', function(){
        $('#errors').children().remove();
        ManualEntries.restoreForm($.noop);
        $('#records-form').resetForm();
        $('#modal-form-container').modal('show');
    })

    $('#records-form').validate({
        rules: {
            stations_name: {
                required: true,
                maxlength: 45
            },
            receivers_id: {
                required: true,
                maxlength: 45
            },
            release_value: {
                digits: true,
                maxlength: 11
            },
            hobo: {
                digits: true,
                maxlength: 11
            },
            frequency_codespace: {
                maxlength: 20
            },
            sync_tag: {
                digits: true,
                maxlength: 11
            },
            latitude: {
                required: true,
                number: true
            },
            longitude: {
                required: true,
                number: true
            },
            secondary_latitude: {
                number: true
            },
            secondary_longitude: {
                number: true
            },
            secondary_waypoint: {
                digits: true
            },
            depth: {
                number: true
            },
            recevier_height: {
                number: true
            },
            date_in: {
                required: true,
                date: true
            },
            time_in: {
                required: true
            },
            date_out: {
                required: true,
                date: true
            },
            date_downloaded: {
                date: true
            }
        }
    });
})