<?php
if(isset($_POST)){

require_once("Utilities.php");

//$test_data = '{"Query" : {"Query": "AuditsPerAuditor", "Params" : []}, "fetchType" : "ASSOC"}';

$obj = json_decode($_POST["Object"]);
//$obj = json_decode($test_data);

$xml = simplexml_load_file("../../xml/queries.xml");
foreach($xml->query as $query){
    if($query["name"] == $obj->Query->Query){
        $queryStr = $query->qstring;  
        $conStr = $query->connection;
        break;
    }
}

$conn = get_connection($conStr);
if($conn){
    $stmt = $conn->prepare($queryStr);
}else{
    echo "[]";
    exit();
}

if(isset($obj->fetchType)){
    $fetchType = (($obj->fetchType == "ASSOC") ? PDO::FETCH_ASSOC : PDO::FETCH_NUM);
}else{
    $fetchType = PDO::FETCH_NUM;
}
    

if($stmt->execute($obj->Query->Params)){
    if($rows = $stmt->fetchAll($fetchType)){
        echo json_encode($rows);
    }else{
        echo "[]";
    }
}else{
    //$err = $stmt->errorInfo();
    //echo $err;
    echo "[]";
}

}
?>