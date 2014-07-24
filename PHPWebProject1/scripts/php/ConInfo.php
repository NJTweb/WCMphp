<?php
    $obj = json_decode($_POST["Object"]);

    //$testJSON = '{"rawObj":{},"fields":[{"name":"SEWONumber","format":"text","order":1.7976931348623157e+308},{"name":"AccidentType","format":"text","order":1.7976931348623157e+308},{"name":"Plant","format":"text","order":1.7976931348623157e+308},{"name":"Department","format":"text","order":1.7976931348623157e+308},{"name":"InjuredName","format":"text","order":1.7976931348623157e+308},{"name":"Sex","format":"text","order":1.7976931348623157e+308},{"name":"TypeOfPosition","format":"text","order":1.7976931348623157e+308},{"name":"ZZone","format":"text","order":1.7976931348623157e+308},{"name":"Machine","format":"text","order":1.7976931348623157e+308},{"name":"InjuredJob","format":"text","order":1.7976931348623157e+308},{"name":"ReportedBy","format":"text","order":1.7976931348623157e+308},{"name":"Shift","format":"text","order":1.7976931348623157e+308},{"name":"InjuryDate","format":"text","order":1.7976931348623157e+308},{"name":"InjuryTime","format":"text","order":1.7976931348623157e+308},{"name":"WWhat","format":"text","order":1.7976931348623157e+308},{"name":"InjuryType","format":"text","order":1.7976931348623157e+308,"value":" "},{"name":"BodyPart","format":"text","order":1.7976931348623157e+308},{"name":"SketchURL","format":"text","order":1.7976931348623157e+308},{"name":"ActionDescription","format":"text","order":1.7976931348623157e+308},{"name":"WWhen","format":"text","order":1.7976931348623157e+308},{"name":"PPESupplied","format":"text","order":1.7976931348623157e+308},{"name":"WWhere","format":"text","order":1.7976931348623157e+308},{"name":"PPEInUse","format":"text","order":1.7976931348623157e+308},{"name":"WWho","format":"text","order":1.7976931348623157e+308},{"name":"UsualWork","format":"text","order":1.7976931348623157e+308},{"name":"WWhich","format":"text","order":1.7976931348623157e+308},{"name":"HHow","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhy1","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhy2","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhy3","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhy4","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhy5","format":"text","order":1.7976931348623157e+308},{"name":"FiveWhyRootCause","format":"text","order":1.7976931348623157e+308},{"name":"RootCause","format":"text","order":1.7976931348623157e+308},{"name":"SecondaryCause","format":"text","order":1.7976931348623157e+308},{"name":"MicroCause","format":"text","order":1.7976931348623157e+308},{"name":"Action","format":"text","order":1.7976931348623157e+308},{"name":"ActionPlan","format":"text","order":1.7976931348623157e+308},{"name":"Responsible","format":"text","order":1.7976931348623157e+308},{"name":"DueDate","format":"text","order":1.7976931348623157e+308},{"name":"CloseDate","format":"text","order":1.7976931348623157e+308},{"name":"Notes","format":"text","order":1.7976931348623157e+308},{"name":"ExpansionPlan","format":"text","order":1.7976931348623157e+308},{"name":"Location","format":"text","order":1.7976931348623157e+308},{"name":"Act","format":"text","order":1.7976931348623157e+308},{"name":"EmployeeName","format":"text","order":1.7976931348623157e+308},{"name":"TeamLeadName","format":"text","order":1.7976931348623157e+308},{"name":"SupervisorName","format":"text","order":1.7976931348623157e+308},{"name":"DeptManagerName","format":"text","order":1.7976931348623157e+308},{"name":"SafetyMgrName","format":"text","order":1.7976931348623157e+308},{"name":"PlantMgrName","format":"text","order":1.7976931348623157e+308},{"name":"EmployeeDate","format":"text","order":1.7976931348623157e+308},{"name":"TeamLeadDate","format":"text","order":1.7976931348623157e+308},{"name":"SupervisorDate","format":"text","order":1.7976931348623157e+308},{"name":"DeptManagerDate","format":"text","order":1.7976931348623157e+308},{"name":"SafetyMgrDate","format":"text","order":1.7976931348623157e+308},{"name":"PlantMgrDate","format":"text","order":1.7976931348623157e+308},{"name":"EmployeeSignature","format":"text","order":1.7976931348623157e+308},{"name":"TeamLeadSignature","format":"text","order":1.7976931348623157e+308},{"name":"SupervisorSignature","format":"text","order":1.7976931348623157e+308},{"name":"DeptManagerSignature","format":"text","order":1.7976931348623157e+308},{"name":"SafetyMgrSignature","format":"text","order":1.7976931348623157e+308},{"name":"PlantMgrSignature","format":"text","order":1.7976931348623157e+308},{"name":"InjuredStatement","format":"text","order":1.7976931348623157e+308},{"name":"WitnessStatement","format":"text","order":1.7976931348623157e+308},{"name":"InjuredSignature","format":"text","order":1.7976931348623157e+308},{"name":"InjuredDate","format":"text","order":1.7976931348623157e+308},{"name":"WitnessSignature","format":"text","order":1.7976931348623157e+308},{"name":"WitnessDate","format":"text","order":1.7976931348623157e+308}],"name":"SEWO","table":"ESEWOs","primaryKey":"SEWONumber","ID":"241","connection":"Safety"}';
    //$testJSON = '{"connection" : "Safety", "Query" : "Plants", "Params" : []}';
    //$obj = json_decode($testJSON);
    $conStr = $obj->connection;

    $xml = simplexml_load_file("../../xml/connections.xml");
    foreach($xml->connection as $conXml):
        if($conXml["name"] == $conStr):
            $conData = $conXml;  
        endif;
    endforeach;

    define("HOST", $conData->host);     // The host you want to connect to.
    define("USER", $conData->user);    // The database username. 
    define("PASSWORD", $conData->password);    // The database password. 
    define("DATABASE", $conData->database);    // The database name.

    try{
        $conn = new PDO("sqlsrv:Server=".HOST."; Database=".DATABASE, USER, PASSWORD);
    }catch(PDOException $e){
        echo $e->getMessage();
        exit();
    }
?>