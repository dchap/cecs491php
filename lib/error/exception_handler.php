<?php

namespace Lib\Error
{
    /**
     * Changes the HTTP status code and displays an error message
     */
    class Exception_Handler
    {
        public static function Error500($msg)
        {
            header('HTTP/1.1 500 Server Error');
            exit($msg);
        }
        
        public static function Error404($msg)
        {
            header('HTTP/1.1 404 Not Found');
            exit($msg);
        }
    }
}
?>
