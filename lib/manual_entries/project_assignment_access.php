<?php

namespace Lib\Manual_Entries
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Lib\Manual_Entries\Singular_Entries_Access as SingularEntries;
    use Config\Database\Pdo_Connection as PdoConnect;
    /**
     * Description of project_assignment_access
     */
    class Project_Assignment_Access 
    {
        // Stations
        public static function AddStation($station, $project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "INSERT INTO projects_stations ( projects_name, stations_name )
                    VALUES ( :projects_name, :stations_name )";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->bindValue(":stations_name", $station, \PDO::PARAM_STR);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Insert failed: " . $e->getMessage());
            }
        }
        
        public static function RemoveStation($station, $project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "DELETE FROM projects_stations 
                    WHERE projects_name = :projects_name AND stations_name = :stations_name";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->bindValue(":stations_name", $station, \PDO::PARAM_STR);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
        }
        
        public static function GetAllStations()
        {
            return SingularEntries::GetAllEntries("stations");
        }
        
        public static function GetAllAssignedStations($project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT stations_name from projects_stations
                    WHERE projects_name = :projects_name 
                    ORDER BY `stations_name`";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->execute();
                $rows = array();
                while ($row = $st->fetch(\PDO::FETCH_NUM))
                    $rows[] = $row[0];
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
            
            return $rows;
        }
        
        // Fish
        public static function AddFish($fishId, $project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "INSERT INTO projects_fish ( projects_name, fish_id )
                    VALUES ( :projects_name, :fish_id )";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->bindValue(":fish_id", $fishId, \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Insert failed: " . $e->getMessage());
            }
        }
        
        public static function RemoveFish($fishId, $project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "DELETE FROM projects_fish 
                    WHERE projects_name = :projects_name AND fish_id = :fish_id";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->bindValue(":fish_id", $fishId, \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
        }
        
        public static function GetAllFishRecordsAscension()
        {
            $records = array();
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare("SELECT id, ascension FROM fish ORDER BY ascension");
                $st->execute();
                while ($row = $st->fetch())
                    $records[$row['id']] = $row['ascension'];
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Query failed: " . $e->getMessage());
            }
            
            return $records;
        }
        
        public static function GetAllAssignedFish($project)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT fish_id, ascension 
                    FROM projects_fish INNER JOIN fish ON fish_id = id
                    WHERE projects_name = :projects_name 
                    ORDER BY `ascension`";
                $st = $conn->prepare($sql);
                $st->bindValue(":projects_name", $project, \PDO::PARAM_STR);
                $st->execute();
                $records = array();
                while ($row = $st->fetch())
                    $records[$row['fish_id']] = $row['ascension'];
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
            
            return $records;
        }
    }
}
?>
