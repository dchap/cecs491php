<?php
namespace Config\Database
{
    class Mysqli_Connection
    {
        private static $dbuser = "user";             //user name for the database
        private static $dbpass = "password"; 
        //private static $dbuser = "csulbsha_shark";             //user name for the database
        //private static $dbpass = "acoustictelemetry";      //the password to access the database
        private static $dbname = "csulbsha_sharktopus";             //the name of the database
        private static $dbhost = "localhost";
        //private static $dbhost = "www.csulbsharklab.com";

        private static $db = null;
        
        private function __construct()
        {
        }
        
        public static function GetMysqliInstance()
        {   
/*
            self::$db = mysql_connect($dbhost, $dbuser , $dbpass);
            if (!self::$db)
            {
                die('Could not connect: ' . mysql_error());
            }
*/
            
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