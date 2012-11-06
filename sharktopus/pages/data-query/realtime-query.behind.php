<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Config\Constants\Query as Constants;
use Config\Constants\Urls as Urls;
use Lib\Data_Query\Query_Builder_Realtime as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;
use Lib\Error\Exception_Handler as ExceptionHandler;


if (isset($_GET['action-type']) && $_GET['action-type'] == 'query')
{
    $sql = QueryBuilder::GenerateQuery();

    $sqlCount = QueryBuilder::GenerateCountQuery();
    $totalCount = QueryProcess::GetCount($sqlCount);
    if ($totalCount == 0)
        ExceptionHandler::Error404("No results found.");       

    
    //$totalCount = 100;
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

