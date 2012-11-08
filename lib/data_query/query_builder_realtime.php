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
            $fullQuery = self::BuildFullQuery($select, $from, $where);

            return $fullQuery;
        }
        
        public static function GenerateCountQuery()
        {
            $select = "SELECT COUNT(*) ";
           
            //$select = "SELECT `stations_records`.`stations_name`, `vue`.`date`, `vue`.`time`, `vue`.`transmitter_id`, `fish`.`genus`, `fish`.`species` ";
            $from = self::BuildFromClause();
            $where = self::BuildWhereClause();
            $fullQuery = self::BuildFullQuery($select, $from, $where);            
            
            return $fullQuery;
        }
                       
        
        protected static function BuildFromClause()
        {
            $fromStatement = "FROM `vue` ";
            
            $fromStatement .= "LEFT JOIN `fish` on `vue`.`transmitter_id` = `fish`.`transmitter_id` ";
            $fromStatement .= "LEFT JOIN `stations_records` on `vue`.`receivers_id` = `stations_records`.`receivers_id` ";
            $fromStatement .= "LEFT JOIN `projects_stations` on `stations_records`.`stations_name` = `projects_stations`.`stations_name` ";

            $fromStatement .= "LEFT JOIN `projects_members` ON `projects_stations`.`projects_name` = `projects_members`.`projects_name` ";
            $fromStatement .= "LEFT JOIN `members` ON `projects_members`.`members_id` = `members`.`id` ";
//            $fromStatement .= "INNER JOIN `fish` on `vue`.`transmitter_id` = `fish`.`transmitter_id` ";
//            $fromStatement .= "INNER JOIN `stations_records` on `vue`.`receivers_id` = `stations_records`.`receivers_id` ";
//            $fromStatement .= "INNER JOIN `projects_stations` on `stations_records`.`stations_name` = `projects_stations`.`stations_name` ";
           
            return $fromStatement;
        }
        
        
        protected static function BuildWhereClause()
        {
            date_default_timezone_set('America/Los_Angeles');
            date_default_timezone_get();
            date('H:i:s');
            //$dateTime = new DateTime("now", new DateTimeZone($timeLocation));
            //echo $dateTime->format("Y-m-d H:i:s");
            
            $startDateTimestamp = time() - QueryConstants::Weeks * QueryConstants::Days * QueryConstants::Hours
                          * QueryConstants::Minutes * QueryConstants::Seconds;            
            $endDateTimestamp = time();
            
            // Replace '/' with '-' in dates
            $dateStart = str_replace('/', '-', date('Y/m/d', $startDateTimestamp));
            $dateEnd = str_replace('/', '-', date('Y/m/d', $endDateTimestamp));            
           
            
            $db = MysqliConnect::GetMysqliInstance();
            $dateStart = trim($db->real_escape_string($dateStart));
            $dateEnd = trim($db->real_escape_string($dateEnd));
            MysqliConnect::Disconnect();
            //$where =" WHERE (`members`.`username` = `'admin'` AND (`vue`.`date BETWEEN` `'2010-8-1'` AND `'2012-12-31'`) ";
            //$where .= " WHERE (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`)";
            $where .= " WHERE (`vue`.`date` BETWEEN '$dateStart' AND '$dateEnd')";
//            $where .= " WHERE `vue`.`date` BETWEEN '2012-7-24' AND '2012-11-06'"; // 15 weeks
            
            return $where;
        }
        
        
        protected static function BuildFullQuery($select, $from, $where)
        {
            $orderClause = "ORDER BY `vue`.`date` DESC, `vue`.`time` DESC";
            //$orderClause = $sort == '' ? "" : " ORDER BY $sort $order";
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
