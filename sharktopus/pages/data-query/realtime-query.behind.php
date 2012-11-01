<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Config\Constants\Query as Constants;
use Config\Constants\Urls as Urls;
use Lib\Data_Query\Query_Builder_Realtime as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;
use Lib\Error\Exception_Handler as ExceptionHandler;


if (isset($_GET['action-type']) && $_GET['action-type'] == 'validate')
{ 
//    $sqlCount = QueryBuilder::GenerateCountQuery();
//    if (QueryProcess::GetCount($sqlCount) == 0)
//       ExceptionHandler::Error404("No results found.");
}
elseif (isset($_GET['action-type']) && $_GET['action-type'] == 'query')
{
    if (!isset($_GET[Constants::Page]))
    {
        $sql = QueryBuilder::GenerateQuery();
        
        //var_dump($sql);
//        $b = mysql_query($sql);
//        
//        $totalCount = mysql_num_rows($b);
//        if ($totalCount == 0 )
//            totalCount == 0; 
//        else
//            $totalCount = QueryProcess::GetCount($sql);
//        
//        if ($totalCount == 0)
//            ExceptionHandler::Error404("No results found.");
    }
    else
    {
        $sql = QueryBuilder::GenerateQuery();
        $totalCount = $_GET[Constants::Count];
    }
    $totalCount = 100;
    
    $sort = $_GET[Constants::SortBy];
    $order = $_GET[Constants::SortOrder];
    // uses limit of original query if not the first request
    $limit = $_GET[Constants::Limit];
    $page = isset($_GET[Constants::Page]) ? $_GET[Constants::Page] : 1;
    
    QueryProcess::GeneratePagination($page, $limit, $totalCount);
    QueryProcess::GenerateTable($sql, $limit, $page, $sort, $order);
    QueryProcess::GeneratePagination($page, $limit, $totalCount);
}

?>

