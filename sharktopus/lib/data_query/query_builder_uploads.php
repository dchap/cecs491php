<?php
namespace Lib\Data_Query
{
    use Config\Constants\Query as Constants;
    use Config\Database\Mysqli_Connection as MysqliConnect;
    /**
     * Description of uploaded_files
     */
    class Query_Builder_Uploads
    {
        public static function GenerateQuery(array $queryString)
        { 
            $db = MysqliConnect::GetMysqliInstance();

            $sql = "SELECT * FROM `uploads` ";
            $sql .= self::BuildWhereClause($queryString);
            $sql .= " ORDER BY date DESC";
            
            $page = isset($queryString[Constants::Page]) ? $queryString[Constants::Page] : 1;
            $rowsPerPage = trim($db->real_escape_string($queryString[Constants::Limit]));
            $offset = $page > 1 ? $rowsPerPage * ($page - 1) : 0;
            $sql .= " LIMIT $offset, $rowsPerPage";
            
            return $sql;
        }
        
        /**
         *
         * @param array $queryString $_GET array
         * @return string sql statement for counting total rows
         */
        public static function GenerateCountQuery(array $queryString)
        {
            $query = "SELECT COUNT(*) FROM `uploads` ";
            $query .= self::BuildWhereClause($queryString);
                        
            return $query;
        }
        
        protected static function BuildWhereClause(array $query)
        {
            $db = MysqliConnect::GetMysqliInstance();
            $member = trim($db->real_escape_string($query[Constants::Members]));
            if (!empty($member))
                return " WHERE uploader = '$member'";
            
            return "";
        }
    }

}
?>
