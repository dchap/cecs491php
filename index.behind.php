<?php
//    Copyright (C) 2012 Edward Han
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

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