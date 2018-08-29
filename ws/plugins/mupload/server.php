<?php
    /** путь относительно текущего расположения скрипта */
    $uploaddir = '../upload_files/';
    $uploadfile = $uploaddir . basename($_FILES['mupload']['name']);

    if (move_uploaded_file($_FILES['mupload']['tmp_name'], $uploadfile)) 
        echo 1;
    else
        echo 0;
        
    
    
?>