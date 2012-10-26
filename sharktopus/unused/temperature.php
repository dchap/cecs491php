<?php
require_once substr(__FILE__, 0, -4). '.behind.php';
use Config\Constants\Session_Variables as Session;
$page = new Lib\Views\Page(Session::Superuser);
$page->IncludeJs("/assets/file_uploads/upload.js");
$page->IncludeCss("/assets/file_uploads/upload.css");
$page->BeginHTML();


$form = new Lib\Views\Upload_Form('temperature');

$page->EndHTML(); 
?>