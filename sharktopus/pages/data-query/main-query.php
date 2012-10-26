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

<!--<script>
$(function() {
    $(':checkbox').attr('checked', true);
    $('.projects').val('Los_Islotes_TT');
    $('.datepicker').first().val(0);
    $('input[name^=time]').first().val(0);
});

</script>-->

<h3>Main Query</h3><hr />
<form action="main-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group">
            <p>Project:</p>
            <?php HTMLControls::DropDownList("projects", "project", "--Select Project--", "projects span3")?>
        </div>
        <div class="query-group" id="outer-query">
            <p>Search By:</p>
            <label><input type="radio" name="outer-query" value="<?php echo QueryConstants::Fish; ?>" checked />Fish</label>
            <label><input type="radio" name="outer-query" value="<?php echo QueryConstants::Station; ?>" />Stations</label>
        </div>
        <div class="query-group fish-options">
            <p>Display:</p>
            <label><input type="radio" name="inner-query" value="<?php echo QueryConstants::Transmitter; ?>" checked />Transmitter Detections</label>
            <label><input type="radio" name="inner-query" value="<?php echo QueryConstants::Sensor; ?>" />Sensor Detections</label>
        </div>
        <div class="query-group station-options">
            <p>Display:</p>
            <label><input type="radio" name="inner-query" value="<?php echo QueryConstants::RecognizedFish; ?>" />Fish in Database</label>
            <label><input type="radio" name="inner-query" value="<?php echo QueryConstants::UnrecognizedFish; ?>" />Fish not in Database</label>
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
        <div class="query-group">
            <p>Station:</p>
            <input class="span2" name="<?php echo QueryConstants::StationKey; ?>" type="text" />
        </div>
        <div class="query-group">
            <p>Receiver:</p>
            <input class="span2" name="<?php echo QueryConstants::Receiver; ?>" type="text" />
        </div>
        <div class="query-group">
            <p>Genus:</p>
            <?php HTMLControls::DropDownListFish("genus", QueryConstants::Genus,"----------", "span2") ?>
        </div>
        <div class="query-group">
            <p>Species:</p>
            <?php HTMLControls::DropDownListFish("species", QueryConstants::Species, "----------", "span2") ?>
        </div>
    </fieldset>
    <fieldset>
        <div class="query-group ">
            <p>Transmitter/Sensor ID Range:</p>
            <input class="span2" name="<?php echo QueryConstants::TransmitterRangeStart; ?>" type="text" maxlength="5" />
            <span class="date-separator">to</span>
            <input class="span2" name="<?php echo QueryConstants::TransmitterRangeEnd; ?>" type="text" maxlength="5" />
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
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="f1" />Ascension</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="f2" />Genus</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="f3" />Species</label>
        </div>
        <div class="show-group">
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v3" />Receiver</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v4" checked />Date</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v5" checked />Time</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v6" />Sensor Value</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v7" />Sensor Unit</label>
        </div>
        <div class="show-group">
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s1" checked disabled/>Station Name</label>
            <input id ="station" type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s1"/>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s2"/>Station Latitude</label>
            <label><input type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s3"/>Station Longitude</label>
        </div>
  </fieldset>
    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Query</button>
    <button id="download-button" class="btn" data-loading-text="Creating File...">Download</button>
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="uploading..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>