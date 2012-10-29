<?php
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

<h3>Visitor Query</h3><hr />
<form action="visitor-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group ">
            <p>Frequency Codespace:</p>
            <input class="span3" name="<?php echo QueryConstants::FrequencyCodespace; ?>" type="text" maxlength="15" />
        </div>
        <div class="query-group ">
            <p>Transmitter ID:</p>
            <input class="span2" name="<?php echo QueryConstants::TransmitterID; ?>" type="text" maxlength="10" />
        </div>
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
    <fieldset>
        <?php HTMLControls::DateTimeRange(); ?> 
    </fieldset>
    <fieldset class="show">
        <p>Show Columns:</p>
        <div class="show-group">
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v1" checked/>Codespace</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v2" checked /><span id="st-switch">Transmitter ID</span></label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s1" checked disabled/>Station Name</label>
            <input id ="station" type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s1"/>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s2"/>Station Latitude</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s3"/>Station Longitude</label>
        </div>
        <div class="show-group">
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v3" />Receiver</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v4" checked />Date</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v5" checked />Time</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v6" />Sensor Value</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v7" />Sensor Unit</label>
        </div>
  </fieldset>
    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Search</button>
    <button id="download-button" class="btn" data-loading-text="Creating File...">Download</button>
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="uploading..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>