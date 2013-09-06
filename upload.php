<?php

session_start();

$path = "uploads/".$_SESSION['uploads_id']."/";

// set error reporting level
if (version_compare(phpversion(), '5.3.0', '>=') == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);

function bytesToSize1024($bytes, $precision = 2) {
    $unit = array('B','KB','MB');
    return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
}

if (isset($_FILES['myfile'])) {
    $sFileName = $_FILES['myfile']['name'];
    $sFileType = $_FILES['myfile']['type'];
    $sFileSize = bytesToSize1024($_FILES['myfile']['size'], 1);
    
    if( !is_dir($path) ){
        
        mkdir($path,0777);
    }

    if( is_uploaded_file( $_FILES['myfile']['tmp_name'] )){
        
        if( move_uploaded_file($_FILES['myfile']['tmp_name'],$path.$_FILES['myfile']['name']) ){
    
    echo <<<EOF
<div class="s">
    <p>Tu archivo: {$sFileName} se subio con éxito.</p>
</div>
EOF;

        } else {
            
                    echo '<div class="f">An error occurred</div>';

        }

    } else {
        
        echo '<div class="f">An error occurred</div>';
            
    }

} else {
    echo '<div class="f">An error occurred</div>';
}