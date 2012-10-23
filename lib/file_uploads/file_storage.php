<?php
namespace Lib\File_Uploads
{
    use \Lib\Error\Exception_Handler as ExceptionHandler;
    use Lib\File_Uploads\Uploads_Access as UploadsAccess;
    
    /**
     * Stores physical copy of the file on the server
     */
    class File_Storage 
    {   
        /**
         * @param  $filetype  must be a prefix of filename defined in Constants class
         * @param  $_FILES['file']  uploaded file array
         * @param  $uploadedFilePath  full path of filename to be saved
         */
        public static function Save($filetype, $file, $uploadedFilePath)
        {
            if (empty($file) || empty($uploadedFilePath))
                ExceptionHandler::Error500("<p>Invalid arguments</p>");
            
            // Error check
            self::CheckFileNameFormat($file['name']);
            self::CheckUploadErrors($file['error']);
            
            if (UploadsAccess::FilenameExists($file['name'], $filetype) != false)
            {
                ExceptionHandler::Error404("<p>Filename already exists. 
                    Please rename the file or delete the existing one before uploading it again.</p>");
            }
            
            if (!move_uploaded_file($file['tmp_name'], $uploadedFilePath)) 
            {   
                ExceptionHandler::Error500("<p>Could not move file to destination directory</p>");
            }
        }
        
        public static function Delete($filepath)
        {
            while(is_file($filepath) == true)
            {
                chmod($filepath, 0777);
                unlink($filepath);
                return true;
            }
            
            ExceptionHandler::Error500("<p>Could not remove file</p>");
        }
        
        private static function CheckFileNameFormat($fileName)
        {
            if (substr($fileName, -4) != '.csv')
                ExceptionHandler::Error404("<p>Not a .csv file</p>");
        }
        
        private static function CheckUploadErrors($errorNo)
        {
            switch ($errorNo) 
            {
                case 0:
                    break;
                case 1:
                    ExceptionHandler::Error500("<p>File exceeded upload_max_filesize</p>");
                case 2: 
                    ExceptionHandler::Error500("<p>File exceeded max_file_size</p>");
                case 3: 
                    ExceptionHandler::Error500("<p>File only partially uploaded</p>");
                case 4: 
                    ExceptionHandler::Error500("<p>No file uploaded</p>");
                case 6: 
                    ExceptionHandler::Error500("<p>Cannot upload file: No temp directory specified</p>");
                case 7: 
                    ExceptionHandler::Error500("<p>Upload failed: Cannot write to disk</p>");
                default:
                    break;
            }
        }
    }
}
?>
