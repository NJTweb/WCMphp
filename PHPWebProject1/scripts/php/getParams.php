<?php   

//$testData = '{ query: "MachMTTR" }';

require_once("ConInfo.php");

$obj = getObject($_POST["Object"]);

$xml = simplexml_load_file("../../xml/queries.xml");
foreach($xml->query as $query):
    if($query["name"] == $obj->query):
        $qdata = $query;
    endif;
endforeach;

echo json_encode($qdata->parameters);

?>