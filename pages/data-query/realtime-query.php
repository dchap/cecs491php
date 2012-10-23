<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
use Config\Constants\Query as QueryConstants;
$page = new Lib\Views\Page(Session::User);
$page->IncludeCss("/assets/data_query/main_query.css");
$page->IncludeJs("/assets/data_query/main_query.js");
$page->BeginHTML();
?>

<!--<script>
$(function() {
    $(':checkbox').attr('checked', true);
    $('.projects').val('Los_Islotes_TT');
    $('.datepicker').first().val(0);
    $('input[name^=time]').first().val(0);
});

</script>-->

<h3>Main Query</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group">
            <p>Project:</p>
            <?php HTMLControls::DropDownList("projects", "project", "--Select Project--", "projects span3")?>
        </div>
    </fieldset>
    <fieldset>
        <?php HTMLControls::DateTimeRange(); ?>
    </fieldset>
    
    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Query</button>
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>