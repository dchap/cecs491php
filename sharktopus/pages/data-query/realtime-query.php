<?php
header( "refresh:60;url=realtime-query.php" );
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
use Config\Constants\Urls as Urls;
use Config\Constants\Query as QueryConstants;
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::User);
$page->IncludeCss("/assets/data_query/main_query.css");
$page->IncludeJs("/assets/data_query/main_query.js");
$page->BeginHTML();
?>

<h3>Realtime</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group">
          <p>Results Per Page:</p>
          <select class="mini" name="<?php echo QueryConstants::Limit; ?>" id="limit">
            <option value="50">50</option>
            <option value="100" selected="selected">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
          </select>
        </div>
    </fieldset> 

    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Search</button>
    <!--<input id="query-button" type="hidden" class="btn primary" data-loading-text="Processing..." />
    <!-- <button id="download-button" class="btn" data-loading-text="Creating File...">Download</button> -->
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="uploading..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>