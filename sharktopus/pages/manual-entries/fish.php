<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
use Config\Constants\Query as QueryConstants;
$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/manual_entries/manual_entries.js");
$page->IncludeJs("/assets/manual_entries/fish.js");
$page->IncludeCss("/assets/manual_entries/fish.css");
$page->BeginHTML();
?>

<h3>Fish Records</h3>
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
        <form id="records-form" class="form-stacked" action="fish.behind.php" method="post">
            <div class="control-group">
                <label for="codespace">Codespace</label>
                <input class="formInputs span3" name="codespace" type="text" maxlength="20"/>

                <label for="transmitter_id">Transmitter ID</label>
                <input class="formInputs span3 reqDigits" name="transmitter_id" type="text" maxlength="11"/>

                <label for="ascension">Ascension</label>
                <input id="ascension" class="formInputs span3" name="ascension" type="text" maxlength="45" />

                <label for="genus">Genus</label>
                <input class="formInputs span3" name="genus" type="text" maxlength="45" />

                <label for="species">Species</label>
                <input class="formInputs span3" name="species" type="text" maxlength="45" />

                <label for="sensor_codespace1">Sensor Codespace 1</label>
                <input class="formInputs span3" name="sensor_codespace1" type="text" maxlength="20" />

                <label for="sensor_id1">Sensor ID 1</label>
                <input class="formInputs span3" name="sensor_id1" type="text" maxlength="11" />

                <label for="sensor_codespace2">Sensor Codespace 2</label>
                <input class="formInputs span3" name="sensor_codespace2" type="text" maxlength="20" />

                <label for="sensor_id2">Sensor ID 2</label>
                <input class="formInputs span3" name="sensor_id2" type="text" maxlength="11" />
            </div>
            <div class="control-group">
                <label for="sensor_codespace3">Sensor Codespace 3</label>
                <input class="formInputs span3" name="sensor_codespace3" type="text" maxlength="20" />

                <label for="sensor_id3">Sensor ID 3</label>
                <input class="formInputs span3" name="sensor_id3" type="text" maxlength="11" />
  
                <label for="date_deployed">Deploy Date<span class="help-block">(YY-MM-DD)</span></label>
                <input class="formInputs small datepicker" name="date_deployed" type="text" maxlength="10" />

                <label for="time_deployed">Deploy Time</label>
                <input class="formInputs span3" name="time_deployed" type="text" maxlength="8" />

                <label for="sex">Sex</label>
                <select class="formInputs span3" name="sex">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="unknown">Unknown</option>
                </select>

                <label for="total_length">Total Length (mm)</label>
                <input class="formInputs span3" name="total_length" type="text" maxlength="11" />

                <label for="fork_length">Fork Length (mm)</label>
                <input class="formInputs span3" name="fork_length" type="text" maxlength="11" />

                <label for="standard_length">Standard Length (mm)</label>
                <input class="formInputs span3" name="standard_length" type="text" maxlength="11" />

                <label for="girth">Girth (mm)</label>
                <input class="formInputs span3" name="girth" type="text" maxlength="11" />
            </div>
            <div class="control-group">
                <label for="weight">Weight (g)</label>
                <input class="formInputs span3" name="weight" type="text" maxlength="11" />

                <label for="dart_tag">Dart Tag</label>
                <input class="formInputs span3" name="dart_tag" type="text" maxlength="45" />

                <label for="dart_color">Dart Color</label>
                <input class="formInputs span3" name="dart_color" type="text" maxlength="45" />

                <label for="landed_latitude">Landed Latitude</label>
                <input class="formInputs span3" name="landed_latitude" type="text" maxlength="11" />

                <label for="landed_longitude">Landed Longitude</label>
                <input class="formInputs span3" name="landed_longitude" type="text" maxlength="11" />

                <label for="released_latitude">Released Latitude</label>
                <input class="formInputs span3" name="released_latitude" type="text" maxlength="11" />

                <label for="released_longitude">Released Longitude</label>
                <input class="formInputs span3" name="released_longitude" type="text" maxlength="11" />

                <label for="time_out_of_water">Time Out of Water (min)</label>
                <input class="formInputs span3" name="time_out_of_water" type="text" maxlength="11" />

                <label for="time_in_tricane">Time in Tricane (min)</label>
                <input class="formInputs span3" name="time_in_tricane" type="text" maxlength="11" />
            </div>
            <div class="control-group">
                <label for="time_in_surgery">Time in Surgery (min)</label>
                <input class="formInputs span3" name="time_in_surgery" type="text" maxlength="11" />

                <label for="recovery_time">Recovery Time (min)</label>
                <input class="formInputs span3" name="recovery_time" type="text" maxlength="11" />

                <label for="landing_depth">Depth of Landing (m)</label>
                <input class="formInputs span3" name="landing_depth" type="text" maxlength="11" />

                <label for="release_depth">Depth of Release (m)</label>
                <input class="formInputs span3" name="release_depth" type="text" maxlength="11" />

                <label for="landing_temperature">Landing Temperature (sst)</label>
                <input class="formInputs span3" name="landing_temperature" type="text" maxlength="11" />

                <label for="release_temperature">Release Temperature (sst)</label>
                <input class="formInputs span3" name="release_temperature" type="text" maxlength="11" />
                <label for="fish_condition">Fish Condition</label>
                <input class="formInputs span3" name="fish_condition" type="text" maxlength="45" />

                <label for="release_method">Method of Release</label>
                <select class="formInputs span3" name="release_method">
                    <option value="Freeswam">Freeswam</option>
                    <option value="Crated">Crated</option>
                    <option value="Descender hook">Descender hook</option>
                </select>

        <!--        <label for="photo_reference">Photo</label>-->
                <input class="formInputs span3" name="photo_reference" type="hidden" />

                <label for="comment">Comment<span class="help-inline">(100 chars max)</span></label>
                <textarea class="formInputs span3" name="comment" maxlength="100" ></textarea>
            </div>
            <input id="record-id" name="id" type="hidden" />
            <input id="action-type" name="action-type" type="hidden" />
            <div id="modal-errors">
                <label class="error">"Transmitter" and "codespace" taken together are not unique</label>
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
    <p class="modal-body">Are you sure you want to delete this fish record?</p>
    <div class="modal-footer">
        <input id="delete-cancel-button" class="btn" type="button" value="Cancel"/>
        <input id="delete-confirm-button" class="btn danger" type="button" value="Delete"/>
    </div>
</div>

<div id="results" class="clear">
</div>
<div id="no-records" style="display:none">
    <p>No records found.</p>
</div>


<?php $page->EndHTML(); ?>