(function ($) {
    $(function () {
        var SingularEntries = {};
        SingularEntries.target = 'singular-entries.behind.php';

//////////////////////////////////// FUNCTIONS ////////////////////////////////////

        SingularEntries.addRecord = function(recordType, value) {
            $('#errors').children().remove();
            $.ajax({
                type: 'POST',
                url: SingularEntries.target,
                data: {actionType: 'add', table: recordType, value: value},
                success: function(data) {
                    $('select.' + recordType).prepend(data);
                },
                error: SingularEntries.displayErrors
            });
        };

        SingularEntries.deleteRecord = function(recordType, value) {
            $('#errors').children().remove();
            $.ajax({
                type: 'POST',
                url: SingularEntries.target,
                data: {actionType: 'delete', table: recordType, value: value},
                success: function() {
                    $('select.' + recordType).find('option:selected').remove();
                    $(':text.' + recordType).val('');
                    $(':button[name=' + recordType + '-edit]')
                        .add(':button[name=' + recordType + '-delete]')
                        .attr('disabled', true);
                },
                error: SingularEntries.displayErrors
            });
        };

        SingularEntries.editRecord = function(recordType, oldValue, newValue) {
            $('#errors').children().remove();
            $.ajax({
                type: 'POST',
                url: SingularEntries.target,
                data: { 
                    actionType: 'edit', 
                    table: recordType, 
                    oldValue: oldValue, 
                    newValue: newValue 
                },
                success: function(data) {
                    $('select.' + recordType).find('option:selected').val(data).text(data);
                    $(':text.' + recordType).val('');
                },
                error: SingularEntries.displayErrors
            });
        };

        SingularEntries.displayErrors = function(jqXHR) {
            $('#errors').append('<p>' + jqXHR.responseText + '</p>');
        };
        
        SingularEntries.validate = function(value) {
            $('#errors').children().remove();
            if ($.trim(value) == '')
            {
                $('#errors').append('<p>Value cannot be empty</p>');
                return false;
            }
            var valueExists = false;
            $('option').each( function() {
                if ($(this).val() == value)
                {
                    $('#errors').append('<p>Value already exists</p>');
                    valueExists = true;
                }
            });
            if (valueExists)
                return false;
            return true;
        };

//////////////////////////////////// EVENTS ////////////////////////////////////

        // move copy selected value to corresponding textbox
        $('select').change( function() {
            $('#errors').children().remove();
            var value = $(this).val();
            var type = $(this).attr('name');
            $(':text.' + type).val(value);
            $(':button[name=' + type + '-edit]').attr('disabled', false);
            $(':button[name=' + type + '-delete]').attr('disabled', false);
        });

        // add new entry
        $(':button[name$=add]').click( function() {
            var type = $(this).attr('name').replace('-add', '');
            var value = $(':text.' + type).val();
            if (!SingularEntries.validate(value))
                return false;
            $('#errors').children().remove();
            SingularEntries.addRecord(type, value);
        });

        // delete
        $(':button[name$=delete]').click( function() {
            $('#errors').children().remove();
            var type = $(this).attr('name').replace('-delete', '');
            var value = $('select.' + type).val();
            $('#confirm-delete-button').data({
                type: type,
                value: value
            });
            $.get(
                SingularEntries.target,
                {'side-effects': value, type: type},
                function(data) {
                    $('#modal-body-delete').children().remove()
                    .end().append(data)
                }
            );
            $('#modal-container-delete div.modal-header').text('Confirm Delete: ' + value);
            $('#modal-container-delete').modal('show');
        });

        $('#confirm-delete-button').click( function() {
            SingularEntries.deleteRecord(
                $(this).data('type'),
                $(this).data('value')
            );
            $('#modal-container-delete').modal('hide');
        });

        // edit
        $(':button[name$=edit]').click( function() {
            var type = $(this).attr('name').replace('-edit', '');
            var oldValue = $('select.' + type).val();
            var newValue = $(':text.' + type).val();
            if (!SingularEntries.validate(newValue))
                return false;
            $('#errors').children().remove();
            
            $('#confirm-edit-button').data({
                type: type,
                oldValue: oldValue,
                newValue: newValue
            });
            $.get(
                SingularEntries.target,
                {'side-effects': oldValue, type: type},
                function(data) {
                    $('#modal-body-edit').children().remove()
                    .end().append(data)
                }
            );
            $('#modal-container-edit div.modal-header')
                .text('Confirm Edit: ' + oldValue + ' to ' + newValue);
            $('#modal-container-edit').modal('show');
        });

        $('#confirm-edit-button').click( function() {
            SingularEntries.editRecord(
                $(this).data('type'),
                $(this).data('oldValue'),
                $(this).data('newValue')
            );
            $('#modal-container-edit').modal('hide');
        });
        
        // cancel
        $('#cancel-edit-button').click( function() {
            $('#modal-container-edit').modal('hide')
        });
        
        $('#cancel-delete-button').click( function() {
            $('#modal-container-delete').modal('hide')
        })

//////////////////////////////////// ON START ////////////////////////////////////
        
        $('#modal-container-delete').modal({
            backdrop: true,
            keyboard: true
        });
        $('#modal-container-edit').modal({
            backdrop: true,
            keyboard: true
        });
    });
}(jQuery));