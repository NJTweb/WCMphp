<?php

if(isset($_POST)){
    require_once("Utilities.php");
    $query_func = "prepare_update";
    execute_query_upload_files_and_notify($query_func);
}

?>