<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Data_Query\Query_Builder_Realtime as QueryBuilder;
use Lib\Data_Query\Query_Process as QueryProcess;


if (isset($_GET['email']) && isset($_GET['email-preference']))
{   
    $email = $_GET['email'];

    //if email address matches regular expression, update email to database.
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $updateSql = QueryBuilder::GenerateEmailQuery($_GET['email'], $_GET['email-preference']);       
        QueryProcess::UpdateEmail($updateSql);
        header("Location: /pages/home/home.php");
    }
    else //Else refresh page
        header("Location: /pages/home/email.php");
}

?>
