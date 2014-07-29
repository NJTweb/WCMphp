<?php

require_once("ConInfo.php");

$obj = getObject($_POST["Object"]);
$conn = getPDO($obj->connection);

$query = "SELECT * FROM {$obj->table} WHERE {$obj->primaryKey}={$obj->ID}";

$stmt = $conn->prepare($query);

if($stmt->execute()):
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
else:
    echo json_encode('[{"SEWONumber" : 200}]');
endif;

?>