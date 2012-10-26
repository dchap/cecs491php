<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::Superadmin);
$page->IncludeJs("/assets/adminx/js/membership.min.js");
$page->IncludeCss("/assets/adminx/membership.css");
$page->BeginHTML();
?>

<h3>Member Records</h3><hr />
<p>When <span class="label warning">editing</span>, leave password fields blank to leave password unchanged.</p>
<div id="errors"></div>

<div>
    <h5 id="caption">Add a new member</h5>
    <form id="recordsForm" class="form-stacked" action="membership.behind.php" method="post">
        <div class="query-group">
            <label for="username">Username</label>
            <input id="username" class="span4 formInputs" name="username" type="text" maxlength="45" />
            <label for="password">Password</label>
            <input id="password" class="span4 formInputs" name="password" type="password" maxlength="45" />

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" class="span4 formInputs" name="confirm_password" type="password" maxlength="45" />
        </div>
        <div class="query-group">
            <label for="fname">First Name</label>
            <input class="span4 formInputs" name="fname" type="text" maxlength="45" />

            <label for="lname">Last Name</label>
            <input class="span4 formInputs" name="lname" type="text" maxlength="45" />

            <label for="account_type">Account Type</label>
            <select class="span4 formInputs" name="account_type">
                <option value="superadmin">Super Administrator</option>
                <option value="admin">Administrator</option>
                <option value="superuser" selected>Super User</option>
                <option value="user">Basic User</option>
            </select>
        </div>
        <input id="recordId" name="id" type="hidden" />
        <input id="actionType" name="actionType" type="hidden" />
        
        <div id="inputs">
            <input id="add-button" class="btn primary" value="Add" type="button" />
            <input id="edit-button" class="btn primary" value="Make Edit" type="button" />
            <input id="cancel-button" class="btn secondary-action" value="Cancel" type="button" />
        </div>
    </form>
</div>


<table id="recordsTable" class="zebra-striped bordered-table">
    <thead class="blue">
    <tr id="recordsTableFieldNames">
        <th></th>
        <th></th>
        <th>Username</th>
        <th>Password</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Account Type</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
<div id="noRecords" style="display:none">
    <p>No records found.</p>
</div>

<div id="modal-delete-confirm" class="modal fade" style="display:none;">
    <p class="modal-body">Are you sure you want to delete user "<span id="modal-user">this member</span>"?</p>
    <div class="modal-footer">
        <input id="modal-cancel" class="btn" type="button" value="Cancel"/>
        <input id="modal-delete" class="btn danger" type="button" value="Delete"/>
    </div>
</div>
<?php $page->EndHTML(); ?>