<?php
/**
 * todo: 
 * 1. add to upload table after users and sessions created
 * 
 * Stores a vue file on the server in a path defined by UploadDirectories::Vue
 * as well as its contents in the database table 'vue'
 * 
 */

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();

use Config\Constants\Upload_Directories as UploadDirectories;
use Config\Constants\File_Types as FileType;
use Lib\File_Uploads\File_Storage as FileStorage;
use Lib\File_Uploads\DB_Storage_Vue as VueStorage;

$upload = 'uploadedFile1';

if (isset($_POST['action']) && $_POST['action'] == 'uploadAction')
{
    $filename = $_FILES[$upload]['name'];
    $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Vue . $filename;

    FileStorage::Save(FileType::Vue, $_FILES[$upload], $uploadFilePath);
    $vue = new VueStorage($uploadFilePath, $filename);
    $vue->PDOStorage(FileType::Vue);
}
?>