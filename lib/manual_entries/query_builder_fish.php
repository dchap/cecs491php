<?php
namespace Lib\Manual_Entries
{
    use Config\Database\Mysqli_Connection as MysqliConnect;

    class Query_Builder_Fish
    {
        public static function GenerateQuery($project)
        {
            if (empty($project))
            {
                return "SELECT * FROM fish 
                            INNER JOIN fish_details ON fish.id = fish_details.fish_id
                            LEFT JOIN fish_sensors ON fish.id = fish_sensors.fish_id";
            }
            
            $db = MysqliConnect::GetMysqliInstance();
            $project = trim($db->real_escape_string($project));
            return "SELECT * FROM projects
                        INNER JOIN projects_fish ON projects.name = projects_fish.projects_name
                        INNER JOIN fish ON projects_fish.fish_id = fish.id
                        INNER JOIN fish_details ON fish.id = fish_details.fish_id
                        LEFT JOIN fish_sensors ON fish.id = fish_sensors.fish_id
                        WHERE `projects`.`name` = '$project'";
        }
    }
}
?>
