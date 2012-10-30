<?php
namespace Lib\Data_Query
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Constants\Query as QueryConstants;
    use Config\Database\Mysqli_Connection as MysqliConnect;
    
    class Query_Builder_Realtime
    {
        /**
         *
         * @param array $queryString $_GET array
         * @return string sql statement excluding order by, limit
         */
        public static function GenerateQuery(array $queryString, $export = false)
        { 
            // QueryBuilder::ValidateRequired is checking for emputy FrequencyCodespace and TransmitterID
            // we don't want Validate for realtime...
            // self::ValidateRequired($queryString);
//            $select = self::BuildSelectClause($queryString[QueryConstants::Fields], $export);
//            $from = self::BuildFromClause();
//            $where = self::BuildWhereClause($queryString);
            $select = "SELECT * ";
            $from = "FROM fish";
            $where = '';
            
            // sort
            $db = MysqliConnect::GetMysqliInstance();
            //$sort = trim($db->real_escape_string($queryString[QueryConstants::SortBy]));
            //$sort = "`$sort`";
            //$order = trim($db->real_escape_string($queryString[QueryConstants::SortOrder]));
            
            $fullQuery = self::BuildFullQuery($select, $from, $where, $sort, $order);
            //$fullQuery = self::BuildFullQuery($select, $from, $where);

            return $fullQuery;
        }
        
        /**
         *
         * @param array $queryString $_GET array
         * @return string sql statement for counting total rows
         */
        public static function GenerateCountQuery(array $queryString)
        {
            /*
            $select = "SELECT COUNT(*)";
            $from = self::BuildFromClause();
            $where = self::BuildWhereClause($queryString);
             */
            $select = "SELECT COUNT(*)";
            $from = "FROM fish";
            $where = '';
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
        
/*        
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
        
        protected static function BuildFromClause()
        {
            $fromStatement = " FROM `stations_records`
                INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`";

            return $fromStatement;
        }
        
        protected static function BuildWhereClause(array $query)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $dateStart = trim($db->real_escape_string($query[QueryConstants::DateStart]));
            $dateEnd = trim($db->real_escape_string($query[QueryConstants::DateEnd]));
            $timeStart = trim($db->real_escape_string($query[QueryConstants::TimeStart]));
            $timeEnd = trim($db->real_escape_string($query[QueryConstants::TimeEnd]));
            $frequencyCodespace = trim($db->real_escape_string($query[QueryConstants::FrequencyCodespace]));
            $transmitterID = trim($db->real_escape_string($query[QueryConstants::TransmitterID]));
            MysqliConnect::Disconnect();

            $where = '';
            $where .= " WHERE (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`)";
            if ($dateStart == '' && $dateEnd != '')
                $where .= " AND `vue`.`date` < '$dateEnd'";
            elseif ($dateStart != '' && $dateEnd == '')
                $where .= " AND `vue`.`date` > '$dateStart'";
            elseif ($dateStart != '' && $dateEnd != '')
                $where .= " AND (`vue`.`date` BETWEEN '$dateStart' AND '$dateEnd')";
            
            if ($timeStart == '' && $timeEnd != '')
                $where .= " AND `vue`.`time` < '$timeEnd'";
            elseif ($timeStart != '' && $timeEnd == "")
                $where .= " AND `vue`.`time` > '$timeStart'";
            elseif ($timeStart != '' && $timeEnd != '')
                $where .= " AND (`vue`.`time` BETWEEN '$timeStart' AND '$timeEnd')";
            
            $where .= " AND `vue`.`frequency_codespace` = '$frequencyCodespace'";
            $where .= " AND `vue`.`transmitter_id` = $transmitterID";
           
            return $where;
        }
*/
        
        
/*   we don't want this guy for realtime query...     
        public static function ValidateRequired(array $query)
        {
            $errorList = '';
            if ($query[QueryConstants::FrequencyCodespace] == '')
                $errorList .= "<p>Frequency codespace required</p>";
            if ($query[QueryConstants::TransmitterID] == '')
                $errorList .= "<p>Transmitter ID required</p>";
            
            if ($errorList != '')
                ExceptionHandler::Error404($errorList);
        }
 */
    }
}
?>
