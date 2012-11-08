jQuery(function($) {
    var Query = {};

    Query.target = $('form').attr('action');
    $('#main-form').ajaxForm();

    $(':radio[name=inner-query]').change( function() {
        var ele = $(':radio[name=inner-query]:checked');
        if ($(ele).val() == 'sensor')
            $('#st-switch').text('Sensor ID');
        else
            $('#st-switch').text('Transmitter ID');
    });

    $(':radio[name=outer-query]').change( function() {
        var queryType = $(':radio[name=outer-query]:checked').val();
        if (queryType == 'fish') {
            $('.fish-options').show();
            $('.station-options').hide();
            $(':radio[value=transmitter]').click();
        }
        else {
            $('.fish-options').hide();
            $('.station-options').show();
            $(':radio[value=recognized-fish]').click();
        }
    });

    Query.MakeQuery = function() {
        $('#query-button').button('loading');
        $('#results, #errors').html('');
        $(':hidden[name=action-type]').val('query');
        var img = $('#main-form').find('img.upload-indicator').get(0);
        $(img).fadeIn();
        $('#main-form').ajaxSubmit({
            url: Query.target,
            type: 'GET',
            success: function(data) {
                $('#results').append(data);
                $('#query-button').button('reset');
                $(img).hide();
            },
            error: function(jqXHR) {
                $('#errors').append('<p>' + jqXHR.responseText + '</p>');
                $('#query-button').button('reset');
                $(img).hide();
            }
        });
    };

    $('#query-button').click( function() {
        if ($('#main-form').valid())
            Query.MakeQuery();
    });
    
//==============================================================================
//    
//    waitUntilExists($('#query-chbutton'), function(){
//    if($('myDiv'))
//    {
//        $('#query-chbutton').ready( function() {
//        if ($('#main-form').valid())
//            Query.MakeRealtimeQuery();});
//    }
//    
////    // this one is for realtime query page
//        Query.MakeRealtimeQuery = function() {
//        $('#query-chbutton').button('loading');
//        $('#results, #errors').html('');
//        $(':hidden[name=action-type]').val('query');
//        var img = $('#main-form').find('img.upload-indicator').get(0);
//        $(img).fadeIn();
//        $('#main-form').ajaxSubmit({
//            url: Query.target,
//            type: 'GET',
//            success: function(data) {
//                $('#results').append(data);
//                $('#query-chbutton').button('reset');
//                $(img).hide();
//            },
//            error: function(jqXHR) {
//                $('#errors').append('<p>' + jqXHR.responseText + '</p>');
//                $('#query-chbutton').button('reset');
//                $(img).hide();
//            }
//        });
//    };
//    
//==============================================================================

    $('.header').live('click', function() {
        var sortby = $(this).attr('data-sort');
        $(':hidden[name=sort-by]').val(sortby);
        if ($(this).hasClass('blue header'))
        {
            var order = $(':hidden[name=sort-order]').val() == 'asc' ? 'desc' : 'asc';
            $(':hidden[name=sort-order]').val(order);
        }
        else
            $(':hidden[name=sort-order]').val('asc');
        Query.MakeQuery();
    });

    $('.pagination-page').live('click', function(event) {
        event.preventDefault();
        $(':hidden[name=action-type]').val('query');
        var queryString = $('#main-form').serialize() + '&' + $(this).attr('href') + '&' + $('.pagination').attr('data-count');
        $('#results, #errors').html('');
        $('#query-button').button('loading');        
        var img = $('#main-form').find('img.upload-indicator').get(0);
        $(img).fadeIn();
        $.ajax({
            url: Query.target,
            type: 'GET',
            data: queryString,
            success: function(data) {
                $('#results').append(data);
                $('#query-button').button('reset');
                $(img).hide();
            },
            error: function(jqXHR) {
                $('#errors').append('<p>' + jqXHR.responseText + '</p>');
                $('#query-button').button('reset');
                $(img).hide();
            }
        });
    });

    Query.validate = function() {
        $('#results, #errors').children().remove();
        $(':hidden[name=action-type]').val('validate');
        var query = $('#main-form').serialize();
        $.ajax({
            url: Query.target,
            type: 'GET',
            data: query,
            success: Query.download,
            error: function(jqXHR) {
                $('#errors').append('<p>' + jqXHR.responseText + '</p>');
            }
        });
    }

    Query.download = function() {
        $(':hidden[name=action-type]').val('download');
        var query = $('#main-form').serialize();
        window.open(Query.target + '?' + query);
    }

    $('#download-button').click( Query.validate );
    $('.btn').button().click(function(event) {
        event.preventDefault();
    });
    
    $('#main-form').validate({
        rules: {
            project: "required",
            transmitter_start: "digits",
            transmitter_end: "digits",
            frequency_codespace: "required",
            transmitterid: {
                required: true,
                digits: true
            }
        },
        messages: {
            project: {
                required: "A project must be selected."
            }
        }
    });
});