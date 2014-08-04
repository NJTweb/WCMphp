<?php
if(isset($_POST)){

$xml = simplexml_load_file("../../xml/queries.xml");
foreach($xml->query as $query):
    if($query["name"] == $_POST["Query"]):
        $qdata = $query;
    endif;
endforeach;

echo json_encode($qdata->parameters);

}
?>