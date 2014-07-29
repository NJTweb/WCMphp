<?php

require_once("ConInfo.php");

$obj = getObject($_POST["Object"]);
$conn = getPDO($obj->connection);

$query = "SELECT MAX({$obj->primaryKey}) FROM {$obj->table}";

$stmt = $conn->prepare($query);

if($stmt->execute()){
    echo json_encode($stmt->fetchAll(PDO::FETCH_NUM));
}

?>