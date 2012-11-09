<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Data_Query\Query_Builder_Realtime as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;


if (isset($_GET['email']) && isset($_GET['email-preference']))
{    
    @session_start();   
 
    $updateSql = QueryBuilder::GenerateEmailQuery($_GET['email'], $_GET['email-preference']);       
    QueryProcess::UpdateEmail($updateSql);
}

?>
