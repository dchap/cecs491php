<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Manual_Entries\Singular_Entries_Access as SingleEntries;

if (isset($_GET['side-effects']) && isset($_GET['type']))
{
    if ($_GET['type'] == 'stations')
    {
        list($temperature, $sonde, $stRecord) = SingleEntries::GetStationSideEffects($_GET['side-effects']);
        $output = '';
        if ($temperature == 0)
            $output .= "<p><span class='label success'>Safe</span> No temperature data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $temperature temperature record(s) will be no longer be associated with this station.</p>";
        if ($sonde == 0)
            $output .= "<p><span class='label success'>Safe</span> No sonde data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $sonde sonde record(s) will be no longer be associated with this station.</p>";
        if ($stRecord == 0)
            $output .= "<p><span class='label success'>Safe</span> No station record data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $stRecord station record(s) will be DELETED.</p>";
        
        exit($output);
    }
    elseif ($_GET['type'] == 'receivers')
    {
        list($metadata, $vue, $stRecord) = SingleEntries::GetReceiverSideEffects($_GET['side-effects']);
        $output = '';
        if ($metadata == 0)
            $output .= "<p><span class='label success'>Safe</span> No temperature data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $metadata metadata record(s) will be no longer be associated with this station.</p>";
        if ($vue == 0)
            $output .= "<p><span class='label success'>Safe</span> No sonde data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $vue vue record(s) will be no longer be associated with this station.</p>";
        if ($stRecord == 0)
            $output .= "<p><span class='label success'>Safe</span> No station record data will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $stRecord station record(s) will be DELETED.</p>";
        
        exit($output);
    }
    elseif ($_GET['type'] == 'projects')
    {
        list($fish, $station) = SingleEntries::GetProjectSideEffects($_GET['side-effects']);
        $output = '';
        if ($fish == 0)
            $output .= "<p><span class='label success'>Safe</span> No fish associated with this project will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $fish fish record(s) will be no longer be associated with this project.</p>";
        if ($station == 0)
            $output .= "<p><span class='label success'>Safe</span> No stations associated with this project will be affected.</p>";
        else
            $output .= "<p><span class='label important'>Warning</span> $station station record(s) will be no longer be associated with this project.</p>";
        
        exit($output);
    }
}

if (isset($_POST['actionType']) && $_POST['actionType'] == 'add')
{
    $value = SingleEntries::Insert($_POST['table'], $_POST['value']);
    $value = htmlspecialchars($value, ENT_QUOTES);
    echo "<option value='$value'>$value</option>";
}

if (isset($_POST['actionType']) && $_POST['actionType'] == 'delete')
{
    $field = GetFieldName($_POST['table']);
    SingleEntries::Delete($_POST['table'], $field, $_POST['value']);
}

if (isset($_POST['actionType']) && $_POST['actionType'] == 'edit')
{
    $field = GetFieldName($_POST['table']);
    echo SingleEntries::Update($_POST['table'], $field, $_POST['oldValue'], $_POST['newValue']);
}

function GetFieldName($table)
{
    switch ($table)
    {
        case 'receivers' : return 'id';
        case 'stations' :
        case 'projects' : return 'name';
    }
}
?>