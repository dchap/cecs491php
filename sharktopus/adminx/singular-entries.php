<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Lib\Views\HTMLControls as HtmlControls;
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::Admin);
$page->IncludeJs("/assets/adminx/js/singular_entries.min.js");
$page->IncludeCss("/assets/adminx/singular_entries.css");
$page->BeginHTML();
?>
<h3>Singular Entries</h3><hr />

<p>To <span class="label notice">add</span>, type in a new value in the text box and click "Add" for the corresponding data type.</p>
<p>To <span class="label warning">edit</span>, select a value from the list, type in a new value and click "Edit".</p>
<p>To <span class="label important">delete</span>, select a value from the list and click "Delete".</p>

<div class="form-group">
    <label class="custom-width" for="receivers"><strong>Receivers</strong></label>
    <div><?php HtmlControls::SelectList("receivers", 20, "receivers", "receivers custom-width"); ?></div>
    <p><strong>New receiver value:</strong></p>
    <div><input type="text" class="receivers custom-width" /></div>
    <div>
        <input class="btn" type="button" name="receivers-add" value="Add" />
        <input class="btn" type="button" name="receivers-edit" value="Edit" disabled />
        <input class="btn danger" type="button" name="receivers-delete" value="Delete" disabled />
    </div>
</div>
<div class="form-group">
    <label class="custom-width" for="stations"><strong>Stations</strong></label>
    <div><?php HtmlControls::SelectList("stations", 20, "stations", "stations custom-width"); ?></div>
    <p><strong>New station value:</strong></p>
    <div><input type="text" class="stations custom-width"/></div>
    <div>
        <input class="btn" type="button" name="stations-add" value="Add" />
        <input class="btn" type="button" name="stations-edit" value="Edit" disabled />
        <input class="btn danger" type="button" name="stations-delete" value="Delete" disabled />
    </div>
</div>
<div class="form-group">
    <label class="custom-width" for="projects"><strong>Projects</strong></label>
    <div><?php HtmlControls::SelectList("projects", 20, "projects", "projects custom-width"); ?></div>
    <p><strong>New project value:</strong></p>
    <div><input type="text" class="projects custom-width" /></div>
    <div>
        <input class="btn" type="button" name="projects-add" value="Add" />
        <input class="btn" type="button" name="projects-edit" value="Edit" disabled />
        <input class="btn danger" type="button" name="projects-delete" value="Delete" disabled />
    </div>
</div>
<div id="errors"></div>

<div id="modal-container-delete" class="modal fade" style="display:none;">
    <div class="modal-header">Confirm Delete</div>
    <div id="modal-body-delete" class="modal-body"></div>
    <div class="modal-footer">
        <input id="cancel-delete-button" class="btn" type="button" value="Cancel"/>
        <input id="confirm-delete-button" class="btn danger" type="button" value="Delete"/>
    </div>
</div>
<div id="modal-container-edit" class="modal fade" style="display:none;">
    <div class="modal-header">Confirm Edit</div>
    <div id="modal-body-edit" class="modal-body"></div>
    <div class="modal-footer">
        <input id="cancel-edit-button" class="btn" type="button" value="Cancel"/>
        <input id="confirm-edit-button" class="btn primary" type="button" value="Edit"/>
    </div>
</div>
<?php $page->EndHTML(); ?>