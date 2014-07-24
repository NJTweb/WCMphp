<?php

require_once("ConInfo.php");

$query = "SELECT * FROM {$obj->table} WHERE {$obj->primaryKey}={$obj->ID}";

$stmt = $conn->prepare($query);

if($stmt->execute()):
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
else:
    echo json_encode('[{"SEWONumber" : 200}]');
endif;

?>