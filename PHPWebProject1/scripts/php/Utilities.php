<?php
  function get_connection($connection_name){
    $con = (string)$connection_name;
        
    $xml = simplexml_load_file("http://192.9.200.62/xml/connections.xml");
    $con_nodes = $xml->xpath("/connections/connection[@name='".$con."']");
    $con_data = $con_nodes[0];

    try{
        $conn = new PDO("sqlsrv:Server=".$con_data->host."; Database=".$con_data->database, $con_data->user, $con_data->password);
        return $conn;
    }
    catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}

function ms_escape_string($data) {
    if (!isset($data) or empty($data)){
        return '';
    }
    if (is_numeric($data)){
        return $data;
    }
    $non_displayables = array(
        '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/',             // url encoded 16-31
        '/[\x00-\x08]/',            // 00-08
        '/\x0b/',                   // 11
        '/\x0c/',                   // 12
        '/[\x0e-\x1f]/'             // 14-31
    );
    foreach ( $non_displayables as $regex ){
        $data = preg_replace( $regex, '', $data );
    }
    $data = str_replace("'", "''", $data );
    return $data;
}

?>