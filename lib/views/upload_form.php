<?php
namespace Lib\Views
{
    /**
     * Description of uploadForm
     */
    class Upload_Form 
    {
        public static function Generate($filetype)
        {
?>
<form id="<?php echo $filetype; ?>-form" action="upload.behind.php" method="POST" enctype="multipart/form-data">
    <div class="form-rows">
        <p>Choose a <?php echo $filetype; ?> file to upload:</p>
    </div>
    <div class="form-rows">
        <input class="span5 choose-file" name="uploadedFile" type="file" />
    </div>
    <div class="form-rows">
        <input type="hidden" name="file-type" value="<?php echo $filetype; ?>"/>
        <button class="btn primary submit-button" data-loading-text="Uploading...">Upload File</button>
        <img class="upload-indicator" src="/assets/shared/ajax-loader.gif" alt="uploading..." />
    </div>
</form>

<?php
        }
    }

}
?>
