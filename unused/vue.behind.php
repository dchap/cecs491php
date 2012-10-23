<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();

/**
 * Stored with mysqli instead of pdo for performance 30% gain.
 * 
 * todo: delete stored entries if one part insert fails?
 */

use Config\Constants\Upload_Directories as UploadDirectories;
use Config\Constants\File_Types as FileType;
use Lib\File_Uploads\File_Storage as FileStorage;

$upload = 'uploadedFile1';

if (isset($_POST['action']))
{
    $filename = $_FILES[$upload]['name'];
    $uploadFilePath =  $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Vue . $filename;
    FileStorage::Save(FileType::Vue, $_FILES[$upload], $uploadFilePath);
    $db = \Config\Database\Mysqli_Connection::GetMysqliInstance();
    $insert = $insertInitial = "INSERT IGNORE INTO vue ( date, time, frequency_codespace, transmitter_id,
                                                        sensor_value, sensor_unit, receivers_id ) VALUES ";
    $data = array();
    $lineNum = 0;
    $affectedRows = 0;
    $timestart = time();
    if (($handle = fopen($uploadFilePath, 'r')) !== false) 
    {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) 
        {
            if ($lineNum > 0)
            {
                $date = $db->real_escape_string(date("Y-m-d", strtotime($data[0])));
                $time = $db->real_escape_string(date("H:i:s", strtotime($data[0])));
                $receiver = $db->real_escape_string($data[1]);
                $sensorValue = $db->real_escape_string($data[5]);
                $sensorUnit = $db->real_escape_string($data[6]);
                $splitTransmitter = explode("-", $data[2]);
                $codespace = $db->real_escape_string($splitTransmitter[0] . "-" . $splitTransmitter[1]);
                $transmitterId = $db->real_escape_string($splitTransmitter[2]);

                $insert .= "('$date', '$time', '$codespace', '$transmitterId', 
                '$sensorValue', '$sensorUnit', '$receiver'),";

                if ($lineNum % 5000 == 0)
                {
                    DBInsert();
                    $insert = $insertInitial;
                }
            }
            $lineNum++;
        }
         // Inserts the rest of the data
        if (strlen($insert) > strlen($insertInitial))
            DBInsert();
    }
    $timeend = time();
    echo "MYSQL<br />affected: $affectedRows, line: $lineNum, time:".date("i:s", $timeend - $timestart);
}

function DBInsert()
{
    global $insert, $db, $affectedRows;
    $insert = substr($insert, 0, -1);
    if ($db->query($insert))
        $affectedRows += $db->affected_rows;
    else
        \Lib\Error\Exception_Handler::Error404("Upload failed: Receiver(s) may have to be manually
            entered before uploading this file.<br />" . $db->error);
}
?>