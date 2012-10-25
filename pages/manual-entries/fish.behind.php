<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Manual_Entries\Fish_Access as FishAccess;
use Lib\Data_Query\Query_Process as QueryProcess;
use Config\Constants\Query as QueryConstants;

if (isset($_GET["codespace"]) && isset($_GET["transmitter"]))
{
    $result = FishAccess::XmitterUnique($_GET["codespace"], $_GET["transmitter"]);
    echo json_encode($result);
}
elseif (isset($_GET["ascension"]))
{
    $result = FishAccess::AscensionUnique($_GET["ascension"]);
    echo json_encode($result);
}
elseif (isset($_POST["action-type"]))
{
    switch ($_POST["action-type"])
    {
        case 'add':
            $newRec = new FishAccess($_POST);
            $record = FishAccess::Insert($newRec);
            DisplayRow($record);
            break;
        case 'delete':
            FishAccess::Delete($_POST['id']);
            break;
        case 'edit':
            $rec = new FishAccess($_POST);
            $record = FishAccess::Update($rec);
            DisplayRow($record);
    }
}
elseif (isset($_GET['action-type']))
{
    switch ($_GET["action-type"])
    {
        case 'query':
            GetFishResults();
            break;
        case 'download':
            $sql = Lib\Manual_Entries\Query_Builder_Fish::GenerateQuery($_GET['project']);
            QueryProcess::ExportCSV($sql);
            break;
    }
}

