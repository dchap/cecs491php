<?php
namespace Lib\Manual_Entries
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Database\Pdo_Connection as PdoConnect;
    
    /**
     * Description of station_records
     */
    class Stations_Records_Access extends Data_Access
    {
        public function __construct(array $data)
        {
            parent::__construct($data);
            foreach (array_keys($data) as $field)
                $this->$field = $field;
        }
        
        protected $data = array(
            "id" => "",
            "stations_name" => "",
            "receivers_id" => "",
            "release_value" => "",
            "hobo" => "",
            "frequency_codespace" => "",
            "sync_tag" => "",
            "latitude" => "",
            "longitude" => "",
            "secondary_latitude" => "",
            "secondary_longitude" => "",
            "secondary_waypoint" => "",
            "depth" => "",
            "receiver_height" => "",
            "date_in" => "",
            "time_in" => "",
            "date_out" => "",
            "time_out" => "",
            "date_downloaded" => "",
            "comment" => ""
        );

        public  $id, $stations_name, $receivers_id, $release_value, $hobo, $frequency_codespace, $sync_tag, 
                $latitude, $longitude, $secondary_latitude, $secondary_longitude, $secondary_waypoint,
                $depth, $receiver_height, $date_in, $time_in, $date_out, $time_out, $date_downloaded, $comment;

        /**
         *
         * @param Stations_Records_Access $record to be inserted
         * @return Stations_Records_Access the record inserted
         */
        public static function Insert(Stations_Records_Access $record)
        {
            self::Validate($record);
            $sql = "INSERT INTO stations_records 
                ( $record->stations_name, $record->receivers_id, $record->hobo, $record->frequency_codespace, 
                $record->release_value, $record->sync_tag, $record->latitude, $record->longitude, 
                $record->secondary_latitude, $record->secondary_longitude, $record->secondary_waypoint, 
                $record->depth, $record->receiver_height, $record->date_in, $record->time_in, 
                $record->date_out, $record->time_out, $record->date_downloaded, $record->comment,
                $record->id ) 
                VALUES 
                ( :$record->stations_name, :$record->receivers_id, :$record->hobo, :$record->frequency_codespace, 
                :$record->release_value, :$record->sync_tag, :$record->latitude, :$record->longitude, 
                :$record->secondary_latitude, :$record->secondary_longitude, :$record->secondary_waypoint, 
                :$record->depth, :$record->receiver_height, :$record->date_in, :$record->time_in, 
                :$record->date_out, :$record->time_out, :$record->date_downloaded, :$record->comment,
                :$record->id )";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                self::Bind($st, $record);
                $st->execute();
                $lastInsertId = $conn->lastInsertId();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Insert failed: " . $e->getMessage());
            }
            
            return self::GetStationRecordById($lastInsertId);
        }
        
        /**
         *
         * @param Stations_Records_Access $record to be updated
         * @return Stations_Records_Access the record updated
         */
        public static function Update(Stations_Records_Access $record)
        {
            self::Validate($record);
            $sql = "UPDATE stations_records 
                SET $record->stations_name = :$record->stations_name, $record->receivers_id = :$record->receivers_id,
                $record->release_value = :$record->release_value, $record->hobo = :$record->hobo, 
                $record->frequency_codespace = :$record->frequency_codespace, $record->sync_tag = :$record->sync_tag, 
                $record->latitude = :$record->latitude, $record->longitude = :$record->longitude, 
                $record->secondary_latitude = :$record->secondary_latitude, 
                $record->secondary_longitude = :$record->secondary_longitude, 
                $record->secondary_waypoint = :$record->secondary_waypoint, $record->depth = :$record->depth, 
                $record->receiver_height = :$record->receiver_height, $record->date_in = :$record->date_in, 
                $record->time_in = :$record->time_in, $record->date_out = :$record->date_out, 
                $record->time_out = :$record->time_out, $record->date_downloaded = :$record->date_downloaded, 
                $record->comment = :$record->comment 
                WHERE id = :id";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                self::Bind($st, $record);
                //$st->bindValue(":id", $record->data['id'], \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Update failed: " . $e->getMessage());
            }    
            
            return self::GetStationRecordById($record->data['id']);
        }
        
        /**
         *
         * @param int $id id (primary key) of the row to be removed
         */
        public static function Delete($id)
        {
            $sql = "DELETE FROM stations_records WHERE id = :id LIMIT 1";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
        }
        
        /**
         *
         * @return array[ array[Stations_Records_Access] ][ total count ]
         */
        public static function GetAllStationRecords($project, $startRow, $numRows)//order
        {
            
            $records = array();

            try 
            {
                $conn = PdoConnect::GetPDOInstance();
                if (empty($project))
                {
                    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM stations_records 
                    ORDER BY `stations_name` 
                    LIMIT :startRow, :numRows";
                    $st = $conn->prepare($sql);
                    $st->bindValue(":startRow", $startRow, \PDO::PARAM_INT);
                    $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
                }
                else
                {
                    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM projects
                    INNER JOIN `projects_stations` ON `projects_stations`.`projects_name` = `projects`.`name`
                    INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
                    WHERE `projects`.`name` = :project
                    ORDER BY `stations_records`.`stations_name` 
                    LIMIT :startRow, :numRows";
                    $st = $conn->prepare($sql);
                    $st->bindValue(":startRow", $startRow, \PDO::PARAM_INT);
                    $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
                    $st->bindValue(":project", $project, \PDO::PARAM_STR);
                }
                $st->execute();
                // every row is a dictionary with table field names as keys
                foreach ($st->fetchAll() as $row)
                    $records[] = new Stations_Records_Access($row);
                
                $st = $conn->query("SELECT found_rows() as totalRows");
                $row = $st->fetch();
                PdoConnect::Disconnect();
                
                return array($records, $row["totalRows"]);
            }
            catch(\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Query failed: " . $e->getMessage());
            }
        }
        
        /**
         *
         * @param string $station
         * @param string $dateIn
         * @param string $timeIn
         * @return bool(false) if no record found, array otherwise
         */
        public static function StationRecordIsUnique($station, $dateIn, $timeIn)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT * FROM `stations_records` 
                    WHERE `stations_name` = :stations_name AND `date_in` = :date_in
                    AND `time_in` = :time_in";
                $st = $conn->prepare($sql);
                $st->bindValue(":stations_name", $station, \PDO::PARAM_STR);
                $st->bindValue(":date_in", $dateIn, \PDO::PARAM_STR);
                $st->bindValue(":time_in", $timeIn, \PDO::PARAM_STR);
                $st->execute();
                $record = $st->fetch(\PDO::FETCH_ASSOC);
                PdoConnect::Disconnect();
                return $record === false;
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error404("There was an error processing the request");
            }
        }
        
        /**
         * @param type $id
         * @return Stations_Records_Access 
         */
        private static function GetStationRecordById($id)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare("SELECT * FROM stations_records WHERE id = :id");
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                $lastEntry = $st->fetch();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Could not retrieve last entry:" . $e->getMessage());
            }
            
            return new Stations_Records_Access($lastEntry);
        }
        
        private static function Bind(&$st, Stations_Records_Access &$record)
        {
            foreach ($record->data as $field => $value)
            {
                if (trim($value) == '')
                    $st->bindValue(":$field", null, \PDO::PARAM_NULL);
                else
                    $st->bindValue(":$field", $value); //defaults to string
            }
        }
        
        private static function Validate(Stations_Records_Access $record)
        {
            foreach ($record->data as $field => $value)
            {
                if ($field != 'id' && $field != $record->comment
                    && $field != $record->release_value && $field != $record->hobo
                    && $field != $record->frequency_codespace && $field != $record->sync_tag
                    && $field != $record->secondary_latitude && $field != $record->secondary_longitude
                    && $field != $record->secondary_waypoint && $field != $record->depth
                    && $field != $record->receiver_height && $field != $record->date_out
                    && $field != $record->time_out && $field != $record->date_downloaded
                    && (!isset($value) || trim($value) ===''))
                {
                    ExceptionHandler::Error404 ("One or more inputs must have a value");
                }
            }
        }
    }
}
?>
