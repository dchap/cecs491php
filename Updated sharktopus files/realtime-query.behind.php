<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Config\Constants\Query as Constants;
use Lib\Data_Query\Query_Builder_Realtime as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;
use Lib\Error\Exception_Handler as ExceptionHandler;


if (isset($_GET['action-type']) && $_GET['action-type'] == 'query')
{    
    @session_start();   
    
    //if the page refreshed, store the previous query's count, else store 0
    $previousCount = isset($_SESSION[Constants::PreviousCount]) ? $_SESSION[Constants::PreviousCount] : 0;
    
    $sql = QueryBuilder::GenerateQuery();       
    $sqlCount = QueryBuilder::GenerateCountQuery();
    $totalCount = QueryProcess::GetCount($sqlCount);
    
    if ($totalCount == 0)
        ExceptionHandler::Error404("No results found."); 
        
    $sort = "date";
    $order = "desc";
    // uses limit of original query if not the first request
    $limit = 100;
    $page = isset($_GET[Constants::Page]) ? $_GET[Constants::Page] : 1;

    
    //If current count is > previous count, highlight new data
    if (($totalCount > $previousCount) && ($previousCount > 0)) { 
        echo '<h1>New data has just arrived!</h1>';
        
        $newDataCount = $totalCount - $previousCount;
        
        QueryProcess::GeneratePagination($page, $limit, $totalCount);
        QueryProcess::GenerateRealTimeTable($sql, $limit, $page, $sort, $order, $newDataCount);
    }
    else {
        QueryProcess::GeneratePagination($page, $limit, $totalCount);
        QueryProcess::GenerateTable($sql, $limit, $page, $sort, $order);
    }
    
    QueryProcess::GeneratePagination($page, $limit, $totalCount);
    
    //Store the current count in the session for future comparison
    $_SESSION[Constants::PreviousCount] = $totalCount;
}

?>

