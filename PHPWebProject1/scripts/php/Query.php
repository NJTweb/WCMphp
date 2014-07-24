<?php


require_once("ConInfo.php");

$xml = simplexml_load_file("../../xml/queries.xml");
foreach($xml->query as $query):
    if($query["name"] == $obj->query->Query):
        $queryStr = $query->qstring;  
    endif;
endforeach;
    
$stmt = $conn->prepare($queryStr);

if($stmt->execute($obj->query->Params)):
    echo json_encode($stmt->fetchAll(PDO::FETCH_NUM));
endif;

?>