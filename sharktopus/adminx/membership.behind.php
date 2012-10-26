<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Manual_Entries\Members_Access as MembersAccess;

if (isset($_GET["loadTable"]))
{
    $records = MembersAccess::GetAllMembers();
    if (count($records) == 0)
        exit("none");
    foreach ($records as $record)
    {
        DisplayRow($record);
    }
}

if (isset($_GET["username"]))
{
    $result = MembersAccess::UsernameNotTaken($_GET["username"]);
    echo json_encode($result);
}

if (isset($_POST["actionType"]))
{
    switch ($_POST["actionType"])
    {
        case 'add':
            $newRec = new MembersAccess($_POST);
            $record = MembersAccess::Insert($newRec);
            DisplayRow($record);
            break;
        case 'delete':
            MembersAccess::Delete($_POST['id']);
            break;
        case 'edit':
            $rec = new MembersAccess($_POST);
            $record = MembersAccess::Update($rec);
            DisplayRow($record);
    }
}

function DisplayRow(MembersAccess $record)
{
    echo "<tr data-id='" . $record->getValueEncoded('id') . "'>\n";
    echo "<td><a href='javascript:void(0);' class='edit'>Edit</a></td>";
    echo "<td><a href='javascript:void(0);' class='delete'>Delete</a></td>";
    echo "<td class='$record->username'>" . $record->getValueEncoded($record->username) . "</td>";
    echo "<td >*********</td>";
    echo "<td class='$record->fname'>" . $record->getValueEncoded($record->fname) . "</td>";
    echo "<td class='$record->lname'>" . $record->getValueEncoded($record->lname) . "</td>";
    echo "<td class='$record->account_type'>" . $record->getValueEncoded($record->account_type) . "</td>";
    echo "\n</tr>";
}
?>