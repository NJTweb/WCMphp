<?php


require_once("ConInfo.php");

$obj = getObject($_POST["Object"]);

$xml = simplexml_load_file("../../xml/queries.xml");
foreach($xml->query as $query):
    if($query["name"] == $obj->query->Query):
        $queryStr = $query->qstring;  
        $conStr = $query->connection;
        break;
    endif;
endforeach;

$conn = getPDO($conStr);

if(isset($obj->fetchType)):
    $fetchType = (($obj->fetchType == "ASSOC") ? PDO::FETCH_ASSOC : PDO::FETCH_NUM);
else:
    $fetchType = PDO::FETCH_NUM;
endif;
    
$stmt = $conn->prepare($queryStr);

if($stmt->execute($obj->query->Params)):
    echo json_encode($stmt->fetchAll($fetchType));
else:
    $err = $stmt->errorInfo();
    echo $err;
endif;

?>