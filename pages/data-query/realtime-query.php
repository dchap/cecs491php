
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

<h3>Realtime</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group ">
<!--            <p>Frequency Codespace:</p>-->
<!--            <input class="span3" name="<?php //echo QueryConstants::FrequencyCodespace; ?>" value ="A69-1303" type="text" maxlength="15" />-->
<!--             <input class="span3" name="<?php //echo QueryConstants::FrequencyCodespace; ?>" type="text" maxlength="15" /> -->
        </div>
        <div class="query-group ">
<!--            <p>Transmitter ID:</p>-->
<!--            <input class="span2" name="<?php //echo QueryConstants::TransmitterID; ?>" value="47436" type="text" maxlength="10" />-->
<!--             <input class="span2" name="<?php //echo QueryConstants::TransmitterID; ?>" type="text" maxlength="10" />-->
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

<!-- Ignore date/time 
    <fieldset>
        <!-- DateTimeRange() for Date and Time Range is in lib/views/htmlcontrols.php - ->
        <?//php HTMLControls::DateTimeRange(); ?>
        
        
        <div class="inline-inputs">
            <div>
                <label>Date Range: <span class="help-inline">(YY-MM-DD)</span></label>
                <input class="small datepicker" type="text" name="<?//php echo QueryConstants::DateStart; ?>" value =" "/>
                <span class="date-separator">to</span>
                <input class="small datepicker" type="text" name="<?//php echo QueryConstants::DateEnd; ?>" />
            </div>
            <div style="padding-left: 20px">
                <label>Time Range: <span class="help-inline">(HH:MM)</span></label>
                <input class="mini" type="text" name="<?//php echo QueryConstants::TimeStart; ?>" value =" "/>
                <span class="date-separator">to</span>
                <input class="mini" type="text" name="<?//php echo QueryConstants::TimeEnd; ?>" />
            </div>
        </div>
    </fieldset>
-->  
    
    <fieldset class="show">
       <!-- <p>Show Columns:</p> -->
        <div class="show-group">
            <!--<label> --> <input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v1" checked/><!-- Codespace</label>-->
            <!--<label> --><input <input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v2" checked /><!-- <span id="st-switch">Transmitter ID</span></label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s1" checked disabled/><!-- Station Name</label> -->
            <input id ="station" type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s1"/>
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s2"checked/><!-- Station Latitude</label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="s3"checked/><!-- Station Longitude</label> -->
        </div>
        <div class="show-group">
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v3" checked/><!-- Receiver</label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v4" checked/><!-- Date</label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v5" checked/><!-- Time</label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v6" checked/><!-- Sensor Value</label> -->
            <!--<label> --><input type="hidden" type="checkbox" name="<?php echo QueryConstants::Fields; ?>[]" value="v7" checked/><!-- Sensor Unit</label> -->
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





<?/* php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
use Config\Constants\Query as QueryConstants;
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::User);
$page->IncludeCss("/assets/data_query/main_query.css");
$page->IncludeJs("/assets/data_query/main_query.js");
$page->BeginHTML();
*/
?>


<!--
<script>
$(function() {
    $(':checkbox').attr('checked', true);
    $('.projects').val('Los_Islotes_TT');
    $('.datepicker').first().val(0);
    $('input[name^=time]').first().val(0);
});
</script>
-->

<!--
Initial Date
2010-08-02
-->

<!--
Initial Time
17:57:40
-->

<!--
<h3>Real Time Query</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="inline-inputs">
            <div>
                <label>Date Range: <span class="help-inline">(YY-MM-DD)</span></label>
                <!--  <input class="small datepicker" type="text" name="<?php //echo QueryConstants::DateStart; ?>" />  - ->
                <input type="hidden" name ="<?// php echo QueryConstants::DateStart; ?>" value="2010-08-02"/>
                <span class="date-separator">to</span>
                <input type="hidden" name="<?// php echo QueryConstants::DateEnd; ?>" value="2012-10-20"/>
            </div>
                <label>Time Range: <span class="help-inline">(HH:MM)</span></label> 
                <!--<input class="mini" type="text" name="<?php //echo QueryConstants::TimeStart; ?>" value="17:57:40" /> - ->
                <input type="hidden" name="<?// php echo QueryConstants::TimeStart; ?>" value="17:57:40"/>
                <span class="date-separator">to</span>
                <input type="hidden" name="<?// php echo QueryConstants::TimeEnd; ?>" value="24:00:00" />
            </div>
        </div>
    </fieldset>
    <fieldset>
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v1" /> <!-- v1 : Codespace - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v2" /> <!-- v2 : Transmitter ID - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v3" /> <!-- v3 : Receiver - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v4" /> <!-- v4 : Date - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v5" /> <!-- v5 : Time - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v6" /> <!-- v6 : Sensor Value - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="v7" /> <!-- v7 : Sensor Unit - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="s1" /> <!-- s1 : Station Name - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="s2" /> <!-- s2 : Latitude - ->
        <input type="hidden" name="<?// php echo QueryConstants::Fields; ?>[]" value="s3" /> <!-- s3 : Longitude - ->
    </fieldset>    
    
    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?// php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?// php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Search</button>
</form>

<div id="results"></div>
<div id="errors"></div>
-->


<?// php $page->EndHTML(); ?>
