<?php

namespace Lib\Manual_Entries
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Database\Mysqli_Connection as MysqliConnect;
    use \Config\Database\Pdo_Connection as PdoConnect;
    
    /**
     * For stations, projects, receivers
     */
    class Singular_Entries_Access 
    {
        /**
         *
         * @param string $table to insert on
         * @param string $value to be inserted
         * @return string trimmed $value parameter
         */
        public static function Insert($table, $value)
        {
            if (empty($table) || empty($value))
                ExceptionHandler::Error404 ("Values cannot be empty");
            
            $db = MysqliConnect::GetMysqliInstance();
            $table = strtolower(trim($db->real_escape_string($table)));
            $escapedValue = trim($db->real_escape_string($value));
            
            if ($db->query("INSERT INTO $table VALUES ( '$escapedValue' )"))
            {
                MysqliConnect::Disconnect();
                return trim($value);
            }
            else
            {
                ExceptionHandler::Error500("Insert failed: $db->error");
            }
        }
        
        public static function Update($table, $field, $oldValue, $newValue)
        {
            if (empty($table) || empty($field) || empty($oldValue) || empty($newValue))
                ExceptionHandler::Error404 ("Values cannot be empty");
            
            $db = MysqliConnect::GetMysqliInstance();
            $table = strtolower(trim($db->real_escape_string($table)));
            $field = trim($db->real_escape_string($field));
            $oldValue = trim($db->real_escape_string($oldValue));
            $escapedNewValue = trim($db->real_escape_string($newValue));
            
            if ($result = $db->query("UPDATE $table SET $field = '$escapedNewValue' WHERE $field = '$oldValue'"))
            {
                MysqliConnect::Disconnect();
                return trim($newValue);
            }
            else
            {
                ExceptionHandler::Error500("Update failed: $db->error");
            }
        }
        
        public static function Delete($table, $field, $value)
        {
            if (empty($table) || empty($field) || empty($value))
                ExceptionHandler::Error404 ("Values cannot be empty");
            $db = MysqliConnect::GetMysqliInstance();
            $table = strtolower(trim($db->real_escape_string($table)));
            $field = trim($db->real_escape_string($field));
            $value = trim($db->real_escape_string($value));
            $sql = "DELETE FROM $table WHERE $field = '$value'";
            
            if ($result = $db->query("DELETE FROM $table WHERE $field = '$value'"))
            {
                MysqliConnect::Disconnect();
                return;
            }
            else
            {
                ExceptionHandler::Error500("Delete failed: $db->error");
            }
        }
        
        public static function GetAllEntries($table)
        {
            if (empty($table))
                ExceptionHandler::Error404 ("Table name empty");
            
            $db = MysqliConnect::GetMysqliInstance();
            $table = strtolower(trim($db->real_escape_string($table)));
            $entries = array();
            if ($result = $db->query("SELECT * FROM $table"))
            {
                while ($row = $result->fetch_array(\MYSQLI_NUM))
                    $entries[] = $row[0];
            }
            else
            {
                ExceptionHandler::Error500("Query failed: $db->error");
            }

            return $entries;
        }
        
        public static function GetAllEntriesFish($column)
        {
            if (empty($column))
                ExceptionHandler::Error404 ("Column name empty");
            
            $db = MysqliConnect::GetMysqliInstance();
            $column = strtolower(trim($db->real_escape_string($column)));
            $entries = array();
            if ($result = $db->query("SELECT DISTINCT $column FROM fish"))
            {
                while ($row = $result->fetch_array(\MYSQLI_NUM))
                    $entries[] = $row[0];
            }
            else
            {
                ExceptionHandler::Error500("Query failed: $db->error");
            }

            return $entries;
        }
        
        /**
         * @param string $station
         * @return array affected counts of [temp, sonde, stationrecord]
         */
        public static function GetStationSideEffects($station)
        {
            if (empty ($station))
                ExceptionHandler::Error404 ("Station name cannot be empty");
            
            return array(
                self::GetAffectedTemperature($station),
                self::GetAffectedSonde($station),
                self::GetAffectedStationRecordsStation($station)
            );
        }
        
        private static function GetAffectedTemperature($station)
        {
            if (empty ($station))
                ExceptionHandler::Error404 ("Station name cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `stations` 
                INNER JOIN `temperatures` ON `stations_name` = `name` 
                WHERE `name` = :name");
            $st->bindValue(":name", $station, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        private static function GetAffectedSonde($station)
        {
            if (empty ($station))
                ExceptionHandler::Error404 ("Station name cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `stations` 
                INNER JOIN `sonde` ON `stations_name` = `name` 
                WHERE `name` = :name");
            $st->bindValue(":name", $station, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        private static function GetAffectedStationRecordsStation($station)
        {
            if (empty ($station))
                ExceptionHandler::Error404 ("Station name cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `stations` 
                INNER JOIN `stations_records` ON `stations_name` = `name` 
                WHERE `name` = :name");
            $st->bindValue(":name", $station, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        /**
         * @param string $receiver
         * @return array affected counts of [metadata, vue, stationrecord]
         */
        public static function GetReceiverSideEffects($receiver)
        {
            if (empty ($receiver))
                ExceptionHandler::Error404 ("Receiver cannot be empty");
            
            return array(
                self::GetAffectedMetadata($receiver),
                self::GetAffectedVue($receiver),
                self::GetAffectedStationRecordsReceiver($receiver)
            );
        }
        
        private static function GetAffectedStationRecordsReceiver($receiver)
        {
            if (empty ($receiver))
                ExceptionHandler::Error404 ("Receiver cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `receivers` 
                INNER JOIN `stations_records` ON `receivers_id` = `receivers`.`id` 
                WHERE `receivers_id` = :receivers_id");
            $st->bindValue(":receivers_id", $receiver, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        private static function GetAffectedMetadata($receiver)
        {
            if (empty ($receiver))
                ExceptionHandler::Error404 ("Receiver cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `receivers` 
                INNER JOIN `metadata` ON `receivers_id` = `receivers`.`id` 
                WHERE `receivers_id` = :receivers_id");
            $st->bindValue(":receivers_id", $receiver, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        private static function GetAffectedVue($receiver)
        {
            if (empty ($receiver))
                ExceptionHandler::Error404 ("Receiver cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `receivers` 
                INNER JOIN `vue` ON `receivers_id` = `receivers`.`id` 
                WHERE `receivers_id` = :receivers_id");
            $st->bindValue(":receivers_id", $receiver, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        public static function GetProjectSideEffects($project)
        {
            if (empty ($project))
                ExceptionHandler::Error404 ("Project cannot be empty");
            
            return array(self::GetAffectedProjectsFish($project),
                self::GetAffectedProjectsStations($project));
            
        }
        
        private static function GetAffectedProjectsFish($project)
        {
            if (empty ($project))
                ExceptionHandler::Error404 ("Project cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `projects` 
                INNER JOIN `projects_fish` ON `name` = `projects_name`
                WHERE `projects_name` = :name");
            $st->bindValue(":name", $project, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
        
        private static function GetAffectedProjectsStations($project)
        {
            if (empty ($project))
                ExceptionHandler::Error404 ("Project cannot be empty");
            
            $conn = PdoConnect::GetPDOInstance();
            $st = $conn->prepare("SELECT COUNT(*) FROM `stations` 
                INNER JOIN `projects_stations` ON `name` = `projects_name` 
                WHERE `projects_name` = :name");
            $st->bindValue(":name", $project, \PDO::PARAM_STR);
            $st->execute();
            $row = $st->fetch(\PDO::FETCH_NUM);
            return $row[0];
        }
    }
}
?>
