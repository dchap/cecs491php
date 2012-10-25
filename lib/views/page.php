<?php
namespace Lib\Views
{
    use Config\Constants\Session_Variables as Session;
    use Config\Constants\Urls as Url;
    
    class Page
    {
        private $cssOpen = '<link rel="stylesheet" type="text/css" href="';
        private $cssClose = "\" />\n";
        private $jsOpen = '<script type="text/javascript" src="';
        private $jsClose = "\"></script>\n";
        private $head = '';
        
        public function __construct($permissionLevel)
        {
            @session_start();
            if (!isset($_SESSION[Session::AccountType]))
            {
                $url = urlencode($_SERVER['REQUEST_URI']);
                header("location: " . Url::Login . "?redirect=$url");
                exit;
            }
            if ($_SESSION[Session::AccountType] < $permissionLevel)
            {
                $url = urlencode($_SERVER['REQUEST_URI']);
                header("location: " . Url::Login . "?denied");
                exit;
            }
        }
        
        // filepath must be root relative
        public function IncludeCss($filepath)
        {
            $this->head .= $this->cssOpen . $filepath . $this->cssClose;
        }
        
        public function IncludeJs($filepath)
        {
            $this->head .= $this->jsOpen . $filepath . $this->jsClose;
        }
        
        public function BeginHTML()
        {
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/assets/shared/global.css" />
    <link rel="stylesheet/less" type="text/css" href="/assets/shared/less/bootstrap.less" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/shared/custom-theme/jquery-ui-1.8.16.custom.css" />
    <script type="text/javascript" src="/assets/shared/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="/assets/shared/js/less-1.1.5.min.js"></script>
    <script type="text/javascript" src="/assets/shared/js/jquery.form.min.js"></script>
    <script type="text/javascript" src="/assets/shared/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="/assets/shared/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/assets/shared/js/bootstrap/bootstrap-modal.js"></script>
    <script type="text/javascript" src="/assets/shared/js/bootstrap/bootstrap-tabs.js"></script>
    <script type="text/javascript" src="/assets/shared/js/bootstrap/bootstrap-buttons.js"></script>
    <script type="text/javascript" src="/assets/shared/global.js"></script>
    <?php echo $this->head; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="sidebar">
            <div class="well">
                <h5 class="home-link">
                    <?php if ($_SESSION[Session::AccountType] >= Session::Superuser): ?>
                        <a href="/pages/home/home.php">Home</a>
                        <span id="spacer"> | </span>
                    <?php endif; ?>
                    <a href="/pages/home/login.php">Logout</a>
                </h5>
            <?php $this->GenerateNav(); ?>
            </div>
        </div>
        <div class="content">
<?php
        }

        private function GenerateNav()
        {
            switch($_SESSION[Session::AccountType])
            {
                case $_SESSION[Session::AccountType] == Session::Superadmin :
                    $this->SuperadminNav();
                case $_SESSION[Session::AccountType] >= Session::Admin :
                    $this->AdminNav();
                case $_SESSION[Session::AccountType] >= Session::Superuser :
                    $this->SuperuserNav();
                case $_SESSION[Session::AccountType] >= Session::User :
                    $this->UserNav();
            }
        }
        
        private function SuperadminNav()
        {
?>
            <script type="text/javascript">
                jQuery(function() {
                    jQuery('<li><a href="/adminx/membership.php">Membership</a></li>')
                    .prependTo(jQuery('#admin-nav'));
                });
            </script>
<?php
        }
        
        private function AdminNav()
        {
?>
            <h5>Admin</h5>
            <ul id="admin-nav">
                <li><a href="/adminx/singular-entries.php">Singular Entries</a></li>
                <li><a href="/adminx/project-assignment.php">Project Assignment</a></li>
            </ul>
<?php
        }
        
        private function SuperuserNav()
        {
?>
            <h5>Uploads</h5>
            <ul>
                <li><a href="/pages/file-uploads/view-uploads.php">View Uploaded Files</a></li>
                <li><a href="/pages/file-uploads/upload.php">Upload Files</a></li>
            </ul>
            <h5>Manual Entries</h5>
            <ul>
                <li><a href="/pages/manual-entries/station-records.php">Station Records</a></li>
                <li><a href="/pages/manual-entries/fish.php">Fish Records</a></li>
            </ul>
<?php
        }
        
        private function UserNav()
        {
?>
            <h5>Query</h5>
            <ul>
                <li><a href="/pages/data-query/main-query.php">Main Query</a></li>
                <li><a href="/pages/data-query/visitor-query.php">Visitor Query</a></li>
                <li><a href="/pages/data-query/realtime-query.php">Real time Query</a></li>
<?php
        }
        
        public function EndHTML()
        {
?>
        </div>
    </div>
</body>
</html>
<?php
        }
    }
}
?>
