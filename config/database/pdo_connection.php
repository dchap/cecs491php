<?php
namespace Config\Database
{
    class Pdo_Connection
    {
        private static $dbuser = "csulbsha_shark";             //user name for the database
        private static $dbpass = "acoustictelemetry";      //the password to access the database
        private static $dbname = "csulbsha_sharktopus";             //the name of the database
        private static $dbhost = "localhost";
        private static $db = null;
        
        private function __construct() { }
        private function __clone() { }
        
        public static function GetPDOInstance()
        {
            if (!self::$db)
            {
                self::$db = new \PDO("mysql:host=" . self::$dbhost . ";dbname=" . self::$dbname, self::$dbuser, self::$dbpass);
                self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
                self::$db->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
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