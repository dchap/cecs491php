$(function() {

///////////////////////////// VALIDATION RULES ///////////////////////////////

    var codespaceReq = "Required with sensor ID";
    var idReq = "Required with codespace";
    
    $('#records-form').validate({
        rules: {
            codespace: {
                required: true,
                maxlength: 20
            },
            transmitter_id: {
                required: true,
                digits: true,
                maxlength: 11
            },
            ascension: {
                required: true,
                maxlength: 45
            },
            genus: {
                required: true,
                maxlength: 45
            },
            species: {
                required: true,
                maxlength: 45
            },
            sensor_id1: {
                digits: true
            },
            sensor_id2: {
                digits: true
            },
            sensor_id3: {
                digits: true
            },
            date_deployed: {
                date: true,
                required: true
            },
            time_deployed: {
                required: true
            },
            sex: {
                required: true
            },
            total_length: {
                required: true,
                digits: true,
                maxlength: 11
            },
            fork_length: {
                digits: true,
                maxlength: 11
            },
            standard_length: {
                required: true,
                digits: true,
                maxlength: 11
            },
            girth: {
                digits: true,
                maxlength: 11
            },
            weight: {
                digits: true,
                maxlength: 11
            },
            dart_tag: {
                required: true,
                maxlength: 45
            },
            dart_color: {
                required: true,
                maxlength: 45
            },
            landed_latitude: {
                required: true,
                number: true,
                maxlength: 11
            },
            landed_longitude: {
                required: true,
                number: true,
                maxlength: 11
            },
            released_longitude: {
                required: true,
                number: true,
                maxlength: 11
            },
            released_latitude: {
                required: true,
                number: true,
                maxlength: 11
            },
            time_out_of_water: {
                number: true,
                maxlength: 11
            },
            time_in_tricane: {
                number: true,
                maxlength: 11
            },
            time_in_surgery: {
                number: true,
                maxlength: 11
            },
            recovery_time: {
                number: true,
                maxlength: 11
            },
            landing_depth: {
                number: true,
                maxlength: 11
            },
            release_depth: {
                number: true,
                maxlength: 11
            },
            landing_temperature: {
                number: true,
                maxlength: 11
            },
            release_temperature: {
                number: true,
                maxlength: 11
            },
            fish_condition: {
                maxlength: 45
            },
            release_method: {
                required: true,
                maxlength: 45
            },
            comment: {
                maxlength: 100
            }
        }
    });
    
    
    $('[name^=sensor_codespace1]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_id1]').rules("add", {
                required: true,
                messages: {
                    required: idReq
                }
            });
        else
            $('[name=sensor_id1]').rules("remove", "required");
    });
    $('[name=sensor_id1]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_codespace1]').rules("add", {
                required: true,
                messages: {
                    required: codespaceReq
                }
            });
        else
            $('[name=sensor_codespace1]').rules("remove", "required");
    });
    $('[name^=sensor_codespace2]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_id2]').rules("add", {
                required: true,
                messages: {
                    required: idReq
                }
            });
        else
            $('[name=sensor_id2]').rules("remove", "required");
    });
    $('[name=sensor_id2]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_codespace2]').rules("add", {
                required: true,
                messages: {
                    required: codespaceReq
                }
            });
        else
            $('[name=sensor_codespace2]').rules("remove", "required");
    });
    $('[name=sensor_codespace3]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_id3]').rules("add", {
                required: true,
                messages: {
                    required: idReq
                }
            });
        else
            $('[name=sensor_id3]').rules("remove", "required");
    });
    $('[name=sensor_id3]').blur(function() {
        if ($.trim($(this).val()) != '')
            $('[name=sensor_codespace3]').rules("add", {
                required: true,
                messages: {
                    required: codespaceReq
                }
            });
        else
            $('[name=sensor_codespace3]').rules("remove", "required");
    });    
    
/////////////////////////// VALIDATION INJECTION //////////////////////////////
    var Fish = new Object();

    Fish.validHandler = function() {
        $.get(
            ManualEntries.target,
            { 
                transmitter: function() {
                    return $('input[name=transmitter_id]').val();
                },
                codespace: function() {
                    return $('input[name=codespace]').val();
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

    Fish.editValidationHandler = function() {
        $('[name^=sensor_id]').add($('[name^=sensor_codespace]')).each(function() {
            $(this).rules("remove", "required");
        });
        
        $('#ascension').rules("remove", "remote");
    };
   
    Fish.addValidationHandler = function() {
        $('#ascension').rules("add", {
            remote: {
                url: ManualEntries.target,
                type: 'GET',
                data: {
                    ascension: function() {
                        return $('input[name=ascension]').val();
                    }
                }
            },
            messages: {
                remote: "Ascension # already exists"
            }
        });
    };

    $('#add-new-button').live('click', function(){
        $('[name^=sensor_id]').add($('[name^=sensor_codespace]')).each(function() {
            $(this).rules("remove", "required");
        });
        $('#errors').children().remove();
        ManualEntries.restoreForm(Fish.addValidationHandler);
        $('#records-form').resetForm();
        $('#modal-form-container').modal('show');
    });
    
    ManualEntries.addConfirmHandler(Fish.validHandler);
    ManualEntries.editClickHandler(Fish.editValidationHandler);
    ManualEntries.restoreForm(Fish.addValidationHandler);
    
///////////////////////////////// EVENTS /////////////////////////////////////


    $('.header').live('click', function() {
        $(':hidden[name=action-type]').val('query');
        var sortby = $(this).attr('data-sort');
        $(':hidden[name=sort-by]').val(sortby);
        if ($(this).hasClass('blue header'))
        {
            var order = $(':hidden[name=sort-order]').val() == 'asc' ? 'desc' : 'asc';
            $(':hidden[name=sort-order]').val(order);
        }
        else
            $(':hidden[name=sort-order]').val('asc');
        
    });
    


//    Fish.Download = function() {
//        $(':hidden[name=action-type]').val('download');
//        var query = $('#query-form').serialize();
//        window.open(ManualEntries.target + '?' + query);
//    }

    $('.btn').button().click(function(event) {
        event.preventDefault();
    });
    
///////////////////////////////// ON START /////////////////////////////////////    
})