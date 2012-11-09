<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
use Config\Database\Mysqli_Connection as MysqliConnect;

$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/shared/js/bootstrap/bootstrap-dropdown.js");
$page->BeginHTML();


//FOR PRINTING EMAIL ADDRESS IN TEXT BOX ---------------------------------
$sql = "SELECT `email` FROM `members` WHERE `username` = '" .$_SESSION[Session::Name] ."'";
echo "$sql";

$db = MysqliConnect::GetMysqliInstance();
$result = $db->query($sql);
//var_dump($result);

$row = $result->fetch_row();
echo $row[0];

MysqliConnect::Disconnect();
//-----------------------------------------------------------------------

?>

<h3>Email</h3><hr />
<form action="email.behind.php" method="get" class="queryForm form-stacked" id="main-form">
    <fieldset>
        <div class="query-group ">
            <p>Email:</p>
            <input class="span3" name="email" type="text" value="<?php echo isset($result) ? null : $result; ?>" maxlength="45" />
        </div>
    </fieldset>
    <fieldset class="show">
        <p>Email Preference:</p>
        <div class="show-group">
            <label><input type="radio" name="email-preference" value="0" checked/>No email</label>
            <label><input type="radio" name="email-preference" value="1"/><span id="st-switch">Once every 24 hours</span></label>
            <label><input type="radio" name="email-preference" value="2"/>Every detection</label>
        </div>
  </fieldset>
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Submit</button>
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="updating..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>

