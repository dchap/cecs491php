<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions(".php");
spl_autoload_register();
use Lib\Manual_Entries\Members_Access as Members;
use Config\Constants\Account_Types as AccTypes;
use Config\Constants\Session_Variables as Session;

if (isset($_POST['user']) && isset($_POST['pass']))
{
    $user = Members::GetMember($_POST['user'], $_POST['pass']);
    if ($user == false)
        exit("none");
    session_start();
    $_SESSION[Session::Name] = $user['name'];
    switch ($user['account_type'])
    {
        case AccTypes::User :
            $_SESSION[Session::AccountType] = Session::User;
            exit('user');
        case AccTypes::Superuser :
            $_SESSION[Session::AccountType] = Session::Superuser;
            exit('superuser');
        case AccTypes::Admin :
            $_SESSION[Session::AccountType] = Session::Admin;
            exit('admin');
        case AccTypes::Superadmin :
            $_SESSION[Session::AccountType] = Session::Superadmin;
            exit('superadmin');
    }
}
?>