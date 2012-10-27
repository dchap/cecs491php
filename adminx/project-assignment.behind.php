<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();

use Lib\Manual_Entries\Project_Assignment_Access as ProjectAssignment;

$stationList = ProjectAssignment::GetAllStations();
$fishList = ProjectAssignment::GetAllFishRecordsAscension();

if (isset($_GET['type']) && isset($_GET['project']))
{
    $type = $_GET['type'];
    $project = $_GET['project'];
    if ($type == 'stations')
    {
        $entries = ProjectAssignment::GetAllAssignedStations($project);
        foreach ($entries as $value)
        {
            $value = htmlspecialchars($value, ENT_QUOTES);
            echo "<option value='$value'>$value</option>";
        }
    }
    else if ($type == 'fish')
    {
        $entries = ProjectAssignment::GetAllAssignedFish($project);
        foreach ($entries as $id => $asc)
        {
            $asc = htmlspecialchars($asc, ENT_QUOTES);
            echo "<option value='$id'>$asc</option>";
        }
        
    }
}

if (isset($_POST['project']) && isset($_POST['dataType']) 
        && isset($_POST['dataValue']) && isset($_POST['actionType']))
{
    $project = $_POST['project'];
    $type = $_POST['dataType'];
    $value = $_POST['dataValue'];
    $action = $_POST['actionType'];
    if ($action == 'add')
    {
        switch ($type)
        {
            case 'stations' :
                ProjectAssignment::AddStation($value, $project);
                break;
            case 'fish' :
                ProjectAssignment::AddFish($value, $project);
                break;
        }
    }
    else if ($action == 'delete')
    {
        switch ($type)
        {
            case 'stations' :
                ProjectAssignment::RemoveStation($value, $project);
                break;
            case 'fish' :
                ProjectAssignment::RemoveFish($value, $project);
                break;
        }
    }
}
?>