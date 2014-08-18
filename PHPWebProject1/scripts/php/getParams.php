<?php
if(isset($_POST)){

$xml = simplexml_load_file("http://192.9.200.62/xml/queries.xml");
$qdata_nodes = $xml->xpath("/queries/query[@name='".$_POST["Query"]."']");
$qdata = $qdata_nodes[0];

echo json_encode($qdata->parameters);

// just send this whole statement to Pipe.php

}
?>