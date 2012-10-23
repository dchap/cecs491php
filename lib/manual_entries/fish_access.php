<?php
namespace Lib\Manual_Entries
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Database\Pdo_Connection as PdoConnect;
    
    /**
     * Queries take priority over other CRUD operations on this table, which
     * is the reason for the somewhat cumbersome design around sensors in particular
     */
    class Fish_Access extends Data_Access
    {
        public function __construct(array $data)
        {
            parent::__construct($data);
            foreach (array_keys($data) as $field)
                $this->$field = $field;
            $this->sensor_codespace1 = 'sensor_codespace1';
            $this->sensor_id1 = 'sensor_id1';
            $this->sensor_codespace2 = 'sensor_codespace2';
            $this->sensor_id2 = 'sensor_id2';
            $this->sensor_codespace3 = 'sensor_codespace3';
            $this->sensor_id3 = 'sensor_id3';
        }
        
        public $data = array(
            "id" => "",
            "codespace" => "",
            "transmitter_id" => "",
            "ascension" => "",
            "genus" => "",
            "species" => "",
            
            "sensor_codespace1" => "",
            "sensor_id1" => "",
            "sensor_codespace2" => "",
            "sensor_id2" => "",
            "sensor_codespace3" => "",
            "sensor_id3" => "",
            
            "date_deployed" => "",
            "time_deployed" => "",
            "sex" => "",
            "total_length" => "",
            "fork_length" => "",
            "standard_length" => "",
            "girth" => "",
            "weight" => "",
            "dart_tag" => "",
            "dart_color" => "",
            "landed_latitude" => "",
            "landed_longitude" => "",
            "released_latitude" => "",
            "released_longitude" => "",
            "time_out_of_water" => "",
            "time_in_tricane" => "",
            "time_in_surgery" => "",
            "recovery_time" => "",
            "landing_depth" => "",
            "release_depth" => "",
            "landing_temperature" => "",
            "release_temperature" => "",
            "fish_condition" => "",
            "release_method" => "",
            "photo_reference" => "",
            "comment" => ""
        );
        
        public $id, $codespace, $transmitter_id, $ascension, $genus, $species, $sensor_codespace1, $sensor_id1,
            $sensor_codespace2, $sensor_id2, $sensor_codespace3, $sensor_id3, $date_deployed, $time_deployed,
            $sex, $total_length, $fork_length, $standard_length, $girth, $weight, $dart_tag, $dart_color,
            $landed_latitude, $landed_longitude, $released_latitude, $released_longitude, $time_out_of_water,
            $time_in_tricane, $time_in_surgery, $recovery_time, $landing_depth, $release_depth, $landing_temperature, 
            $release_temperature, $fish_condition, $release_method, $photo_reference, $comment;

        /**
         *
         * @param Fish_Access $record to be inserted
         * @return Fish_Access the record inserted
         */
        public static function Insert(Fish_Access $record)
        {
            self::Validate($record);
            
            $sqlFish = "INSERT INTO fish 
                ( $record->codespace, $record->transmitter_id, $record->ascension, $record->genus, $record->species ) VALUES 
                ( :$record->codespace, :$record->transmitter_id, :$record->ascension, :$record->genus, :$record->species )";
            
            $sqlDetails = "INSERT INTO fish_details 
                ( fish_id, $record->date_deployed, $record->time_deployed, $record->sex, $record->total_length, 
                    $record->fork_length, $record->standard_length, $record->girth, $record->weight, $record->dart_tag,
                    $record->dart_color, $record->landed_latitude, $record->landed_longitude, $record->released_latitude,
                    $record->released_longitude, $record->time_out_of_water, $record->time_in_surgery, $record->time_in_tricane,
                    $record->recovery_time, $record->landing_depth, $record->release_depth, $record->landing_temperature,
                    $record->release_temperature, $record->fish_condition, $record->release_method, $record->photo_reference, 
                    $record->comment ) VALUES 
                ( :fish_id, :$record->date_deployed, :$record->time_deployed, :$record->sex, :$record->total_length, 
                    :$record->fork_length, :$record->standard_length, :$record->girth, :$record->weight, :$record->dart_tag,
                    :$record->dart_color, :$record->landed_latitude, :$record->landed_longitude, :$record->released_latitude,
                    :$record->released_longitude, :$record->time_out_of_water, :$record->time_in_surgery, :$record->time_in_tricane,
                    :$record->recovery_time, :$record->landing_depth, :$record->release_depth, :$record->landing_temperature,
                    :$record->release_temperature, :$record->fish_condition, :$record->release_method, :$record->photo_reference, 
                    :$record->comment )";
            
            $sqlSensors = "INSERT INTO fish_sensors
                ( fish_id, sensor_codespace, sensor_id ) VALUES
                ( :fish_id, :sensor_codespace, :sensor_id )";
            
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $conn->beginTransaction();
                $st = $conn->prepare($sqlFish);
                self::BindFish($st, $record);
                $st->execute();
                $lastInsertId = $conn->lastInsertId();
                
                $st = $conn->prepare($sqlDetails);
                self::BindDetails($st, $record, $lastInsertId);
                $st->execute();

                $st = $conn->prepare($sqlSensors);
                self::InsertSensors($st, $record, $lastInsertId);
                
                $conn->commit();
                
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                $conn->rollBack();
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Insert failed: " . $e->getMessage());
            }
            
            return self::GetFishById($lastInsertId);
        }
        
        /**
         *
         * @param Fish_Access $record to be updated
         * @return Fish_Access the record updated
         */
        public static function Update(Fish_Access $record)
        {
            self::Validate($record);
            
            $sqlFish = "UPDATE fish 
                SET $record->codespace = :$record->codespace, $record->transmitter_id = :$record->transmitter_id,
                $record->ascension = :$record->ascension, $record->genus = :$record->genus, $record->species = :$record->species
                WHERE id = :id";
            
            $sqlDetails = "UPDATE fish_details
                SET $record->date_deployed = :$record->date_deployed, $record->time_deployed = :$record->time_deployed, 
                    $record->sex = :$record->sex, $record->total_length = :$record->total_length, 
                    $record->fork_length = :$record->fork_length, $record->standard_length = :$record->standard_length,
                    $record->girth = :$record->girth, $record->weight = :$record->weight, 
                    $record->dart_tag = :$record->dart_tag, $record->dart_color = :$record->dart_color,
                    $record->landed_latitude = :$record->landed_latitude, $record->landed_longitude = :$record->landed_longitude,
                    $record->released_latitude = :$record->released_latitude,
                    $record->released_longitude = :$record->released_longitude, $record->time_out_of_water = :$record->time_out_of_water, 
                    $record->time_in_surgery = :$record->time_in_surgery, $record->time_in_tricane = :$record->time_in_tricane, 
                    $record->recovery_time = :$record->recovery_time, $record->landing_depth = :$record->landing_depth, 
                    $record->release_depth = :$record->release_depth, $record->landing_temperature = :$record->landing_temperature, 
                    $record->release_temperature = :$record->release_temperature, $record->fish_condition = :$record->fish_condition, 
                    $record->release_method = :$record->release_method, $record->photo_reference = :$record->photo_reference, 
                    $record->comment = :$record->comment
                WHERE fish_id = :fish_id";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $conn->beginTransaction();
                $st = $conn->prepare($sqlFish);
                self::BindFish($st, $record);
                $st->bindValue(":id", $record->data['id'], \PDO::PARAM_INT);
                $st->execute();
                
                $st = $conn->prepare($sqlDetails);
                self::BindDetails($st, $record, $record->data['id']);
                $st->execute();

                self::UpdateSensors($record);
                
                $conn->commit();
                PdoConnect::Disconnect();

                return self::GetFishById($record->data['id']);

            }
            catch (\PDOException $e)
            {
                $conn->rollBack();
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Update failed: " . $e->getMessage());
            }    
            
        }
        
        /**
         *
         * @param int $id id (primary key) of the row to be removed
         */
        public static function Delete($id)
        {
            $sql = "DELETE FROM fish WHERE id = :id LIMIT 1";
            
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
         * Unlike station records, up to three rows correspond to the same fish
         * because a fish may have three sensors, thus preventing this class's
         * constructor from initializing values per row.
         * 
         * Instead, data is initialized for every unique fish while the fish's sensors
         * are grouped separately.  Then the fish's sensor values are updated.
         *
         * @return array[ array[Fish_Access] ][ total count ]
         */
        public static function GetAllFishRecords($project, $startRow, $numRows)//, $sortBy, $sortOrder)
        {
//            $db = MysqliConnect::GetMysqliInstance();
//            $sortBy = strtolower(trim($db->real_escape_string($sortBy)));
//            $sortOrder = strtolower(trim($db->real_escape_string($sortOrder)));
            $fishRecords = $rows = array();
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                if (empty($project))
                {
                    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM fish 
                        INNER JOIN fish_details ON fish.id = fish_details.fish_id
                        LEFT JOIN fish_sensors ON fish.id = fish_sensors.fish_id 
                        ORDER BY `fish`.`ascension`
                        LIMIT :offset, :count";
                    $st = $conn->prepare($sql);
                    $st->bindValue(":offset", $startRow, \PDO::PARAM_INT);
                    $st->bindValue(":count", $numRows, \PDO::PARAM_INT);
                }
                else
                {
                    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM projects
                        INNER JOIN projects_fish ON projects.name = projects_fish.projects_name
                        INNER JOIN fish ON projects_fish.fish_id = fish.id
                        INNER JOIN fish_details ON fish.id = fish_details.fish_id
                        LEFT JOIN fish_sensors ON fish.id = fish_sensors.fish_id 
                        WHERE projects.name = :project
                        ORDER BY `fish`.`ascension`
                        LIMIT :offset, :count";
                    $st = $conn->prepare($sql);
                    $st->bindValue(":offset", $startRow, \PDO::PARAM_INT);
                    $st->bindValue(":count", $numRows, \PDO::PARAM_INT);
                    $st->bindValue(":project", $project, \PDO::PARAM_STR);
                }
                $st->execute();
                $rows = $st->fetchAll();
                $st = $conn->query("SELECT found_rows() AS totalRows");
                $countRow = $st->fetch();
                $count = $countRow['totalRows'];
                PdoConnect::Disconnect();
                
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Query failed: " . $e->getMessage());
            }

            // 3D array with all sensors and ids mapped to each fish id
            $sensorsWithIds = array();
            for ($i = 0; $i < count($rows); $i += 1 + $skip)
            {
                $skip = 0;
                $sensorsWithIds[$rows[$i]['id']] = array('sensors' => array($rows[$i]['sensor_codespace']),
                                                        'ids' => array($rows[$i]['sensor_id']));
                for ($j = 1; $j < 3; $j++)
                {
                    // if next row is the same fish, add sensor info to sensor array
                    if (isset($rows[$i + $j]) && $rows[$i]['id'] == $rows[$i + $j]['id'])
                    {
                        $outerKey = $rows[$i]['id'];
                        $sensorsWithIds[$outerKey]['sensors'][]  = $rows[$i + $j]['sensor_codespace'];
                        $sensorsWithIds[$outerKey]['ids'][]  = $rows[$i + $j]['sensor_id'];
                        // skip rows of the same fish because only the sensor values are unique
                        $skip++;
                    }

                }
                $fishRecords[] = new Fish_Access($rows[$i]);
            }

            foreach ($fishRecords as $fish)
            {
                $index = $fish->data['id'];
                for ($i = 1; $i < count($sensorsWithIds[$index]['sensors']) + 1; $i++)
                {
                    $fish->data["sensor_codespace$i"] = $sensorsWithIds[$index]['sensors'][$i - 1];
                    $fish->data["sensor_id$i"] = $sensorsWithIds[$index]['ids'][$i - 1];
                }
            }

            return array($fishRecords, $count);
        }
        
        public static function AscensionUnique($ascension)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT * FROM `fish` WHERE `ascension` = :ascension";
                $st = $conn->prepare($sql);
                $st->bindValue(":ascension", $ascension, \PDO::PARAM_STR);
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
        
        public static function XmitterUnique($codespace, $transmitter)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT * FROM `fish` 
                    WHERE `codespace` = :codespace AND `transmitter_id` = :transmitter_id";
                $st = $conn->prepare($sql);
                $st->bindValue(":codespace", $codespace, \PDO::PARAM_STR);
                $st->bindValue(":transmitter_id", $transmitter, \PDO::PARAM_INT);
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
        
        private static function UpdateSensors(Fish_Access &$record)
        {

            $sqlDelete = "DELETE FROM fish_sensors 
                WHERE fish_id = :fish_id AND sensor_codespace = :sensor_codespace
                AND sensor_id = :sensor_id";
            
            $sqlInsert = "INSERT INTO fish_sensors
                ( fish_id, sensor_codespace, sensor_id ) VALUES
                ( :fish_id, :sensor_codespace, :sensor_id )";
            
            $fishId = $record->data['id'];
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT sensor_codespace, sensor_id FROM fish_sensors WHERE fish_id = $fishId");
            $st->execute();
            $rows = $st->fetchAll(\PDO::FETCH_ASSOC); 
            
            $combinedDBSensors = array();
            $combinedFormSensors = array();
            
            if (count($rows) > 0)
            {
                for ($i = 0; $i < count($rows); $i++)
                    $combinedDBSensors[$i] = implode('-', $rows[$i]);
            }
            
            for ($i = 1; $i < 4; $i++)
            {
                // validation ensures sensor id will have a value when codespace does
                if (trim($record->data["sensor_codespace$i"]) !== '')
                    $combinedFormSensors[$i] = $record->data["sensor_codespace$i"] . "-" . $record->data["sensor_id$i"];
            }
            
            // rows coming from the database will be set if stored in $combinedDBSensors
            for ($i = 0; $i < count($combinedDBSensors); $i++)
            {
                if (!in_array($combinedDBSensors[$i], $combinedFormSensors))
                    self::InsertOrDeleteSensor($sqlDelete, $fishId, $rows[$i]["sensor_codespace"], $rows[$i]["sensor_id"]);
            }
            
            for ($i = 1; $i < 4; $i++)
            {
                if (isset($combinedFormSensors[$i]) && !in_array($combinedFormSensors[$i], $combinedDBSensors))
                    self::InsertOrDeleteSensor($sqlInsert, $fishId, $record->data["sensor_codespace$i"], $record->data["sensor_id$i"]);
            }
        }
        
        /**
         * @param type $id
         * @return Fish_Access 
         */
        private static function GetFishById($id)
        {
            try
            {
                // will return, at most, as many rows as there are sensors
                // and at least one if the fish has none
                $sql = "SELECT * FROM fish 
                    INNER JOIN fish_details ON fish.id = fish_details.fish_id
                    LEFT JOIN fish_sensors ON fish.id = fish_sensors.fish_id
                    WHERE id = :id";
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                $rows = array();
                $rows = $st->fetchAll();
                
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Could not retrieve last entry:" . $e->getMessage());
            }
            
            // populate data array with all row values except for sensors
            $fish = new Fish_Access($rows[0]);
            // fill in sensor values if the fish has any
            if (count($rows) > 0)
            {
                for ($i = 1; $i < count($rows) + 1; $i++)
                {
                    $fish->data["sensor_codespace$i"] = $rows[$i - 1]["sensor_codespace"];
                    $fish->data["sensor_id$i"] = $rows[$i - 1]["sensor_id"];
                }
            }
            
            return $fish;
        }
        
        private static function BindFish(&$st, Fish_Access &$record)
        {
            $st->bindValue(":$record->codespace", $record->data[$record->codespace], \PDO::PARAM_STR);
            $st->bindValue(":$record->transmitter_id", $record->data[$record->transmitter_id], \PDO::PARAM_INT);
            $st->bindValue(":$record->ascension", $record->data[$record->ascension], \PDO::PARAM_STR);
            $st->bindValue(":$record->genus", $record->data[$record->genus], \PDO::PARAM_STR);
            $st->bindValue(":$record->species", $record->data[$record->species], \PDO::PARAM_STR);
        }
        
        private static function BindDetails(&$st, Fish_Access &$record, $id)
        {                
            $st->bindValue(":fish_id", $id, \PDO::PARAM_INT);
            $st->bindValue(":$record->date_deployed", $record->data[$record->date_deployed], \PDO::PARAM_STR);
            $st->bindValue(":$record->time_deployed", $record->data[$record->time_deployed], \PDO::PARAM_STR);
            $st->bindValue(":$record->sex", $record->data[$record->sex], \PDO::PARAM_STR);
            $st->bindValue(":$record->total_length", $record->data[$record->total_length], \PDO::PARAM_INT);
            $st->bindValue(":$record->release_method", $record->data[$record->release_method], \PDO::PARAM_STR);
            $st->bindValue(":$record->standard_length", $record->data[$record->standard_length], \PDO::PARAM_INT);
            $st->bindValue(":$record->dart_tag", $record->data[$record->dart_tag], \PDO::PARAM_STR);
            $st->bindValue(":$record->dart_color", $record->data[$record->dart_color], \PDO::PARAM_STR);
            $st->bindValue(":$record->landed_latitude", $record->data[$record->landed_latitude], \PDO::PARAM_STR);
            $st->bindValue(":$record->landed_longitude", $record->data[$record->landed_longitude], \PDO::PARAM_STR);
            $st->bindValue(":$record->released_latitude", $record->data[$record->released_latitude], \PDO::PARAM_STR);
            $st->bindValue(":$record->released_longitude", $record->data[$record->released_longitude], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->fork_length]) == '')
                $st->bindValue(":$record->fork_length", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->fork_length", $record->data[$record->fork_length], \PDO::PARAM_INT);
            
            if (trim($record->data[$record->girth]) == '')
                $st->bindValue(":$record->girth", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->girth", $record->data[$record->girth], \PDO::PARAM_INT);
            
            if (trim($record->data[$record->weight]) == '')
                $st->bindValue(":$record->weight", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->weight", $record->data[$record->weight], \PDO::PARAM_INT);
            
            if (trim($record->data[$record->time_out_of_water]) == '')
                $st->bindValue(":$record->time_out_of_water", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->time_out_of_water", $record->data[$record->time_out_of_water], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->time_in_tricane]) == '')
                $st->bindValue(":$record->time_in_tricane", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->time_in_tricane", $record->data[$record->time_in_tricane], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->time_in_surgery]) == '')
                $st->bindValue(":$record->time_in_surgery", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->time_in_surgery", $record->data[$record->time_in_surgery], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->recovery_time]) == '')
                $st->bindValue(":$record->recovery_time", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->recovery_time", $record->data[$record->recovery_time], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->landing_depth]) == '')
                $st->bindValue(":$record->landing_depth", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->landing_depth", $record->data[$record->landing_depth], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->release_depth]) == '')
                $st->bindValue(":$record->release_depth", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->release_depth", $record->data[$record->release_depth], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->landing_temperature]) == '')
                $st->bindValue(":$record->landing_temperature", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->landing_temperature", $record->data[$record->landing_temperature], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->release_temperature]) == '')
                $st->bindValue(":$record->release_temperature", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->release_temperature", $record->data[$record->release_temperature], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->fish_condition]) == '')
                $st->bindValue(":$record->fish_condition", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->fish_condition", $record->data[$record->fish_condition], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->photo_reference]) == '')
                $st->bindValue(":$record->photo_reference", null, \PDO::PARAM_NULL);
            else
                $st->bindValue(":$record->photo_reference", $record->data[$record->photo_reference], \PDO::PARAM_STR);
            
            if (trim($record->data[$record->comment]) == '')
                $st->bindValue(":$record->comment", null, \PDO::PARAM_NULL);            
            else
                $st->bindValue(":$record->comment", $record->data[$record->comment], \PDO::PARAM_STR);            
        }
        
        private static function InsertSensors(&$st, Fish_Access $record, $lastInsertId)
        {   
            for ($i = 1; $i < 4; $i++)
            {
                $data1 = $record->data["sensor_codespace$i"];
                $data2 = $record->data["sensor_id$i"];
                if (trim($data1) !== '')
                    $st->execute(array(':fish_id' => $lastInsertId, ':sensor_codespace' => $data1, ':sensor_id' => $data2));
            }
        }
        
        private static function InsertOrDeleteSensor($sql, $fishId, $sensorCodespace, $sensorId)
        {
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare($sql);
            $st->bindValue(":fish_id", $fishId, \PDO::PARAM_INT);
            $st->bindValue(":sensor_codespace", $sensorCodespace, \PDO::PARAM_STR);
            $st->bindValue(":sensor_id", $sensorId, \PDO::PARAM_INT);
            $st->execute();
        }
        
        private static function Validate(Fish_Access $record)
        {
            foreach ($record->data as $field => $value)
            {
                if ($field != $record->sensor_codespace1  && $field != $record->sensor_id1 
                        && $field != $record->sensor_codespace2 && $field != $record->sensor_id2 
                        && $field != $record->sensor_codespace3 && $field != $record->sensor_id3
                        && $field != $record->photo_reference && $field != $record->comment
                        && $field != $record->id && $field != $record->fork_length
                        && $field != $record->girth && $field != $record->weight
                        && $field != $record->time_out_of_water && $field != $record->time_in_tricane
                        && $field != $record->time_in_surgery && $field != $record->recovery_time
                        && $field != $record->landing_depth && $field != $record->release_depth
                        && $field != $record->landing_temperature && $field != $record->release_temperature
                        && $field != $record->fish_condition && (!isset($value) || trim($value) === ''))
                {
                    ExceptionHandler::Error404 ("One or more fields must have a value");
                }
                
                if ($field == $record->sensor_codespace1 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_id1]) || trim($record->data[$record->sensor_id1]) === '')
                        ExceptionHandler::Error404 ("A sensor with a codespace must have its id entered.");
                    
                if ($field == $record->sensor_id1 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_codespace1]) || trim($record->data[$record->sensor_codespace1]) === '')
                        ExceptionHandler::Error404 ("A sensor with an id must have its codespace entered.");
                    
                if ($field == $record->sensor_codespace2 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_id2]) || trim($record->data[$record->sensor_id2]) === '')
                        ExceptionHandler::Error404 ("A sensor with a codespace must have its id entered.");
                    
                if ($field == $record->sensor_id2 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_codespace2]) || trim($record->data[$record->sensor_codespace2]) === '')
                        ExceptionHandler::Error404 ("A sensor with an id must have its codespace entered.");
                    
                if ($field == $record->sensor_codespace3 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_id3]) || trim($record->data[$record->sensor_id3]) === '')
                        ExceptionHandler::Error404 ("A sensor with a codespace must have its id entered.");
                    
                if ($field == $record->sensor_id3 && isset($value) && trim($value) !== '')
                    if (!isset($record->data[$record->sensor_codespace3]) || trim($record->data[$record->sensor_codespace3]) === '')
                        ExceptionHandler::Error404 ("A sensor with an id must have its codespace entered.");
            }
            
            //add sensor validation check (no sensor values equal to each other)
        }
    }
}
?>
