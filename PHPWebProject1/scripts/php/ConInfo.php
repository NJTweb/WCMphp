<?php
    
    function getObject($json){
        return json_decode($_POST["Object"]);
    }
    
    function getPDO($connection){
        $con = (string)$connection;
        
        $xml = simplexml_load_file("../../xml/connections.xml");
        foreach($xml->connection as $conXml):
            $name = $conXml["name"];
            if($name == $con):
                $conData = $conXml;
                break;
            endif;
        endforeach;

        define("HOST", $conData->host);     // The host you want to connect to.
        define("USER", $conData->user);    // The database username. 
        define("PASSWORD", $conData->password);    // The database password. 
        define("DATABASE", $conData->database);    // The database name.

        try{
            $conn = new PDO("sqlsrv:Server=".HOST."; Database=".DATABASE, USER, PASSWORD);
            return $conn;
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    
?>