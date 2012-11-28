<?php
require_once substr(__FILE__, 0, -4) . '.behind.php';
use Config\Constants\Session_Variables as Session;
use Config\Database\Mysqli_Connection as MysqliConnect;

$page = new Lib\Views\Page(Session::User);
$page->IncludeJs("/assets/shared/js/bootstrap/bootstrap-dropdown.js");
$page->IncludeJs("/pages/email-validation.js");
$page->BeginHTML();


//FOR PRINTING EMAIL ADDRESS IN TEXT BOX, SELECTING CURRENT EMAIL PREFERENCE ---------------------------------
$sql = "SELECT `email`, `email_preference` FROM `members` WHERE `username` = '" .$_SESSION[Session::Name] ."'";

$db = MysqliConnect::GetMysqliInstance();
$result = $db->query($sql);

$row = $result->fetch_row();
$email = $row[0];
$email_preference = $row[1];


MysqliConnect::Disconnect();
//------------------------------------------------------------------------------------------------------------
?>

<h3>Email</h3><hr />
<form name="form1" action="email.behind.php" method="get" class="queryForm form-stacked" id="main-form" onsubmit="return validate()">
    <fieldset>
        <div class="query-group ">
            <p>Email:</p>
            <input class="span3" name="email" type="text" value="<?php echo isset($result) ? $row[0] : null; ?>" maxlength="45" />
        </div>
    </fieldset>
    <fieldset class="show">
        <p>Email Preference:</p>
        <div class="show-group">
            <?php if ($email_preference == 0) {?>
                <label><input type="radio" name="email-preference" value="0" checked/>No email</label>
                <label><input type="radio" name="email-preference" value="1"/><span id="st-switch">Once every 24 hours (daily at 12:00 PM)</span></label>
                <label><input type="radio" name="email-preference" value="2"/>Every detection (Every 4 hours?)</label>
            <?php } else if($email_preference == 1) { ?>
                <label><input type="radio" name="email-preference" value="0"/>No email</label>
                <label><input type="radio" name="email-preference" value="1" checked/><span id="st-switch">Once every 24 hours (daily at 12:00 PM)</span></label>
                <label><input type="radio" name="email-preference" value="2"/>Every detection (Every 4 hours?)</label>
            <?php } else { ?>
                <label><input type="radio" name="email-preference" value="0"/>No email</label>
                <label><input type="radio" name="email-preference" value="1"/><span id="st-switch">Once every 24 hours (daily at 12:00 PM)</span></label>
                <label><input type="radio" name="email-preference" value="2" checked/>Every detection (Every 4 hours?)</label>
            <?php } ?>
            
        </div>
  </fieldset>
    <button id="query-button" class="btn primary" data-loading-text="Processing...">Submit</button>
    <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="updating..." />
</form>

<div id="results"></div>
<div id="errors"></div>

<?php $page->EndHTML(); ?>

