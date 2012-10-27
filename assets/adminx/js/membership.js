(function ($) {
    $(function () {
        var Membership = {};


//////////////////////////////////// FUNCTIONS ////////////////////////////////////

        Membership.getRecords = function() {
            $('#noRecords').hide();
            $.ajax({
                type: 'GET',
                url: Membership.target,
                data: 'loadTable=true',
                success: function(response) {
                    if (response == 'none')
                        $('#noRecords').show();
                    else
                        $('#recordsTable').append(response);
                },
                error: Membership.displayErrors
                
            });
        };
        
        Membership.addRecord = function() {
            $('div#errors').children().remove();
            $('#actionType').val('add');
            $('#recordsForm').ajaxSubmit({
                url: Membership.target,
                type: 'POST',
                dataType: 'html',
                success: function(data) {
                    $('tbody').prepend(data);
                    $('tbody :first').show('explode');
                    $('#recordsForm').resetForm();
                },
                error: Membership.displayErrors
            });
        };
        
        Membership.deleteRecord = function(recordId) {
            $.ajax({
                type: 'POST',
                url: Membership.target,
                data: {actionType: 'delete', id: recordId},
                success: function() {
                    $('tr[data-id=' + recordId + ']')
                        .hide('explode', 
                        function() {
                            $(this).remove();
                        })
                },
                error: Membership.displayErrors
            });
        }; 
        
        Membership.editRecord = function() {
            $('div#errors').children().remove();
            $('#actionType').val('edit');
            $('#recordsForm').ajaxSubmit({
                url: Membership.target,
                type: 'POST',
                dataType: 'html',
                success: function(data) {
                    var recordId = $(data).attr('data-id');
                    $('tr[data-id=' + recordId + ']').remove();
                    $('tbody').prepend(data);
                    $('tbody :first').show('explode');
                    $('#recordsForm').resetForm();
                    Membership.restore();
                },
                error: Membership.displayErrors
            });
        }
        
        Membership.displayErrors = function(jqXHR) {
            $('#errors').append('<p>' + jqXHR.responseText + '</p>');
        };
        
        Membership.restore = function() {
            $('#edit-button').hide();
            $('#add-button').fadeIn('fast');
            $('#caption').html('Add a new member');
            $('form').find('.error').removeClass('error');
            $('label[generated]').remove();
            $('#username').rules("add", {
                remote: {
                    url: Membership.target,
                    type: 'GET',
                    data: {
                        username: function() {
                            return $(':input[name=username]').val();
                        }
                    }
                },
                messages: {
                    remote: "Username exists"
                }
            });
            $('#password').rules("add", {
                required: true,
                messages: {
                    required: "Password required"
                }
            });
        };

//////////////////////////////////// EVENTS ////////////////////////////////////
        
        $('.delete').live('click', function() {
           $('#modal-delete-confirm').modal('show');
           var id = $(this).closest('tr[data-id]').attr('data-id');
           var username = $(this).parent().siblings('.username').text();
           $('#modal-user').html(username);
           $('#modal-delete').data('id', id);
           $('div#errors').children().remove();
        });
        
        $('#modal-delete').click( function() {
            Membership.deleteRecord($(this).data('id'));
            $('#modal-delete-confirm').modal('hide');
            Membership.restore();
            $('#recordsForm').resetForm();
        });
        
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
            $('#recordId').val(id);
            $('#add-button').hide();
            $('#edit-button').fadeIn('fast');
            $('#caption').html('Edit member');
            $('#username').rules('remove', 'remote');
            $('#password').rules('remove', 'required');
        });
        
        $('#add-button').click( function() {
            if ($('#recordsForm').valid())
                Membership.addRecord();
        });
        $('#edit-button').click( function() {
            if ($('#recordsForm').valid())
                Membership.editRecord()
        });
        
        $('#cancel-button').click( function() {
            $('#recordsForm').resetForm();
            Membership.restore();
        });
        
        $('#modal-cancel').click( function() {
            $('#modal-delete-confirm').modal('hide');
        });
        
//////////////////////////////////// ON START ////////////////////////////////////
        
        Membership.target = $('#recordsForm').attr('action');
        $('#recordsForm').ajaxForm();
        Membership.getRecords();
        $('#modal-delete-confirm').modal({
            backdrop: true,
            keyboard: true
        });

/////////////////////////////////// VALIDATION ///////////////////////////////////
        
        $('#recordsForm').validate({
            rules: {
                username: {
                    required: true
                },
                confirm_password: {
                    equalTo: "#password"
                },
                fname: "required",
                lname: "required"
            },
            messages: {
                username: {
                    required: "Username required"
                },
                confirm_password: {
                    equalTo: "Passwords do not match"
                },
                fname: {
                    required: "First name required"
                },
                lname: {
                    required: "Last name required"
                }
            }
        });
        Membership.restore();

    });
}(jQuery));