<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Data_Query\Query_Process as QueryProcess;
use Lib\Manual_Entries\Stations_Records_Access as StationsRecordsAccess;
use Config\Constants\Query as QueryConstants;

// load initial records
//if (isset($_GET["loadTable"]))
//{
//    list($records, $totalCount) = StationsRecordsAccess::GetAllStationRecords();
//    if ($totalCount == 0)
//        exit("none");
//    foreach ($records as $record)
//    {
//        DisplayRow($record);
//    }
//}
// uniqueness validation
if (isset($_GET["station"]) && isset($_GET["date"]) && isset($_GET["time"]))
{
    $unique = StationsRecordsAccess::StationRecordIsUnique($_GET["station"], $_GET["date"], $_GET["time"]);
    echo json_encode($unique);
}
// process submit
elseif (isset($_POST["action-type"]))
{
    switch ($_POST["action-type"])
    {
        case 'add':
            $newRec = new StationsRecordsAccess($_POST);
            $record = StationsRecordsAccess::Insert($newRec);
            DisplayRow($record);
            break;
        case 'delete':
            StationsRecordsAccess::Delete($_POST['id']);
            break;
        case 'edit':
            $rec = new StationsRecordsAccess($_POST);
            $record = StationsRecordsAccess::Update($rec);
            DisplayRow($record);
            break;
    }
}
elseif (isset($_GET['action-type']))
{
    switch ($_GET["action-type"])
    {
        case 'query':
            GetResults();
            break;
        case 'download':
            $sql = Lib\Manual_Entries\Query_Builder_Stations_Records::GenerateQuery($_GET['project']);
            QueryProcess::ExportCSV($sql);
            break;
    }
}

function GetResults()
{
    $page = isset($_GET[QueryConstants::Page]) ? $_GET[QueryConstants::Page] : 1;
    $rowsPerPage = $_GET[QueryConstants::Limit];
    $offset = $page > 1 ? $rowsPerPage * ($page - 1) : 0;
    
    list($records, $totalCount) = StationsRecordsAccess::GetAllStationRecords($_GET['project'], $offset, $rowsPerPage);//($_GET['project'], $offset, $rowsPerPage);//, $sortBy, $sortOrder);
    if ($totalCount == 0)
    {
        BeginTable();
        EndTableNoResults();
        exit;
    }
    
    QueryProcess::GeneratePagination($page, $rowsPerPage, $totalCount);
    
    BeginTable();
    foreach ($records as $record)
        DisplayRow($record);
    EndTable();
    
    QueryProcess::GeneratePagination($page, $rowsPerPage, $totalCount);    
}

function BeginTable()
{
    ?>
<table id="records-table" class="zebra-striped bordered-table">
    <thead>
        <tr>
            <th colspan="2"><input id="add-new-button" class="btn primary" type="button" value="Add New" /></th>
            <th class="header blue headerSortDown">Station Name</th>
            <th>Receiver Id</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Date In</th>
            <th>Time In</th>
            <th>Release</th>
            <th>Hobo</th>
            <th>Frequency Codespace</th>
            <th>Sync Tag</th>
            <th>Secondary Latitude</th>
            <th>Secondary Longitude</th>
            <th>Secondary Waypoint</th>
            <th>Depth</th>
            <th>Receiver Height</th>
            <th>Date Out</th>
            <th>Time Out</th>
            <th>Date Downloaded</th>
            <th>Comment</th>
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

function DisplayRow(StationsRecordsAccess $record)
{
    echo "<tr data-id='" . $record->getValueEncoded('id') . "'>\n";
    echo "<td><a href='javascript:void(0);' class='edit'>Edit</a></td>";
    echo "<td><a href='javascript:void(0);' class='delete'>Delete</a></td>";
    echo "<td class='$record->stations_name'>" . $record->getValueEncoded($record->stations_name) . "</td>";
    echo "<td class='$record->receivers_id'>" . $record->getValueEncoded($record->receivers_id) . "</td>";
    echo "<td class='$record->latitude'>" . $record->getValueEncoded($record->latitude) . "</td>";
    echo "<td class='$record->longitude'>" . $record->getValueEncoded($record->longitude) . "</td>";
    echo "<td class='$record->date_in'>" . $record->getValueEncoded($record->date_in) . "</td>";
    echo "<td class='$record->time_in'>" . $record->getValueEncoded($record->time_in) . "</td>";
    echo "<td class='$record->release_value'>" . $record->getValueEncoded($record->release_value) . "</td>";
    echo "<td class='$record->hobo'>" . $record->getValueEncoded($record->hobo) . "</td>";
    echo "<td class='$record->frequency_codespace'>" . $record->getValueEncoded($record->frequency_codespace) . "</td>";
    echo "<td class='$record->sync_tag'>" . $record->getValueEncoded($record->sync_tag) . "</td>";
    echo "<td class='$record->secondary_latitude'>" . $record->getValueEncoded($record->secondary_latitude) . "</td>";
    echo "<td class='$record->secondary_longitude'>" . $record->getValueEncoded($record->secondary_longitude) . "</td>";
    echo "<td class='$record->secondary_waypoint'>" . $record->getValueEncoded($record->secondary_waypoint) . "</td>";
    echo "<td class='$record->depth'>" . $record->getValueEncoded($record->depth) . "</td>";
    echo "<td class='$record->receiver_height'>" . $record->getValueEncoded($record->receiver_height) . "</td>";
    echo "<td class='$record->date_out'>" . $record->getValueEncoded($record->date_out) . "</td>";
    echo "<td class='$record->time_out'>" . $record->getValueEncoded($record->time_out) . "</td>";
    echo "<td class='$record->date_downloaded'>" . $record->getValueEncoded($record->date_downloaded) . "</td>";
    echo "<td class='$record->comment'>" . $record->getValueEncoded($record->comment) . "</td>";
    echo "\n</tr>";
}
?>