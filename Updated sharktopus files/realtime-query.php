<?php
header ("refresh:60;url=realtime-query.php");
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::User);
$page->IncludeCss("/assets/data_query/realtime_query.css");
$page->IncludeJs("/assets/data_query/realtime_query.js");
$page->BeginHTML();
?>

<h3>Real-time</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <input type="hidden" name="action-type" />
    <input  type="hidden" id="query-chbutton" /> 
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="uploading..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>