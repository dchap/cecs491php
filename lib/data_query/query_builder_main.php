<?php
namespace Lib\Data_Query
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Constants\Query as QueryConstants;
    use Config\Database\Mysqli_Connection as MysqliConnect;
    
    class Query_Builder_Main
    {
        /**
         *
         * @param array $queryString $_GET array
         * @return string sql statement excluding order by, limit
         */
        public static function GenerateQuery(array $queryString, $export = false)
        { 
            self::ValidateRequired($queryString);
            $select = self::BuildSelectClause($queryString[QueryConstants::Fields], $export);
            $from = self::BuildFromClause($queryString[QueryConstants::OuterQueryType], $queryString[QueryConstants::InnerQueryType]);
            $where = self::BuildWhereClause($queryString);
            
            // sort
            $db = MysqliConnect::GetMysqliInstance();
            $sort = trim($db->real_escape_string($queryString[QueryConstants::SortBy]));
            $sort = "`$sort`";
            $order = trim($db->real_escape_string($queryString[QueryConstants::SortOrder]));
            
            $fullQuery = self::BuildFullQuery($select, $from, $where, $sort, $order);

            return $fullQuery;
        }
        
        /**
         *
         * @param array $queryString $_GET array
         * @return string sql statement for counting total rows
         */
        public static function GenerateCountQuery(array $queryString)
        {
            $select = "SELECT COUNT(*)";
            $from = self::BuildFromClause($queryString[QueryConstants::OuterQueryType], $queryString[QueryConstants::InnerQueryType]);
            $where = self::BuildWhereClause($queryString);
            $fullQuery = self::BuildFullQuery($select, $from, $where);
                        
            return $fullQuery;
        }
        
        protected static function BuildFullQuery($select, $from, $where, $sort = '', $order = '')
        {
            $orderClause = $sort == '' ? "" : " ORDER BY $sort $order";
            if (is_string($from))
            {
                $fullQuery = $select . $from . $where . $orderClause;
            }
            else
            {
                $fullQuery = $select . $from[0] . $where . " UNION ";
                $fullQuery .= $select . $from[1] . $where . $orderClause;
            }
            
            return $fullQuery;
        }
        
        protected static function BuildSelectClause(array $columns, $export)
        {
            $select = "SELECT ";
            if ($export)
            {
                foreach ($columns as $field)
                {
                    switch ($field)
                    {
                        case "v1":
                            $select .= "`vue`.`frequency_codespace` AS 'Frequency Codespace', ";
                            break;
                        case "v2":
                            $select .= "`vue`.`transmitter_id` AS 'Transmitter ID', ";
                            break;
                        case "v3":
                            $select .= "`vue`.`receivers_id` AS Receiver, ";
                            break;
                        case "v4":
                            $select .= "`vue`.`date` AS Date, ";
                            break;
                        case "v5":
                            $select .= "`vue`.`time` AS Time, ";
                            break;
                        case "v6":
                            $select .= "`vue`.`sensor_value` AS 'Sensor Value', ";
                            break;
                        case "v7":
                            $select .= "`vue`.`sensor_unit` AS 'Sensor Unit', ";
                            break;
                        case "f1":
                            $select .= "`fish`.`ascension` AS Ascension, ";
                            break;
                        case "f2":
                            $select .= "`fish`.`genus` AS Genus, ";
                            break;
                        case "f3":
                            $select .= "`fish`.`species` AS Species, ";
                            break;
                        case "s1":
                            $select .= "`stations_records`.`stations_name` AS 'Station Name', ";
                            break;
                        case "s2":
                            $select .= "`stations_records`.`latitude` AS Latitude, ";
                            break;
                        case "s3":
                            $select .= "`stations_records`.`longitude` AS Longitude, ";
                            break;
                    }
                }
            }
            else
            {
                foreach ($columns as $field)
                {
                    switch ($field)
                    {
                        case "v1":
                            $select .= "`vue`.`frequency_codespace`, ";
                            break;
                        case "v2":
                            $select .= "`vue`.`transmitter_id`, ";
                            break;
                        case "v3":
                            $select .= "`vue`.`receivers_id`, ";
                            break;
                        case "v4":
                            $select .= "`vue`.`date`, ";
                            break;
                        case "v5":
                            $select .= "`vue`.`time`, ";
                            break;
                        case "v6":
                            $select .= "`vue`.`sensor_value`, ";
                            break;
                        case "v7":
                            $select .= "`vue`.`sensor_unit`, ";
                            break;
                        case "f1":
                            $select .= "`fish`.`ascension`, ";
                            break;
                        case "f2":
                            $select .= "`fish`.`genus`, ";
                            break;
                        case "f3":
                            $select .= "`fish`.`species`, ";
                            break;
                        case "s1":
                            $select .= "`stations_records`.`stations_name`, ";
                            break;
                        case "s2":
                            $select .= "`stations_records`.`latitude`, ";
                            break;
                        case "s3":
                            $select .= "`stations_records`.`longitude`, ";
                            break;
                    }
                }
            }
            
            return substr($select, 0, -2);
        }
        
        protected static function BuildFromClause($outerType, $innerType)
        {
            $fromStatement;
            
            if ($outerType == QueryConstants::Fish)
            {
                if ($innerType == QueryConstants::Sensor)
                {
                    $fromStatement[] = " FROM `projects`
                        INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
                        INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
                        INNER JOIN `fish_sensors` ON `fish`.`id` = `fish_sensors`.`fish_id`
                        INNER JOIN `vue` ON `fish_sensors`.`sensor_codespace` = `vue`.`frequency_codespace`
                            AND `fish_sensors`.`sensor_id` = `vue`.`transmitter_id`
                        LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`";
                    $fromStatement[] = " FROM `projects`
                        INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
                        INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
                        INNER JOIN `vue` ON `fish`.`codespace` = `vue`.`frequency_codespace`
                            AND `fish`.`transmitter_id` = `vue`.`transmitter_id`
                        LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`";
                }
                else if ($innerType == QueryConstants::Transmitter)
                {
                    $fromStatement = " FROM `projects`
                        INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
                        INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
                        INNER JOIN `vue` ON `fish`.`codespace` = `vue`.`frequency_codespace`
                            AND `fish`.`transmitter_id` = `vue`.`transmitter_id`
                        LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`";
                }
            }
            else if ($outerType == QueryConstants::Station)
            {
                if ($innerType == QueryConstants::RecognizedFish)
                {
                    $fromStatement[] = " FROM `projects`
                        INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
                        INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
                        INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
                        INNER JOIN `fish` ON `fish`.`transmitter_id` = `vue`.`transmitter_id`
                            AND `fish`.`codespace` = `vue`.`frequency_codespace`";
                    $fromStatement[] = " FROM `projects`
                        INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
                        INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
                        INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
                        INNER JOIN `fish_sensors` ON `fish_sensors`.`sensor_codespace` = `vue`.`frequency_codespace`
                            AND `fish_sensors`.`sensor_id` = `vue`.`transmitter_id`
                        INNER JOIN `fish` ON `fish_sensors`.`fish_id` = `fish`.`id`";
                }
                else if ($innerType == QueryConstants::UnrecognizedFish)
                {
                    $fromStatement = " FROM `projects`
                        INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
                        INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
                        INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
                        LEFT JOIN `fish` ON `fish`.`transmitter_id` = `vue`.`transmitter_id`
                            AND `fish`.`codespace` = `vue`.`frequency_codespace`";
                }
            }
            
            return $fromStatement;
        }
        
        // check changes to date/time
        protected static function BuildWhereClause(array $query)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $dateStart = trim($db->real_escape_string($query[QueryConstants::DateStart]));
            $dateEnd = trim($db->real_escape_string($query[QueryConstants::DateEnd]));
            $timeStart = trim($db->real_escape_string($query[QueryConstants::TimeStart]));
            $timeEnd = trim($db->real_escape_string($query[QueryConstants::TimeEnd]));
            $project = trim($db->real_escape_string($query[QueryConstants::Project]));
            $station = trim($db->real_escape_string($query[QueryConstants::StationKey]));
            $receiver = trim($db->real_escape_string($query[QueryConstants::Receiver]));
            $transmitterStart = trim($db->real_escape_string($query[QueryConstants::TransmitterRangeStart]));
            $transmitterEnd = trim($db->real_escape_string($query[QueryConstants::TransmitterRangeEnd]));
            $genus = trim($db->real_escape_string($query[QueryConstants::Genus]));
            $species = trim($db->real_escape_string($query[QueryConstants::Species]));
            MysqliConnect::Disconnect();

            $where = '';
            $where .= " WHERE `projects`.`name` = " . "'$project'";
            $where .= " AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`)";
            if ($dateStart == '' && $dateEnd != '')
                $where .= " AND `vue`.`date` < '$dateEnd'";
            elseif ($dateStart != '' && $dateEnd == '')
                $where .= " AND `vue`.`date` > '$dateStart'";
            else
                $where .= " AND (`vue`.`date` BETWEEN '$dateStart' AND '$dateEnd')";
            
            if ($timeStart == '' && $timeEnd != '')
                $where .= " AND `vue`.`time` < '$timeEnd'";
            elseif ($timeStart != '' && $timeEnd == "")
                $where .= " AND `vue`.`time` > '$timeStart'";
            else
                $where .= " AND (`vue`.`time` BETWEEN '$timeStart' AND '$timeEnd')";
            
            if ($query[QueryConstants::OuterQueryType] == QueryConstants::Station
                    && $query[QueryConstants::InnerQueryType] == QueryConstants::UnrecognizedFish)
            {
                $where .= " AND `fish`.`transmitter_id` IS NULL
                            AND `vue`.`transmitter_id` NOT IN
                                (SELECT `fish_sensors`.`sensor_id` 
                                    FROM `fish_sensors`)
                            AND `vue`.`frequency_codespace` NOT IN
                                (SELECT `fish_sensors`.`sensor_codespace` 
                                    FROM `fish_sensors`)";
            }
            
            if ($query[QueryConstants::OuterQueryType] == QueryConstants::Fish
                    && $query[QueryConstants::InnerQueryType] == QueryConstants::Sensor)
            {
                $where .= " AND (`vue`.`sensor_unit` IS NOT NULL AND `vue`.`sensor_value` IS NOT NULL)";
            }
            
            if (!empty($station))
                $where .= " AND `stations_records`.`stations_name` = '$station'";
            
            if (!empty($receiver))
                $where .= " AND `vue`.`receivers_id` = '$receiver'";
            
            if (!empty($transmitterStart))
                $where .= " AND `vue`.`transmitter_id` >= $transmitterStart";
            
            if (!empty($transmitterEnd))
                $where .= " AND `vue`.`transmitter_id` <= $transmitterEnd";
            
            if (!empty($genus))
                $where .= " AND `fish`.`genus` = '$genus'";
            
            if (!empty($species))
                $where .= " AND `fish`.`species` = '$species'";
            
            return $where;
        }
        
        public static function ValidateRequired(array $query)
        {
            $errorList = '';
            if ($query[QueryConstants::Project] == '')
                $errorList .= "<p>A project must be selected</p>";
            if ($query[QueryConstants::DateStart] == '' && $query[QueryConstants::DateEnd] == '')
                $errorList .= "<p>At least one date field required</p>";
            if ($query[QueryConstants::TimeStart] == '' && $query[QueryConstants::TimeEnd] == '')
                $errorList .= "<p>At least one time field required</p>";
            
            if ($errorList != '')
                ExceptionHandler::Error404($errorList);
        }
    }
}
?>
