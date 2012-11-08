<?php
    require_once substr(__FILE__, 0, -4) . ".behind.php";
    use Config\Constants\Urls as Url;
    use Config\Constants\Session_Variables as Session;
    session_start();

    if (isset($_GET['denied']))
    {
        $showPermissionError = true;
    }
    elseif (isset($_GET['redirect']))
    {
        $redirect = ($_GET['redirect']);
    }

    if (isset($_SESSION[Session::AccountType]))
    {
        unset($_SESSION[Session::AccountType]);
        unset($_SESSION[Session::Name]);
        session_write_close();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="/assets/shared/js/jquery-1.6.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/assets/shared/global.css" />
    <link rel="stylesheet" type="text/css" href="/assets/home/login.min.css" />
    <script type="text/javascript">
        $(function()
        { 
            // can only show one error or the other when trying to access another page
            var redirect = '<?php echo isset($redirect) ? $redirect : null; ?>';
            var showPermission = '<?php echo isset($showPermissionError) ? "yes" : null; ?>';
            if (showPermission)
                $('#error-permission').show();
            else if (redirect)
                $('#error-login').show();
            
            function Submit() {
                $('.errors').hide();
                var validated = true;
                if ($('.enter[name=user]').val().trim() == '') {
                    $('#error-user').fadeIn();
                    validated = false;
                }
                if ($('.enter[name=pass]').val().trim() == '') {
                    $('#error-pass').fadeIn();
                    validated = false;
                }
                if (!validated)
                    return;
                
                $.post('index.behind.php', 
                    $('form').serialize(), 
                    function(data)
                    {
                        if (data == 'none')
                            $('#error-record').fadeIn();
                        else if (data == 'user')
                        {
                            window.location.replace('<?php echo Url::Query ?>');
                        }
                        else if (data == 'superuser' || data == 'admin' || data == 'superadmin')
                        {
                            if (redirect)
                                window.location.replace(redirect);
                            else
                                window.location.replace('<?php echo Url::Home ?>'); 
                        }
                    }
                ); 
            }
            
            $(':button').click(Submit);
            $('form').keypress( function(e)
            {   
                if (e.which == 13)
                {
                    Submit();
                }
            });

            $(':input[name=user]').focus();
        });
    </script>
</head>
<body>
<!--    <div id="img-div">
        <img src="/assets/home/login_small_alt.jpg" />
    </div>-->
    <div id="form-div">
        <form action="index.php" method="post">
            <label>username&nbsp;<input class="enter" name="user" type="text" maxlength="20" /></label>
            <label>password&nbsp;<input class="enter" name="pass" type="password" maxlength="20" /></label>
            <input id="submit" type="button" value="submit" />
        </form>
    </div>
    <div id="error-div">
        <p id="error-login" class="errors">login required</p>
        <p id="error-permission" class="errors">not authorized</p>
        <p id="error-record" class="errors">no match found</p>
        <p id="error-user" class="errors">username required</p>
        <p id="error-pass" class="errors">password required</p>
    </div>
</body>
</html>