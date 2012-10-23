// todo: change edit remote rules like membership

var ManualEntries = {};

//ManualEntries.getRecords = function() {
//    $('#noRecords').hide();
//    $.ajax({
//        type: 'GET',
//        url: ManualEntries.target,
//        data: 'loadTable=true',
//        success: function(response) {
//            if (response == 'none')
//                $('#noRecords').show();
//            else
//                $('#recordsTable').append(response);
//        },
//        error: ManualEntries.displayErrors
//
//    });
//};

ManualEntries.addRecord = function() {
    $('div#errors').children().remove();
    $('#action-type').val('add');
    $('#records-form').ajaxSubmit({
        url: ManualEntries.target,
        type: 'POST',
        dataType: 'html',
        success: function(data) {
            $('tbody').prepend(data);
            $('tbody :first').show('explode');
            $('#records-form').resetForm();
        },
        error: ManualEntries.displayErrors
    });
};

ManualEntries.deleteRecord = function(recordId) {
    $.ajax({
        type: 'POST',
        url: ManualEntries.target,
        data: {'action-type': 'delete', id: recordId},
        success: function() {
            $('tr[data-id=' + recordId + ']')
                .hide('explode', 
                function() {
                    $(this).remove();
                })
        },
        error: ManualEntries.displayErrors
    });
}; 

ManualEntries.editRecord = function() {
    $('div#errors').children().remove();
    $('#action-type').val('edit');
    $('#records-form').ajaxSubmit({
        url: ManualEntries.target,
        type: 'POST',
        dataType: 'html',
        success: function(data) {
            var recordId = $(data).attr('data-id');
            $('tr[data-id=' + recordId + ']').remove();
            $('tbody').prepend(data);
            $('tbody :first').show('explode');
            $('#records-form').resetForm();
        },
        error: ManualEntries.displayErrors
    });
}

ManualEntries.displayErrors = function(jqXHR) {
    $('.upload-indicator').hide();
    $('#errors').append('<p>' + jqXHR.responseText + '</p>');
};

ManualEntries.query = function() {
    $('#query-form :hidden[name=action-type]').val('query');
    $('#no-records').hide();
    $('#results').html('');
    $('.upload-indicator').show();
    $('#query-form').ajaxSubmit({
        url: ManualEntries.target,
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('.upload-indicator').hide();
            $('#results').append(response);
            if ($('tbody').children().length > 0)
                $('#download-button').attr('disabled', false);
            else
                $('#download-button').attr('disabled', true);
        },
        error: ManualEntries.displayErrors
    });
};


///////////////////////////// VALIDATION INJECTION /////////////////////////////

// UniqueValidator must be implemented by scripts using this one
// and function must be called
ManualEntries.addConfirmHandler = function(ValidHandler) {
    $('#add-confirm-button').click( function() {
        if ($('#records-form').valid())
            ValidHandler();
    });
};

ManualEntries.editClickHandler = function(EditValidationHandler) {
    // maps a table row's cells' class attr to every form input's name attr
    // and copies their values
    $('.edit').live('click', function() {
        $(this).parent().siblings('[class]').each( function() {
            var match = $(this).attr('class');
            var value = $(this).text();
            $('.formInputs').each( function() {
               if ($(this).attr('name') == match)
                   $(this).val(value);
            });
        });
        $('form').find('.error').removeClass('error');
        $('label[generated]').remove();
        var id = $(this).closest('tr[data-id]').attr('data-id');
        $('#record-id').val(id);
        $('#form-title').html('Edit record');
        $('#add-confirm-button').hide();
        $('#edit-confirm-button').show();
        EditValidationHandler();
        $('#modal-form-container').modal('show');
    });
};

ManualEntries.restoreForm = function(AddValidationHandler) {
    $('#edit-confirm-button').hide();
    $('#add-confirm-button').show();
    $('#form-title').html('Add a new record');
    $('form').find('.error').not('#modal-errors > label').removeClass('error');
    $('label[generated]').remove();
    $('#modal-errors').hide();
    $('#record-id').removeAttr('value');
    AddValidationHandler();
};

//////////////////////////////////// EVENTS ////////////////////////////////////

$(function () {

    $('.delete').live('click', function() {
        var id = $(this).closest('tr[data-id]').attr('data-id');
        $('div#errors').children().remove();
        $('#delete-confirm-button').data('id', id);
        $('#modal-delete-container').modal('show');
    });

    $('#delete-confirm-button').click( function() {
        ManualEntries.deleteRecord($(this).data('id'));
        $('#modal-delete-container').modal('hide');
    });

    $('#edit-confirm-button').click( function() {
        if ($('#records-form').valid()) {
            ManualEntries.editRecord();
            $('#modal-form-container').modal('hide');
        }
    });

    $('#cancel-button').click( function() {
        $('#modal-form-container').modal('hide');
    });

    $('#delete-cancel-button').click( function() {
        $('#modal-delete-container').modal('hide');
    });
    
    $('.pagination-page').live('click', function(event) {
        event.preventDefault();
        $(':hidden[name=action-type]').val('query');
        var queryString = $('#query-form').serialize() + '&' + $(this).attr('href') + '&' + $('.pagination').attr('data-count');
        $('#results, #errors').html('');
        $.ajax({
            url: ManualEntries.target,
            type: 'GET',
            data: queryString,
            success: function(data) {
                $('#results').append(data);
            },
            error: function(jqXHR) {
                $('#errors').append('<p>' + jqXHR.responseText + '</p>');
            }
        });
    });
    
    $('.projects').change(ManualEntries.query);
    
    $('#download-button').click(function() {
        $('#query-form :hidden[name=action-type]').val('download');
        var query = $('#query-form').serialize();
        window.open(ManualEntries.target + '?' + query);
    })
        

//////////////////////////////////// ON START //////////////////////////////////

    $('#records-form').ajaxForm();
    $('#query-form').ajaxForm();

    ManualEntries.target = $('#records-form').attr('action');
    ManualEntries.query();

    $('#modal-form-container').modal({
        backdrop: true,
        keyboard: true
    });

    $('#modal-delete-container').modal({
        backdrop: true,
        keyboard: true
    });
    
    $('tr').live('dblclick', function() {
    	window.scrollBy(-10000,0);
    })
});