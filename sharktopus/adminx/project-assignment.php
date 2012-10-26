<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HTMLControls;
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::Admin);
$page->IncludeJs("/assets/adminx/js/project_assignment.min.js");
$page->IncludeCss("/assets/adminx/project_assignment.css");
$page->BeginHTML();
?>
<h3>Assign to Project</h3><hr />
<p>Select stations/fish/projects before adding or removing associations.</p>
<fieldset>
    <div class="form-group">
        <label class="span3 pad" for="stations"><strong>Stations</strong></label>
        <?php HTMLControls::SelectList("stations", 20, "stations", "stations span3 select-list", $stationList);?>
    </div>
    <div class="form-group">
        <div class="top"></div>
        <div class="mid">
            <input class="btn primary span2" type="button" name="add-stations" value="Add >>" />
            <input class="btn span2" type="button" name="remove-stations" value="<< Remove" />
        </div>
    </div>
    <div class="form-group">
        <?php HTMLControls::DropDownList("projects", "projects-stations", "--Select Project--", "projects-stations span3 pad"); ?>
        <select class="span3 select-list" name="assigned-stations" size="20"></select>
    </div>
</fieldset>
<fieldset>
    <div class="form-group">
        <label class="span3 pad" for="fish"><strong>Fish</strong></label>
        <?php HTMLControls::SelectListValueDisparity(20, "fish", "fish span3 select-list", $fishList);?>
    </div>
    <div class="form-group">
        <div class="top"></div>
        <div class="mid">
            <input class="btn primary span2" type="button" name="add-fish" value="Add >>" />
            <input class="btn span2" type="button" name="remove-fish" value="<< Remove" />  
        </div>
    </div>
    <div class="form-group">
        <?php HTMLControls::DropDownList("projects", "projects-fish", "--Select Project--", "projects-fish span3 pad"); ?>
        <select class="span3 select-list" name="assigned-fish" size="20"></select>
    </div>
</fieldset>    
<div id="errors"></div>

<?php $page->EndHTML(); ?>