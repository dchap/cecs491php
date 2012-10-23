<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Data_Query\Query_Builder_Uploads as QueryBuilder;
use Lib\File_Uploads\Uploads_Access as UploadsAccess;
use Lib\Data_Query\Query_Process as QueryProcess;
use Config\Database\Mysqli_Connection as MysqliConnect;
use Config\Constants\Query as Constants;
use Lib\Error\Exception_Handler as ExceptionHandler;
use Config\Constants\Session_Variables as Session;
session_start();

if (isset($_GET['action-type']) && $_GET['action-type'] == 'query')
{
    $sql = QueryBuilder::GenerateQuery($_GET);
    $sqlCount = QueryBuilder::GenerateCountQuery($_GET);
    $totalCount = QueryProcess::GetCount($sqlCount);
    if ($totalCount == 0)
    {
        BeginTable();
        EndTableNoResults();
        exit;
    }
    
    $entries = UploadsAccess::GetUploadsLimited($sql);

    $page = isset($_GET[Constants::Page]) ? $_GET[Constants::Page] : 1;
    $rowsPerPage = $_GET[Constants::Limit];
    $offset = $page > 1 ? $rowsPerPage * ($page - 1) : 0;
    
    QueryProcess::GeneratePagination($page, $rowsPerPage, $totalCount);

    BeginTable();
    foreach ($entries as $record)
    {
        echo "<tr data-id='" . $record->getValueEncoded('id') . "'>\n";
        if ($_SESSION[Session::AccountType] >= Session::Admin)
            echo "<td><a href='javascript:void(0);' class='delete'>Delete</a></td>";
        echo "<td class='$record->uploader'>" . $record->getValueEncoded($record->uploader) . "</td>";
        echo "<td class='$record->filename'>" . $record->getValueEncoded($record->filename) . "</td>";
        echo "<td class='$record->file_type'>" . ucfirst($record->getValueEncoded($record->file_type)) . "</td>";
        echo "<td class='$record->entries'>" . $record->getValueEncoded($record->entries) . "</td>";
        echo "<td class='$record->date'>" . $record->getValueEncoded($record->date) . "</td>";
        echo "<td class='$record->time'>" . $record->getValueEncoded($record->time) . "</td>";
        echo "\n</tr>";
    }
    EndTable();
    
    QueryProcess::GeneratePagination($page, $rowsPerPage, $totalCount);  
    
}
elseif (isset($_POST['action-type']))
{
    UploadsAccess::Delete($_POST['filename'], $_POST['filetype']);
}

function BeginTable()
{
?>

<table class="zebra-striped bordered-table">
    <thead class="blue">
        <tr>
            <?php if ($_SESSION[Session::AccountType] >= Session::Admin) echo "<td></td>"; ?>
            <td>Uploader</td>
            <td>Filename</td>
            <td>Filetype</td>
            <td>Entries Stored From File</td>
            <td class="header blue headerSortUp">Date</td>
            <td>Time</td>
        </tr>
    </thead>
    <tbody>
<?php
}

function EndTable()
{
?>
    </tbody>
</table>
<?php
}

function EndTableNoResults()
{
?>
    </tbody>
</table>
<div id="no-records">
    <p>No records found.</p>
</div>
<?php
}
?>