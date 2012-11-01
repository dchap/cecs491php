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
        public static function GenerateQuery()
        {             
            $select = "SELECT `stations_records`.`stations_name`, `vue`.`date`, `vue`.`time`, `vue`.`transmitter_id`, `fish`.`genus`, `fish`.`species` ";
            
            $from = self::BuildFromClause();
            $where = self::BuildWhereClause();
            
            $db = MysqliConnect::GetMysqliInstance();
            $sort = "'date'";
            $order = "asc";
            
            $fullQuery = self::BuildFullQuery($select, $from, $where, $sort, $order);

            return $fullQuery;
        }
                       
        
        protected static function BuildFromClause()
        {
            $fromStatement = "FROM `projects_fish` INNER JOIN `fish` on `projects_fish`.`fish_id` = `fish`.`id` ";
            $fromStatement .= "INNER JOIN `projects` on `projects_fish`.`projects_name` = `projects`.`name` ";
            $fromStatement .= "INNER JOIN `projects_stations` on `projects`.`name` = `projects_stations`.`projects_name` ";
            $fromStatement .= "INNER JOIN `stations` on `projects_stations`.`stations_name` = `stations`.`name` ";
            $fromStatement .= "INNER JOIN `stations_records` on `stations`.`name` = `stations_records`.`stations_name` ";
            $fromStatement .= "INNER JOIN `receivers` on `stations_records`.`receivers_id` = `receivers`.`id` ";
            $fromStatement .= "INNER JOIN `vue` on `receivers`.`id` = `vue`.`receivers_id` ";
            
            return $fromStatement;
        }
        
        
        protected static function BuildWhereClause()
        {
            date_default_timezone_get();
            $startDateTimestamp = time() - QueryConstants::Weeks * QueryConstants::Days * QueryConstants::Hours
                          * QueryConstants::Minutes * QueryConstants::Seconds;            
            $endDateTimestamp = time();
            
            //Replace '/' with '-' in dates
            $dateStart = str_replace('/', '-', date('Y/m/d', $startDateTimestamp));
            $dateEnd = str_replace('/', '-', date('Y/m/d', $endDateTimestamp));            
           
            
            $db = MysqliConnect::GetMysqliInstance();
            $dateStart = trim($db->real_escape_string($dateStart));
            $dateEnd = trim($db->real_escape_string($dateEnd));
            MysqliConnect::Disconnect();
            
            $where = " WHERE (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`)";
            if ($dateStart == '' && $dateEnd != '')
                $where .= " AND `vue`.`date` < $dateEnd";
            elseif ($dateStart != '' && $dateEnd == '')
                $where .= " AND `vue`.`date` > $dateStart";
            elseif ($dateStart != '' && $dateEnd != '')
                $where .= " AND (`vue`.`date` BETWEEN '$dateStart' AND '$dateEnd')";
            
//            var_dump($where);
            return $where;
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
            
//            echo $fullQuery;           
            return $fullQuery;
        }         
    }
}
?>
