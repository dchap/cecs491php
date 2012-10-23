<?php
/**
 * Stores a copy of the uploaded file on the server in a path defined by 
 * UploadDirectories class in addition to 
 * storing its contents in the corresponding database table
 */

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();

use Config\Constants\Upload_Directories as UploadDirectories;
use Config\Constants\File_Types as FileType;
use Config\Constants\Session_Variables as Session;
use Lib\File_Uploads\File_Storage as FileStorage;
use Lib\File_Uploads\DB_Storage_Meta as MetaStorage;
use Lib\File_Uploads\DB_Storage_Sonde as SondeStorage;
use Lib\File_Uploads\DB_Storage_Temp as TempStorage;
use Lib\File_Uploads\DB_Storage_Vue as VueStorage;
use Lib\File_Uploads\Uploads_Access as UploadsAccess;
session_start();
$upload = 'uploadedFile';

if (isset($_POST['file-type']))
{
    if ($_POST['file-type'] == strtolower(FileType::Metadata))
    {
        $filename = $_FILES[$upload]['name'];
        $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Metadata . $filename;

        FileStorage::Save(FileType::Metadata, $_FILES[$upload], $uploadFilePath);
        $meta = new MetaStorage($uploadFilePath, $filename);
        $entries = $meta->PDOStorage(FileType::Metadata);
        AddToUploadsTable(FileType::Metadata, $filename, $entries);
        exit("<p>Success - <strong>filename:</strong> $filename, <strong>type:</strong> " .  FileType::Metadata .  
                ", <strong>entries:</strong> $entries</p>");
    }
    elseif ($_POST['file-type'] == strtolower(FileType::Sonde))
    {
        $filename = $_FILES[$upload]['name'];
        $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Sonde . $filename;

        FileStorage::Save(FileType::Sonde, $_FILES[$upload], $uploadFilePath);
        $sonde = new SondeStorage($uploadFilePath, $filename);
        $entries = $sonde->PDOStorage(FileType::Sonde);
        AddToUploadsTable(FileType::Sonde, $filename, $entries);
        exit("<p>Success - <strong>filename:</strong> $filename, <strong>type:</strong> " .  FileType::Sonde .  
                ", <strong>entries:</strong> $entries</p>");
    }
    elseif ($_POST['file-type'] == strtolower(FileType::Temperature))
    {
        $filename = $_FILES[$upload]['name'];
        $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Temperatures. $filename;

        FileStorage::Save(FileType::Temperature, $_FILES[$upload], $uploadFilePath);
        $temp = new TempStorage($uploadFilePath, $filename);
        $entries = $temp->PDOStorage(FileType::Temperature);
        AddToUploadsTable(FileType::Temperature, $filename, $entries);
        exit("<p>Success - <strong>filename:</strong> $filename, <strong>type:</strong> " .  FileType::Temperature .  
                ", <strong>entries:</strong> $entries</p>");
    }
    elseif ($_POST['file-type'] == strtolower(FileType::Vue))
    {
        $filename = $_FILES[$upload]['name'];
        $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Vue . $filename;
        FileStorage::Save(FileType::Vue, $_FILES[$upload], $uploadFilePath);
        $entries = VueStorage::MysqlStorage($uploadFilePath);
        AddToUploadsTable(FileType::Vue, $filename, $entries);
        exit("<p>Success - <strong>filename:</strong> $filename, <strong>type:</strong> " .  FileType::Vue .  
                ", <strong>entries:</strong> $entries</p>");
    }
}

function AddToUploadsTable($filetype, $filename, $entries)
{
    $uploadsEntry = new UploadsAccess(array(
        'uploader' => $_SESSION[Session::Name],
        'filename' => $filename,
        'entries' => $entries,
        'file_type' => $filetype,
        'date' => date("Y-m-d"),
        'time' => date("H:i:s")
    ));
    UploadsAccess::Insert($uploadsEntry);
}
?>