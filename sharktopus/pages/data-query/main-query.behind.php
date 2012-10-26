<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Config\Constants\Query as Constants;
use Config\Constants\Urls as Urls;
use Lib\Data_Query\Query_Builder_Main as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;
use Lib\Error\Exception_Handler as ExceptionHandler;

if (isset($_GET['action-type']) && $_GET['action-type'] == 'validate')
{
    QueryBuilder::ValidateRequired($_GET); 
    $sqlCount = QueryBuilder::GenerateCountQuery($_GET);
    if (QueryProcess::GetCount($sqlCount) == 0)
       ExceptionHandler::Error404("No results found.");
}
elseif (isset($_GET['action-type']) && $_GET['action-type'] == 'download')
{
    $sql = QueryBuilder::GenerateQuery($_GET, true);
    QueryProcess::ExportCSV($sql);
}
elseif (isset($_GET['action-type']) && $_GET['action-type'] == 'query')
{
    if (!isset($_GET[Constants::Page]))
    {
        $sql = QueryBuilder::GenerateQuery($_GET);
        $sqlCount = QueryBuilder::GenerateCountQuery($_GET);
        $totalCount = QueryProcess::GetCount($sqlCount);
        if ($totalCount == 0)
            ExceptionHandler::Error404("No results found.");
    }
    else
    {
        $sql = QueryBuilder::GenerateQuery($_GET);
        $totalCount = $_GET[Constants::Count];
    }
    
    $isSensor = $_GET[Constants::InnerQueryType] == 'sensor' ? true : false;
    $sort = $_GET[Constants::SortBy];
    $order = $_GET[Constants::SortOrder];
    // uses limit of original query if not the first request
    $limit = $_GET[Constants::Limit];
    $page = isset($_GET[Constants::Page]) ? $_GET[Constants::Page] : 1;
    
    QueryProcess::GeneratePagination($page, $limit, $totalCount);
    QueryProcess::GenerateTable($sql, $limit, $page, $sort, $order, $isSensor);
    QueryProcess::GeneratePagination($page, $limit, $totalCount);
}
?>