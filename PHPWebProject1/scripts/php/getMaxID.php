<?php
if(isset($_POST)){

require_once("Utilities.php");

//$t_POST = array("Connection" => "Safety", "PrimaryKey" => "ID", "Table" => "DevForm");

$conn = get_connection($_POST["Connection"]);

$query = "SELECT MAX({$_POST["PrimaryKey"]}) FROM {$_POST["Table"]}";
$query = ms_escape_string($query);

if($conn){
    $stmt = $conn->prepare($query);
}else{
    echo -1;
    exit();
}

if($stmt->execute()){
    if($row = $stmt->fetch(PDO::FETCH_NUM)){
        echo $row[0];
    }else{
        echo -1;
    }
}else{
    echo -1;
}

}

?>