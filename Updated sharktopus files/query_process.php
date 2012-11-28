<?php
namespace Lib\Data_Query
{
    use Config\Constants\Query as Constants;
    use Config\Database\Mysqli_Connection as MysqliConnect;

    class Query_Process
    {
        
        /**
         *
         * @param string $sql assumes query is properly escaped and will return results
         */
        public static function ExportCSV($sql)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $result = $db->query($sql);
            $fieldInfo = $result->fetch_fields();
            $headers = array();
            foreach ($fieldInfo as $field)
                $headers[] = $field->name;
            $fp = fopen('php://output', 'w');
            if ($fp && $result) 
            {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $headers);
                while ($row = $result->fetch_array(MYSQLI_NUM))
                    fputcsv($fp, array_values($row));
                exit;
            }
        }
        
        /**
         *
         * @param string $sql 
         */
        public static function GenerateTable($sql, $numRows, $page, $sort, $order, $isSensor = false)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $limit = trim($db->real_escape_string($numRows));
            $offset = $page > 1 ? (($page - 1) * $limit) : 0; 
            $sql .= " LIMIT $offset, $limit";

            $result = $db->query($sql);
            if (!$result)
                exit("No results found.");
            
            $column = 0;
            $table = "<table class='bordered-table zebra-striped'>\n<thead class='blue'>\n<tr>\n";
            $fieldInfo = $result->fetch_fields();
            
            foreach($fieldInfo as $field)
            {
                switch($field->orgname)
                {
                    case "frequency_codespace": 
                        $formattedName = "Codespace";
                        break;
                    case "transmitter_id":
                        $formattedName = $isSensor ? "Sensor ID" : "Transmitter ID";
                        break;
                    case "receivers_id":
                        $formattedName = "Receiver";
                        break;
                    case "sensor_value":
                        $formattedName = "Sensor Value";
                        break;
                    case "sensor_unit":
                        $formattedName = "Sensor Unit";
                        break;
                    case "stations_name":
                        $formattedName = "Station";
                        break;
                    default:
                        $formattedName = ucfirst($field->orgname);
                }
                if ($sort == $field->orgname)
                {
                    if ($order == 'asc')
                        $table .= "<th class='blue header headerSortDown'";
                    else
                        $table .= "<th class='blue header headerSortUp'";
                }
                else
                    $table .= "<th class='header'";
                $table .= " data-sort='$field->orgname'>$formattedName</th>\n";
                $column++;
            }
            $table .= "</tr>\n</thead>\n<tbody>\n";
            $color = "light";
            while ($row = $result->fetch_row())
            {
                $color = $color == "light" ? "dark" : "light";
                $table .= "<tr class=\"$color\">\n";
                for ($i = 0; $i < $result->field_count; $i++)
                    $table .= "<td>$row[$i]</td>\n";
                $table .= "</tr>\n";
            }
            $table .= "</tbody>\n</table>\n";
            
            MysqliConnect::Disconnect();
            echo $table;
        }
        
        
        public static function GenerateRealTimeTable($sql, $numRows, $page, $sort, $order, $newDataCount, $isSensor = false)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $limit = trim($db->real_escape_string($numRows));
            $offset = $page > 1 ? (($page - 1) * $limit) : 0; 
            $sql .= " LIMIT $offset, $limit";

            $result = $db->query($sql);
            if (!$result)
                exit("No results found.");
            
            $column = 0;
            $table = "<table class='bordered-table zebra-striped'>\n<thead class='blue'>\n<tr>\n";
            $fieldInfo = $result->fetch_fields();
            
            foreach($fieldInfo as $field)
            {
                //changes header name of column
                switch($field->orgname)
                {
                    case "frequency_codespace": 
                        $formattedName = "Codespace";
                        break;
                    case "transmitter_id":
                        $formattedName = $isSensor ? "Sensor ID" : "Transmitter ID";
                        break;
                    case "receivers_id":
                        $formattedName = "Receiver";
                        break;
                    case "sensor_value":
                        $formattedName = "Sensor Value";
                        break;
                    case "sensor_unit":
                        $formattedName = "Sensor Unit";
                        break;
                    case "stations_name":
                        $formattedName = "Station";
                        break;
                    default:
                        $formattedName = ucfirst($field->orgname);
                }
                if ($sort == $field->orgname)
                {
                    if ($order == 'asc')
                        $table .= "<th class='blue header headerSortDown'";
                    else
                        $table .= "<th class='blue header headerSortUp'";
                }
                else
                    $table .= "<th class='header'";
                $table .= " data-sort='$field->orgname'>$formattedName</th>\n";
                $column++;
            }
            $table .= "</tr>\n</thead>\n<tbody>\n";
            $color = "light";
            
            $rowCount = 0;
            while ($row = $result->fetch_row())
            {
                //if new data is in the table, color code the new row red
                if ($rowCount < $newDataCount)
                    $color = "red";
                else
                    $color = $color == "light" ? "dark" : "light";
                
                $rowCount++;
                
                $table .= "<tr class=\"$color\">\n";
                for ($i = 0; $i < $result->field_count; $i++)
                    $table .= "<td>$row[$i]</td>\n";
                $table .= "</tr>\n";
            }
            $table .= "</tbody>\n</table>\n";
            
            MysqliConnect::Disconnect();
            echo $table;
        }
        
        
        public static function GetCount($sql)
        {
            $count = 0;
            
            $db = MysqliConnect::GetMysqliInstance();
            $result = $db->query($sql);

            // separate count for each 'unioned' query
            while ($row = $result->fetch_row())
                $count += $row[0];
            
            MysqliConnect::Disconnect();
            return $count;
        }
        
        /**
         *
         * @param int $page current page number
         * @param int $limit maximum number results per page
         * @param int $totalCount total number of results
         * 
         * contains:
         * element: pagination-container [attr: data-query]
         * element: pagination-page [attr: href="page=#"]
         * element: span [attr: class=current]
         */
        public static function GeneratePagination($page, $limit, $totalCount)
        {
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($totalCount / $limit);
            $pageBeforeLast = $lastpage - 1;

            $adjacents = 3;

            $pagination = "";
            if($lastpage > 1)
            {	
                // stores query and limit
                // to be appended with page number parameter stored in paginated page link
                $pagination .= "<div class=\"pagination-summary\">$totalCount matches found.</div>";
                $pagination .= "<div class=\"pagination\" data-count=\"" . Constants::Count . "=$totalCount\">";
                //previous button
                if ($page > 1) 
                    $pagination.= "<ul><li><a class=\"prev pagination-page\" href=\"page=$prev\">« previous</a></li>";
                else
                    $pagination.= "<ul><li><a class=\"prev disabled\">« previous</a></li>";	

                //pages	
                if ($lastpage < 6 + ($adjacents * 2))	//not enough pages to bother breaking it up
                {	
                    for ($counter = 1; $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
                        else
                            $pagination.= "<li><a class=\"pagination-page\" href=\"page=$counter\">$counter</a></li>";					
                    }
                }
                else	//enough pages to hide some
                {
                    //close to beginning; only hide later pages
                    if ($page < 1 + ($adjacents * 2))		
                    {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<li><a class=\"active\">$counter</a></li>";
                            else
                                $pagination.= "<li><a class=\"pagination-page\" href=\"page=$counter\">$counter</a></li>";
                        }
                        $pagination.= "<li><a>...</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=$pageBeforeLast\">$pageBeforeLast</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=$lastpage\">$lastpage</a></li>";
                    }
                    //in middle; hide some front and some back
                    elseif ($page < $lastpage - ($adjacents * 2) && $page > ($adjacents * 2))
                    {
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=1\">1</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=2\">2</a></li>";
                        $pagination.= "<li><a>...</a></li>";
                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<li><a class=\"active\">$counter</a></li>";
                            else
                                $pagination.= "<li><a class=\"pagination-page\" href=\"page=$counter\">$counter</a></li>";
                        }
                        $pagination.= "<li><a>...</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=$pageBeforeLast\">$pageBeforeLast</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=$lastpage\">$lastpage</a></li>";		
                    }
                    //close to end; only hide early pages
                    else
                    {
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=1\">1</a></li>";
                        $pagination.= "<li><a class=\"pagination-page\" href=\"page=2\">2</a></li>";
                        $pagination.= "<li><a>...</a></li>";
                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<li><a class=\"active\">$counter</a></li>";
                            else
                                $pagination.= "<li><a class=\"pagination-page\" href=\"page=$counter\">$counter</a></li>";
                        }
                    }
                }

                //next button
                if ($page < $counter - 1) 
                        $pagination.= "<li><a class=\"next pagination-page\" href=\"page=$next\">next »</a></li></ul>";
                else
                        $pagination.= "<li><a class=\"next disabled\">next »</a></li></ul>";
                $pagination.= "</div>\n";		
            }
            
            if ($pagination != "")
                echo $pagination;
        }
        
        
        public static function UpdateEmail($updateEmailQuery)
        {
            echo $updateEmailQuery;
            $db = MysqliConnect::GetMysqliInstance();
            $result = $db->query($updateEmailQuery);           
            MysqliConnect::Disconnect();      
            
            if (!$result) {
                die('Mysql Error" '.mysql_error());
            }
        }
    }

}
?>