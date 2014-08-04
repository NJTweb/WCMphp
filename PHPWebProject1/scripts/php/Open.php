 <?php
 
if(isset($_POST)){

    //$t_POST = array("Connection" => "Safety", "Table" => "DevForm", "PrimaryKey" => "ID", "ID" => "1");

    require_once("Utilities.php");

    // send only form data ($_POST contains form meta-data)
    $query = "SELECT * FROM {$_POST["Table"]} WHERE {$_POST["PrimaryKey"]}=?";
    $query = ms_escape_string($query);
    $conn = get_connection($_POST["Connection"]);
    if($conn){
        $stmt = $conn->prepare($query);
    }else{
        echo "[]";
        exit();
    }
   
    if($stmt->execute(array($_POST["ID"]))){
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data = format_record($conn, $row, $_POST["Table"]);
            echo json_encode($data);
        }else{
            echo "[]";
        }
    }else{
        echo "[]";
    }
    
}

function format_record($conn, $row, $table){
    $formatted_data = array();
    foreach($row as $col => $val){
        $query = "SELECT TYPE_NAME(system_type_id) as DataType FROM sys.columns WHERE name=? AND [object_id] = OBJECT_ID(?)";
        $stmt = $conn->prepare($query);
        $stmt->execute(array($col, $table));
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $data_type = $row[0];
        switch($data_type){
            case "datetime":
                $vals = explode(" ", $val);
                $val = $vals;
                break;
            case "varchar":
                $val = (string)($val);
                break;
            case "int":
                $val = (int)($val);
                break;
            case "float":
                $val = (float)($val);
                break;
        }
        $formatted_data[$col] = array("type" => $data_type, "value" => $val);
    }
    return $formatted_data;
}

?>