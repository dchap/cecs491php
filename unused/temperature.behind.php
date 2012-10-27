<?php
/**
 * todo: 
 * 1. add to upload table after users and sessions created
 * 2. not counting affected rows even though data is being stored
 * 
 * Stores a temperature file on the server in a path defined by UploadDirectories::Temperatures
 * as well as its contents in the database table 'temperatures'
 * 
 */

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();

use Config\Constants\Upload_Directories as UploadDirectories;
use Config\Constants\File_Types as FileType;
use Lib\File_Uploads\File_Storage as FileStorage;
use Lib\File_Uploads\DB_Storage_Temp as TempStorage;

$upload = 'uploadedFile1';

if (isset($_POST['action']))
{
    $filename = $_FILES[$upload]['name'];
    $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Temperatures. $filename;

    FileStorage::Save(FileType::Sonde, $_FILES[$upload], $uploadFilePath);
    $temp = new TempStorage($uploadFilePath, $filename);
    $temp->PDOStorage(FileType::Temperature);
}
?>