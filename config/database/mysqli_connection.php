<?php
namespace Config\Database
{
    class Mysqli_Connection
    {
        private static $dbuser = "csulbsha_shark";             //user name for the database
        private static $dbpass = "acoustictelemetry";      //the password to access the database
        //private static $dbpass = "YES"; 
        private static $dbname = "csulbsha_sharktopus";             //the name of the database
        private static $dbhost = "localhost";

        private static $db = null;
        
        private function __construct()
        {
        }
        
        public static function GetMysqliInstance()
        {
            if (!(self::$db instanceof \mysqli))
            {
                self::$db = new \mysqli(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname)
                    or die("Could not connect to database: " . $db->connect_error);
            }
            
            return self::$db;
        }
        
        public static function Disconnect()
        {
            self::$db = null;
        }
    }
}
?>