<?php
namespace Config\Constants
{
    // Relative to document root.  Must prepend $_SERVER['DOCUMENT_ROOT'] before use.
    class Upload_Directories
    {
        const Metadata = '/uploaded_files/metadata/';
        const Picture = '/uploaded_files/pictures/';
        const Sonde = '/uploaded_files/sonde/';
        const Temperatures = '/uploaded_files/temperatures/';
        const Vue = '/uploaded_files/vue/';
    }
}
?>
