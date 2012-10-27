<?php
namespace Lib\Manual_Entries
{
    use Config\Database\Mysqli_Connection as MysqliConnect;

    class Query_Builder_Stations_Records
    {
        public static function GenerateQuery($project)
        {
            if (empty($project))
                return "SELECT * FROM `stations_records`";
            
            $db = MysqliConnect::GetMysqliInstance();
            $project = trim($db->real_escape_string($project));

            return "SELECT * FROM `projects`
                    INNER JOIN `projects_stations` ON `projects_stations`.`projects_name` = `projects`.`name`
                    INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
                    WHERE `projects`.`name` = '$project'";
        }
    }
}
?>