function GetFishResults()
{
//    $sortOrder = $_POST[QueryConstants::SortOrder];
//    $sortBy = $_POST[QueryConstants::SortBy];
    $page = isset($_GET[QueryConstants::Page]) ? $_GET[QueryConstants::Page] : 1;
    $rowsPerPage = $_GET[QueryConstants::Limit];
    $offset = $page > 1 ? $rowsPerPage * ($page - 1) : 0;
    
    list($records, $totalCount) = FishAccess::GetAllFishRecords($_GET['project'], $offset, $rowsPerPage);//, $sortBy, $sortOrder);
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
                <th data-sort="frequency_codespace">Codespace</th>
                <th data-sort="transmitter_id">Transmitter ID</th>
                <th class="header blue headerSortDown" data-sort="ascension">Ascension</th>
                <th data-sort="genus">Genus</th>
                <th data-sort="species">Species</th>
                <th data-sort="date_deployed">Date Deployed</th>
                <th data-sort="time_deployed">Time Deployed</th>
                <th data-sort="sex">Sex</th>
                <th data-sort="total_length">Total Length (mm)</th>
                <th data-sort="standard_length">Standard Length (mm)</th>
                <th data-sort="dart_tag">Dart Tag</th>
                <th data-sort="dart_color">Dart Color</th>
                <th data-sort="landed_latitude">Landed Latitude</th>
                <th data-sort="landed_longitude">Landed Longitude</th>
                <th data-sort="released_latitude">Released Latitude</th>
                <th data-sort="released_longitude">Released Longitude</th>
                <th data-sort="method_of_release">Method of Release</th>
                <th data-sort="sensor_codespace">Sensor Codespace 1</th>
                <th>Sensor ID 1</th>
                <th>Sensor Codespace 2</th>
                <th>Sensor ID 2</th>
                <th>Sensor Codespace 3</th>
                <th>Sensor ID 3</th>
                <th>Fork Length (mm)</th>
                <th>Girth (mm)</th>
                <th>Weight (g)</th>
                <th>Time Out of Water (min)</th>
                <th>Time in Tricane (min)</th>
                <th>Time in Surgery (min)</th>
                <th>Recovery Time (min)</th>
                <th>Depth of Landing (m)</th>
                <th>Depth of Release (m)</th>
                <th>Landing Temperature (sst)</th>
                <th>Release Temperature (sst)</th>
                <th>Fish Condition</th>
    <!--            <th class="header">Photo</th>-->
                <th class="header">Comment</th>
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

function DisplayRow(FishAccess $record)
{
    echo "<tr data-id='" . $record->getValueEncoded('id') . "'>\n";
    echo "<td><a href='javascript:void(0);' class='edit'>Edit</a></td>";
    echo "<td><a href='javascript:void(0);' class='delete'>Delete</a></td>";
    echo "<td class='$record->codespace'>" . $record->getValueEncoded($record->codespace) . "</td>";
    echo "<td class='$record->transmitter_id'>" . $record->getValueEncoded($record->transmitter_id) . "</td>";
    echo "<td class='$record->ascension'>" . $record->getValueEncoded($record->ascension) . "</td>";
    echo "<td class='$record->genus'>" . $record->getValueEncoded($record->genus) . "</td>";
    echo "<td class='$record->species'>" . $record->getValueEncoded($record->species) . "</td>";
    echo "<td class='$record->date_deployed'>" . $record->getValueEncoded($record->date_deployed) . "</td>";
    echo "<td class='$record->time_deployed'>" . $record->getValueEncoded($record->time_deployed) . "</td>";
    echo "<td class='$record->sex'>" . $record->getValueEncoded($record->sex) . "</td>";
    echo "<td class='$record->total_length'>" . $record->getValueEncoded($record->total_length) . "</td>";
    echo "<td class='$record->standard_length'>" . $record->getValueEncoded($record->standard_length) . "</td>";
    echo "<td class='$record->dart_tag'>" . $record->getValueEncoded($record->dart_tag) . "</td>";
    echo "<td class='$record->dart_color'>" . $record->getValueEncoded($record->dart_color) . "</td>";
    echo "<td class='$record->landed_latitude'>" . $record->getValueEncoded($record->landed_latitude) . "</td>";
    echo "<td class='$record->landed_longitude'>" . $record->getValueEncoded($record->landed_longitude) . "</td>";
    echo "<td class='$record->released_latitude'>" . $record->getValueEncoded($record->released_latitude) . "</td>";
    echo "<td class='$record->released_longitude'>" . $record->getValueEncoded($record->released_longitude) . "</td>";
    echo "<td class='$record->release_method'>" . $record->getValueEncoded($record->release_method) . "</td>";
    echo "<td class='$record->sensor_codespace1'>" . $record->getValueEncoded($record->sensor_codespace1) . "</td>";
    echo "<td class='$record->sensor_id1'>" . $record->getValueEncoded($record->sensor_id1) . "</td>";
    echo "<td class='$record->sensor_codespace2'>" . $record->getValueEncoded($record->sensor_codespace2) . "</td>";
    echo "<td class='$record->sensor_id2'>" . $record->getValueEncoded($record->sensor_id2) . "</td>";
    echo "<td class='$record->sensor_codespace3'>" . $record->getValueEncoded($record->sensor_codespace3) . "</td>";
    echo "<td class='$record->sensor_id3'>" . $record->getValueEncoded($record->sensor_id3) . "</td>";
    echo "<td class='$record->fork_length'>" . $record->getValueEncoded($record->fork_length) . "</td>";
    echo "<td class='$record->girth'>" . $record->getValueEncoded($record->girth) . "</td>";
    echo "<td class='$record->weight'>" . $record->getValueEncoded($record->weight) . "</td>";
    echo "<td class='$record->time_out_of_water'>" . $record->getValueEncoded($record->time_out_of_water) . "</td>";
    echo "<td class='$record->time_in_tricane'>" . $record->getValueEncoded($record->time_in_tricane) . "</td>";
    echo "<td class='$record->time_in_surgery'>" . $record->getValueEncoded($record->time_in_surgery) . "</td>";
    echo "<td class='$record->recovery_time'>" . $record->getValueEncoded($record->recovery_time) . "</td>";
    echo "<td class='$record->landing_depth'>" . $record->getValueEncoded($record->landing_depth) . "</td>";
    echo "<td class='$record->release_depth'>" . $record->getValueEncoded($record->release_depth) . "</td>";
    echo "<td class='$record->landing_temperature'>" . $record->getValueEncoded($record->landing_temperature) . "</td>";
    echo "<td class='$record->release_temperature'>" . $record->getValueEncoded($record->release_temperature) . "</td>";
    echo "<td class='$record->fish_condition'>" . $record->getValueEncoded($record->fish_condition) . "</td>";
//    echo "<td class='$record->photo_reference'>" . $record->getValueEncoded($record->photo_reference) . "</td>";
    echo "<td class='$record->comment'>" . $record->getValueEncoded($record->comment) . "</td>";
    echo "\n</tr>";
}

?>