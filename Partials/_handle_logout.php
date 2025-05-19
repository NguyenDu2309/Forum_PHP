<?php
        
        session_start();
        session_unset();
        session_destroy();
        header("location:/Forum_website/index.php");
        exit();
?>