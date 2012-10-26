<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
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


<!-- Initial Date -->
<!--2010-08-02 -->

<!-- Initial Time -->
<!--17:57:40 -->

<h3>Real Time Query</h3><hr />
<form action="realtime-query.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="inline-inputs">
            <div>
                <label>Date Range: <span class="help-inline">(YY-MM-DD)</span></label>
                <!--  <input class="small datepicker" type="text" name="<?php //echo QueryConstants::DateStart; ?>" />  -->
                <input type="hidden" name ="<?php echo QueryConstants::DateStart; ?>" value="2010-08-02"/>
                <span class="date-separator">to</span>
                <input type="hidden" name="<?php echo QueryConstants::DateEnd; ?>" value="2012-10-20"/>
            </div>
                <label>Time Range: <span class="help-inline">(HH:MM)</span></label> 
                <!--<input class="mini" type="text" name="<?php //echo QueryConstants::TimeStart; ?>" value="17:57:40" /> -->
                <input type="hidden" name="<?php echo QueryConstants::TimeStart; ?>" value="17:57:40"/>
                <span class="date-separator">to</span>
                <input type="hidden" name="<?php echo QueryConstants::TimeEnd; ?>" value="24:00:00" />
            </div>
        </div>
    </fieldset>
    <fieldset>
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v1" /> <!-- v1 : Codespace -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v2" /> <!-- v2 : Transmitter ID -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v3" /> <!-- v3 : Receiver -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v4" /> <!-- v4 : Date -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v5" /> <!-- v5 : Time -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v6" /> <!-- v6 : Sensor Value -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="v7" /> <!-- v7 : Sensor Unit -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s1" /> <!-- s1 : Station Name -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s2" /> <!-- s2 : Latitude -->
        <input type="hidden" name="<?php echo QueryConstants::Fields; ?>[]" value="s3" /> <!-- s3 : Longitude -->
    </fieldset>    
    
    <input type="hidden" name="action-type" />
    <input type="hidden" name="<?php echo QueryConstants::SortBy; ?>" value="stations_name" />
    <input type="hidden" name="<?php echo QueryConstants::SortOrder; ?>" value="asc" />
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Search</button>
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>