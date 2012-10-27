<?php
namespace Lib\File_Uploads
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Database\Pdo_Connection as PdoConnect;
    use Config\Database\Mysqli_Connection as MysqliConnect;
    use Lib\Data_Query\Query_Builder_Uploads as QueryBuilder;
    use Lib\File_Uploads\File_Storage as FileStorage;
    use Config\Constants\Upload_Directories as UploadDirectories;
    use Config\Constants\File_Types as FileTypes;
    use Lib\File_Uploads\DB_Storage_Meta as MetaStorage;
    use Lib\File_Uploads\DB_Storage_Sonde as SondeStorage;
    use Lib\File_Uploads\DB_Storage_Vue as VueStorage;
    use Lib\File_Uploads\DB_Storage_Temp as TempStorage;
    
    /**
     * Description of Members_Access
     */
    class Uploads_Access extends \Lib\Manual_Entries\Data_Access
    {
        public function __construct(array $data)
        {
            parent::__construct($data);
            foreach (array_keys($data) as $field)
                $this->$field = $field;
        }
        
        protected $data = array(
            "id" => "",
            "uploader" => "",
            "filename" => "",
            "file_type" => "",
            "entries" => "",
            "date" => "",
            "time" => "",
        );
        
        public  $id, $uploader, $filename, $file_type, $date, $time, $entries;
        
        /**
         *
         * @param type $filename
         * @param type $filetype
         * @return false if NO match found, array if match found
         */
        public static function FilenameExists($filename, $filetype)
        {
            if (empty($filename) || empty($filetype))
                ExceptionHandler::Error404 ("<p>Null arguments</p>");
            
            $sql = "SELECT * FROM `uploads` WHERE filename = :filename AND file_type = :file_type";
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":filename", $filename, \PDO::PARAM_STR);
                $st->bindValue(":file_type", $filetype, \PDO::PARAM_STR);
                $st->execute();
                $row = $st->fetch(\PDO::FETCH_NUM);
                PdoConnect::Disconnect();
                return $row;
            }
            catch(\PDOException $e)
            {
                ExceptionHandler::Error404($e->getMessage());
            }
        }

        public static function Insert(Uploads_Access $record)
        {
            if (empty($record))
                ExceptionHandler::Error404("<p>Null entry</p>");
                 
            foreach ($record->data as $field => $value)
            {
                if ($field != 'id' && (!isset($value) || trim($value) === ''))
                    ExceptionHandler::Error404 ("<p>Uploads table entry failed: one or more fields null</p>");
            }
            
            $sql = "INSERT INTO uploads
                ( $record->uploader, $record->filename, $record->file_type, 
                    $record->date, $record->time, $record->entries) 
                VALUES 
                ( :$record->uploader, :$record->filename, :$record->file_type, 
                    :$record->date, :$record->time, :$record->entries)";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":$record->uploader", $record->data[$record->uploader], \PDO::PARAM_STR);
                $st->bindValue(":$record->filename", $record->data[$record->filename], \PDO::PARAM_STR);
                $st->bindValue(":$record->file_type", $record->data[$record->file_type], \PDO::PARAM_STR);
                $st->bindValue(":$record->date", $record->data[$record->date], \PDO::PARAM_STR);
                $st->bindValue(":$record->time", $record->data[$record->time], \PDO::PARAM_STR);
                $st->bindValue(":$record->entries", $record->data[$record->entries], \PDO::PARAM_INT);
                $st->execute();
                $lastInsertId = $conn->lastInsertId();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("<p>Insert failed: " . $e->getMessage() . "</p>");
            }
            
            return self::GetUploadedFileById($lastInsertId);
        }
        
        /**
         *
         * @param int $id id (primary key) of the row to be removed
         */
        public static function Delete($filename, $filetype)
        {
            if (empty($filename) || empty($filetype))
                ExceptionHandler::Error500("<p>Null arguments</p>");
            $filepath = ''; 
            
            switch ($filetype)
            {
                case FileTypes::Metadata :
                    $filepath = $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Metadata . "$filename";
                    $entries = new MetaStorage($filepath, $filetype);
                    $entries->PDODelete();
                    break;
                case FileTypes::Sonde :
                    $filepath = $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Sonde . "$filename";
                    $entries = new SondeStorage($filepath, $filetype);
                    $entries->PDODelete();
                    break;
                case FileTypes::Vue:
                    $filepath = $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Vue . "$filename";
                    $entries = new VueStorage($filepath, $filetype);
                    $entries->MysqlDelete($filepath);
                    break;
                case FileTypes::Temperature :
                    $filepath = $_SERVER['DOCUMENT_ROOT'] . UploadDirectories::Temperatures . "$filename";
                    $entries = new TempStorage($filepath, $filetype);
                    $entries->PDODelete();
                    break;
            }
            FileStorage::Delete($filepath);
            
            $sql = "DELETE FROM `uploads` WHERE filename = :filename AND file_type = :file_type LIMIT 1";
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":filename", $filename, \PDO::PARAM_STR);
                $st->bindValue(":file_type", $filetype, \PDO::PARAM_STR);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("<p>Delete failed: " . $e->getMessage() . "</p>");
            }
        }
        
        /**
         * @param string sql statement
         * @return array upload entries
         */
        public static function GetUploadsLimited($sql)
        {
            try 
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->query( $sql );
                $records = array();
                // every row is a dictionary with table field names as keys
                foreach ($st->fetchAll() as $row)
                    $records[] = new Uploads_Access($row);
                PdoConnect::Disconnect();
                
                return $records;
            }
            catch(\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("<p>Query failed: " . $e->getMessage() . "</p>");
            }
        }
        
        private static function GetUploadedFileById($id)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare("SELECT * FROM uploads WHERE id = :id");
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                $lastEntry = $st->fetch();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("<p>Could not retrieve last entry:" . $e->getMessage() . "</p>");
            }
            
            return new Uploads_Access($lastEntry);
        }
    }
}
?>
