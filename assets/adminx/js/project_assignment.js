(function () {
    var RUN_ON_READY;
    RUN_ON_READY = function ($) {
        
        var ProjectAssignment = {};
        ProjectAssignment.target = "project-assignment.behind.php";

         ProjectAssignment.add = function(element, project, assignmentType) {
            $('#errors').children().remove();
            var value = $(element).val();
            $.ajax({
                type: 'POST',
                url: ProjectAssignment.target,
                data: {
                    actionType: 'add',
                    project: project,
                    dataType: assignmentType,
                    dataValue: value
                },
                success: function () {
                    $(element).clone().prependTo($('select[name=assigned-' + assignmentType + ']'));
                },
                error: ProjectAssignment.displayErrors
            });
         };

         ProjectAssignment.remove = function(element, project, assignmentType) {
            $('#errors').children().remove();
            var value = $(element).val();
            $.ajax({
                type: 'POST',
                url: ProjectAssignment.target,
                data: {
                    actionType: 'delete',
                    project: project,
                    dataType: assignmentType,
                    dataValue: value
                },
                success: function () {
                    $(element).remove();
                },
                error: ProjectAssignment.displayErrors
            });
         }

        ProjectAssignment.displayErrors = function(jqXHR) {
            $('#errors').append('<p>' + jqXHR.responseText + '</p>');
        };

        $('select[name^=projects]').change( function() {
            var project = $(this).val();
            $('select[name^=projects]').each( function () {
                // select option with value=project
                // names with spaces don't work with .find
                var ele = $(this).children().map( function () {
                    if ($(this).val() == project)
                        return this;
                });
                $(ele).attr('selected', 'selected');

                var assignedType = $(this).attr('name').replace('projects-', '');
                var assignedSelector = 'select[name=assigned-' + assignedType + ']';
                $(assignedSelector).children().remove();

                if (project) {
                    $.get( ProjectAssignment.target, { type: assignedType, project: project },
                        function (data) {
                            $(assignedSelector).append(data);
                        }
                    );
                }
            });
         });

         $(':button[name^=add]').click( function() {
            var type = $(this).attr('name').replace('add-', '');
            var ele = $('select[name=' + type + ']').find('option:selected').get();
            var project = $('select[name=projects-' + type + ']').val();
            ProjectAssignment.add(ele, project, type)
         });

         $(':button[name^=remove]').click( function() {
            var type = $(this).attr('name').replace('remove-', '');
            var ele = $('select[name=assigned-' + type + ']').find('option:selected').get();
            var project = $('select[name=projects-' + type + ']').val();
            ProjectAssignment.remove(ele, project, type)
         });
         
    };
    jQuery.noConflict();
    jQuery(RUN_ON_READY);
}());