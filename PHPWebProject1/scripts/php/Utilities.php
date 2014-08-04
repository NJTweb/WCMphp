<?php
  
function execute_query_upload_files_and_notify($query_func){

    $form_data = extract_form_data();
    $conn = get_connection($form_data["Connection"]);

    $query = $query_func($form_data);
    $stmt = $conn->prepare($query);

    if($stmt->execute($_POST)){
        echo "Form updated/submitted.<br>";
        notify($form_data["Contacts"], $form_data["Name"]." number ".$form_data["ID"]." updated/submitted.", $form_data["Name"]." number ".$form_data["ID"]." updated/submitted. <br>".$form_data["EmailBody"]);
        if(isset($_FILES)){
            upload_files($form_data);
        }
    }else{
        echo "Form NOT updated/submitted.<br>";
        print_r($stmt->errorInfo());
    }
}

function prepare_insert($form_data){
    $query = "INSERT INTO {$form_data["Table"]} ";
    $cols = "";
    $vals = "";
    foreach($_POST as $col => $val){
        $cols .= $col.",";
        $vals .= ":".$col.",";
    }
    $cols = substr($cols, 0, strlen($cols) - 1);
    $vals = substr($vals, 0, strlen($vals) - 1);
    $query .= "(".$cols.") VALUES (".$vals.")";
    $query = ms_escape_string($query);
    return $query;
}

function prepare_update($form_data){
    $query = "UPDATE {$form_data["Table"]} SET ";
    foreach($_POST as $col => $val){
        $query .= $col."=:".$col.",";
    }
    $query = substr($query, 0, strlen($query) - 1);
    $query .= " WHERE {$form_data["PrimaryKey"]}={$form_data["ID"]}";
    $query = ms_escape_string($query);
    return $query;
}

function extract_form_data(){
    $form_data = $_POST["FormData"];
    unset($_POST["FormData"]);                  // remove form metadata from $_POST vars
    unset($_POST[$form_data["PrimaryKey"]]);    // remove $_POST key containing primary key value
    return $form_data;
}
  
function get_connection($connection_name){
    $con = (string)$connection_name;
        
    $xml = simplexml_load_file("../../xml/connections.xml");
    foreach($xml->connection as $conXml):
        $name = $conXml["name"];
        if($name == $con):
            $conData = $conXml;
            break;
        endif;
    endforeach;

    try{
        $conn = new PDO("sqlsrv:Server=".$conData->host."; Database=".$conData->database, $conData->user, $conData->password);
        return $conn;
    }
    catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}

function ms_escape_string($data) {
    if (!isset($data) or empty($data)){
        return '';
    }
    if (is_numeric($data)){
        return $data;
    }
    $non_displayables = array(
        '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/',             // url encoded 16-31
        '/[\x00-\x08]/',            // 00-08
        '/\x0b/',                   // 11
        '/\x0c/',                   // 12
        '/[\x0e-\x1f]/'             // 14-31
    );
    foreach ( $non_displayables as $regex ){
        $data = preg_replace( $regex, '', $data );
    }
    $data = str_replace("'", "''", $data );
    return $data;
}

function notify($contacts, $subject, $body){
    $conn = get_connection("Safety");
    $stmt = $conn->prepare("INSERT INTO Emails (Contacts, Subj, Body) VALUES(?, ?, ?)");
    if($stmt->execute(array($contacts, $subject, $body))){
        echo "Email query executed (Email will send in 0-5 minutes)<br>";
    }else{
        echo "Email query not executed<br>";
    }
}

/*
$_FILES["file"]["name"] - the name of the uploaded file
$_FILES["file"]["type"] - the type of the uploaded file
$_FILES["file"]["size"] - the size in bytes of the uploaded file
$_FILES["file"]["tmp_name"] - the name of the temporary copy of the file stored on the server
$_FILES["file"]["error"] - the error code resulting from the file upload
 */

function upload_files($form_data){
    for($i = 0; $i < count($_FILES["file"]["name"]); ++$i) {
        $base_file = basename($_FILES["file"]["name"][$i]);
        if($base_file != ""){
            $fname = "../../uploads/{$form_data["Name"]}_{$form_data["ID"]}_{$i}_{$base_file}";
            if(move_uploaded_file($_FILES["file"]["tmp_name"][$i],$fname)){
                echo "Uploaded file ".$base_file."<br>";
            }else{
                echo "Error: ".$_FILES["file"]["error"][$i].", Could not upload ".$_FILES["file"]["name"][$i]." to ".$fname."<br>";
            }
        }
    }
}

?>