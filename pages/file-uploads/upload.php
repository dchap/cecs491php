<?php
require_once substr(__FILE__, 0, -4). '.behind.php';
use Config\Constants\Session_Variables as Session;
use Config\Constants\File_Types as FileType;
use Lib\Views\Upload_Form as UploadForm;
$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/file_uploads/upload.min.js");
$page->IncludeCss("/assets/file_uploads/upload.css");
$page->BeginHTML();
$vue = strtolower(FileType::Vue);
$temp = strtolower(FileType::Temperature);
$sonde = strtolower(FileType::Sonde);
$meta = strtolower(FileType::Metadata);
?>
<h3>Upload a File</h3><br />
<p><span class="label notice">Note</span> Max file size up to 100MB</p><br />
<ul class="tabs" data-tabs="tabs">
  <li class="active"><a href="#<?php echo $vue; ?>">Vue</a></li>
  <li><a href="#<?php echo $temp; ?>">Temperature</a></li>
  <li><a href="#<?php echo $sonde; ?>">Sonde</a></li>
  <li><a href="#<?php echo $meta; ?>">Metadata</a></li>
</ul>
<style>td { border: 1px;}</style>
<div class="tab-content">
    <div id="<?php echo $vue; ?>" class="tab-pane active">
        <p>Vue file heading format:</p>
        <table class="bordered-table">
            <tr>
                <th>Date and Time</th>
                <th>Receiver</th>
                <th>Transmitter</th>
                <th>...</th>
                <th>Sensor Value</th>
                <th>Sensor Unit</th>
            </tr>
        </table>
        <?php UploadForm::Generate($vue); ?>
    </div>
    <div id="<?php echo $temp; ?>" class="tab-pane">
        <p>Temperature file heading format (temp. will be converted to &#176;C):</p>
        <table class="bordered-table">
            <tr><td>Plot Title: (Station Name)</td><td colspan="4"></td></tr>    
            <tr>
                <th>#</th>
                <th>Date Time</th>
                <th>Temp</th>
                <th>Intensity</th>
                <th>Batt</th>
            </tr>
        </table>
        <?php UploadForm::Generate($temp); ?>
    </div>
    <div id="<?php echo $sonde; ?>" class="tab-pane">
        <p>Sonde file heading format:</p>
        <table class="bordered-table">
            <tr><td>(Station Name)</td><td colspan="4"></td></tr>    
            <tr>
                <th>Date</th>
                <th>Temp</th>
                <th>SpCond</th>
                <th>TDS</th>
                <th>Salinity</th>
                <th>...</th>
            </tr>
        </table>
        <?php UploadForm::Generate($sonde); ?>
    </div>
    <div id="<?php echo $meta; ?>" class="tab-pane">
        <p>Metadata file heading format:</p>
        <table class="bordered-table">
            <tr>
                <th>Date/Time</th>
                <th>Receiver</th>
                <th>Description</th>
                <th>Data</th>
                <th>Units</th>
            </tr>
        </table>
        <?php UploadForm::Generate($meta); ?>
    </div>
</div>
<div id="errors"></div>
<hr />
<h6>recent upload results:</h6>
<div id="uploaded-files-container">
</div>
<?php

$page->EndHTML(); 
?>