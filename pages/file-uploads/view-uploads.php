<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
use Config\Constants\File_Types as FileTypes;
use Config\Constants\Query as QueryConstants;
use Lib\Manual_Entries\Members_Access as MembersAccess;
use Lib\Views\HTMLControls as HTMLControls;
$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeCss("/assets/data_query/main_query.css");
$page->BeginHTML();
?>
<h3>Uploaded Files</h3><hr />
<p><span class="label notice">Notice</span> Number of entries per file may differ from the actual number of those entries stored in the database if there is data overlap between files.</p>
<form action="view-uploads.behind.php" method="get" class="queryForm" id="main-form">
    <fieldset>
        <div class="query-group" id="outer-query">
          <p>Uploader:</p>
            <select class="members filter" name="<?php echo QueryConstants::Members; ?>">
                <option value="">All</option>
                <?php foreach (MembersAccess::GetAllMembers() as $member): ?>
                <option value="<?php echo $member->getValueEncoded($member->fname) . " " . $member->getValueEncoded($member->lname); ?>">
                    <?php echo $member->getValueEncoded($member->fname) . " " . $member->getValueEncoded($member->lname); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="query-group">
          <p>Results Per Page:</p>
          <select name="<?php echo QueryConstants::Limit; ?>" id="limit" class="filter">
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
          </select>
        </div>
    </fieldset>
    <input type="hidden" name="action-type" />
</form>
<div id="results"></div>
<div id="errors"></div>

<script type="text/javascript">
    $(function() {
        var ViewUploads = {};
        ViewUploads.target = $('form').attr('action');

        ViewUploads.getEntries = function() {
            $('#results').html('');
            $('#no-records').hide();
            $(':hidden[name=action-type]').val('query');
            $.get(
                ViewUploads.target,
                $('form').serialize(),
                function(data) { 
                    $('#results').append(data);
                }
            );
        };
    
        $('.pagination-page').live('click', function(event) {
            event.preventDefault();
            var queryString = $('form').serialize() + '&' + $(this).attr('href') + '&' + $('.pagination').attr('data-count');
            $('#results, #errors').html('');
            $.ajax({
                url: ViewUploads.target,
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

        $('.filter').change(ViewUploads.getEntries);
        
//////////////////////////////////// ON START //////////////////////////////////

        ViewUploads.getEntries();
        $('.bth').button();
    });
</script>
<?php if ($_SESSION[Session::AccountType] >= Session::Admin) { ?>
<div id="modal-delete-container" class="modal fade" style="display:none;">
    <div class="modal-body">
        <p>Are you sure you want to delete "<span id="modal-filename">Filename</span>"?</p>
    </div>
    <div class="modal-footer">
        <button id="modal-cancel" class="btn" type="button">Cancel</button>
        <button id="modal-delete" class="btn danger" type="button" data-loading-text="Processing..">
            Delete
        </button>
    </div>
</div>
<div class="modal-backdrop" style="display: none"></div>
<script type="text/javascript">
    $(function() {
        $('.delete').live('click', function() {
            var id = $(this).closest('tr[data-id]').attr('data-id');
            $('div#errors').children().remove();
            var filename = $(this).parent().nextAll('.filename').text();
            var filetype = $(this).parent().nextAll('.file_type').text();
            
            $('#modal-delete').data({
                filename: filename,
                filetype: filetype,
                'action-type': 'delete',
                id: id
            });
            $('#modal-filename').text(filename);
            $('.modal-backdrop').show();
            $('#modal-delete-container').modal('show');
        });
        
        $('#modal-delete').click(function(){
            $(':hidden[name=action-type]').val('delete');
            $(this).button('loading');
            $('#modal-cancel').attr('disabled', true);
            $('<img/>', {   
                src: "/assets/shared/ajax-loader.gif", 
                css: { 
                    'vertical-align': 'middle',
                    'margin': '10px auto 0 auto',
                    'display': 'block'
                } 
            }).appendTo('.modal-body');
            
            $.ajax({
                type: 'post',
                url: $('form').attr('action'),
                data: { 
                    filename: $(this).data('filename'), 
                    filetype: $(this).data('filetype'), 
                    'action-type': 'delete' 
                },
                success: function(data) {
                    $('tr[data-id=' + $('#modal-delete').data('id') + ']').remove();
                    modalReset();
                },
                error: function(data) {
                    $('.modal-body').append('<h6><span class="label important">error</span>'
                        + data.responseText + '</h6>');
                    $('.modal-body img').remove();
                    $('#modal-cancel').attr('disabled', false);
                }
            });
        });
        
        $('#modal-cancel').click( function() {
            modalReset();
        });
        
        modalReset = function(){
            $('#modal-delete-container').modal('hide');
            $('.modal-backdrop').hide();
            $('.modal-body > :not(p)').remove();
            $('#modal-delete').button('reset');
            $('#modal-cancel').attr('disabled', false);
        };
        
        $('#modal-delete-container').modal({
            backdrop: false,
            keyboard: false
        });
    });
</script>
<?php } // end admin account privilege ?>

<?php $page->EndHTML(); ?>