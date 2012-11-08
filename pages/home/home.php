<?php
require_once substr(__FILE__, 0, -4). '.behind.php';
use Config\Constants\Session_Variables as Session;

$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/shared/js/bootstrap/bootstrap-dropdown.js");
$page->BeginHTML();

//var_dump($user['account_type']);
?>

<ul class="tabs" data-tabs="tabs">
  <li class="active"><a href="#start">Getting Started</a></li>
  <li><a href="#target">License</a></li>
</ul>
<div class="tab-content">
    <div id="start" class="tab-pane active">
        <ol>
            <li>Create members
                <ul>
                    <li>Basic user: can access query page</li>
                    <li>Super user: can access above plus manual entries/ upload pages/ home</li>
                    <li>Admin: can access above plus singular entries (receivers/stations/projects/project assignment)</li>
                    <li>Super admin: can access above plus membership page</li>
                </ul>
            </li>
            <li>Have admin+ level user add receivers/stations/projects <em>before</em> uploading files or entering other data</li>
            <li>Upload files/ manually enter data only containing receivers/stations previously entered or you will get an error
                <ul>
                    <li>Only admins can delete uploaded files</li>
                </ul>
            </li>
            <li>Assign fish/stations to projects</li>
            <li>Run queries</li>
        </ol>
    </div>
    <div id="target" class="tab-pane">
        <h6>License</h6>
        <p>Copyright (C) 2012 Edward Han</p>
        <p>This program is free software: you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation, either version 3 of the License, or
        (at your option) any later version.</p>

        <p>This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.</p>
        <p>You should have received a copy of the GNU General Public License along with this program.  If not, see <a href="http://www.gnu.org/licenses">http://www.gnu.org/licenses</a></p>

    </div>
</div>
<script type="text/javascript">
    $('.tabs').tabs();
</script>
<?php $page->EndHTML(); ?>