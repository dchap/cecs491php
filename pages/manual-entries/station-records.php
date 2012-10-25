<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
use Config\Constants\Session_Variables as Session;
use Config\Constants\Query as QueryConstants;
$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/manual_entries/manual_entries.js");
$page->IncludeJs("/assets/manual_entries/station_records.js");
$page->IncludeCss("/assets/manual_entries/manual_entries.css");
$page->BeginHTML();
?>

<h3>Station Records</h3>
<div>
    <form id="query-form">
        <?php \Lib\Views\HTMLControls::DropDownList("projects", "project", "All Projects", "projects span3"); ?>
        <label>Results per page:</label>
        <select class="mini projects" name="<?php echo QueryConstants::Limit; ?>">
            <option value="50" selected="selected">50</option>
            <option value="100">100</option>
        </select>
        <input id="download-button" class="btn primary" type="button" value="Download" />
        <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="loading..." />
        <input type="hidden" name="action-type" value="query"/>
    </form>
</div>
<br />
<p class="clear"><span class="label notice">Note</span> Double clicking a table cell's value will scroll the window to the left for easier editing.</p>
<div id="errors" class="clear"></div>
<div id="modal-form-container" class="modal fade">
    <div class="modal-header"><h5 id="form-title">Add a new record</h5></div>
    <div class="modal-body">
        <form id="records-form" class="form-stacked" action="station-records.behind.php" method="post">
            <div class="control-group">
                <label for="stations_name">Station Name</label>
                <?php HTMLControls::DropDownList("stations", "stations_name", "--Stations--", "formInputs span3"); ?>

                <label for="receivers_id">Receiver Id</label>
                <?php HTMLControls::DropDownList("receivers", "receivers_id", "--Receivers--", "formInputs span3"); ?>

                <label for="release_value">Release</label>
                <input class="formInputs span3" name="release_value" type="text" maxlength="11" />

                <label for="hobo">Hobo</label>
                <input class="formInputs span3" name="hobo" type="text" maxlength="11" />

                <label for="frequency_codespace">Frequency Codespace</label>
                <input class="formInputs span3" name="frequency_codespace" type="text" maxlength="20" />

                <label for="sync_tag">Sync Tag</label>
                <input class="formInputs span3" name="sync_tag" type="text" maxlength="11" />

                <label for="latitude">Latitude</label>
                <input class="formInputs span3" name="latitude" type="text" maxlength="11" />

                <label for="longitude">Longitude</label>
                <input class="formInputs span3" name="longitude" type="text" maxlength="11" />

                <label for="secondary_latitude">Secondary Latitude</label>
                <input class="formInputs span3" name="secondary_latitude" type="text" maxlength="11" />

                <label for="secondary_longitude">Secondary Longitude</label>
                <input class="formInputs span3" name="secondary_longitude" type="text" maxlength="11" />
            </div>
            <div class="control-group">
                <label for="secondary_waypoint">Secondary Waypoint</label>
                <input class="formInputs span3" name="secondary_waypoint" type="text" maxlength="11" />

                <label for="depth">Depth</label>
                <input class="formInputs span3" name="depth" type="text" maxlength="11" />

                <label for="receiver_height">Receiver Height</label>
                <input class="formInputs span3" name="receiver_height" type="text" maxlength="11" />
                
                <label for="date_in">Date In<span class="help-block">(YYYY-MM-DD)</span></label>
                <input class="formInputs small datepicker" name="date_in" type="text" />
                
                <label for="time_in">Time In<span class="help-block">(HH:MM:SS)</span></label>
                <input class="formInputs span3" name="time_in" type="text" />

                <label for="date_out">Date Out</label>
                <input class="formInputs small datepicker" name="date_out" type="text" />

                <label for="time_out">Time Out</label>
                <input class="formInputs span3" name="time_out" type="text" />

                <label for="date_downloaded">Date Downloaded</label>
                <input class="formInputs small datepicker" name="date_downloaded" type="text" />

                <label for="comment">Comment<span class="help-block">(< 100 chars)</span></label>
                <textarea class="formInputs span3" name="comment" maxlength="100"></textarea>
            </div>
            <input id="record-id" name="id" type="hidden" />
            <input id="action-type" name="action-type" type="hidden" />
            <div id="modal-errors">
                <label class="error">"Station name" and "date in" and "time in" taken together are not unique</label>
            </div>
            <div class="inputs">
                <input id="cancel-button" class="btn" value="Cancel" type="button" />
                <input id="add-confirm-button" class="btn primary" value="Add" type="button" />
                <input id="edit-confirm-button" class="btn primary" value="Make Edit" type="button" />
            </div>
        </form>
    </div>
</div>

<div id="modal-delete-container" class="modal fade">
    <p class="modal-body">Are you sure you want to delete this station record?</p>
    <div class="modal-footer">
        <input id="delete-cancel-button" class="btn" type="button" value="Cancel"/>
        <input id="delete-confirm-button" class="btn danger" type="button" value="Delete"/>
    </div>
</div>

<div id="results"></div>

<?php $page->EndHTML(); ?>