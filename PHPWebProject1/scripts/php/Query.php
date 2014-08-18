<?php
class Query{
    private $name;
    private $fetch_type;
    public $params;        // params hold actual values with placeholders replaced
    public $placeholders;  // placeholders hold original values for parameters
    
    public function __construct($name, $params, $fetch_type = PDO::FETCH_NUM){
        $this->name = $name;
        $this->params = $params;
        $this->placeholders = $params;
        $this->fetch_type = $fetch_type;
    }
    
    public function execute(){

        $xml = simplexml_load_file("http://192.9.200.62/xml/queries.xml");
        $query = $xml->xpath("/queries/query[@name='".$this->name."']");
        $query_string = $query[0]->qstring;  
        $connection_string = $query[0]->connection;
        
        $query_string = ms_escape_string($query_string);
        $conn = get_connection($connection_string);
        if($conn){
            $stmt = $conn->prepare($query_string);
        }else{
            return array();
            exit();
        }
        
        if($stmt->execute($this->params)){
            if($rows = $stmt->fetchAll($this->fetch_type)){
                if($this->fetch_type == PDO::FETCH_NUM){
                    $first_of_each = function($n){
                        return $n[0];
                    };
                    $filtered = array_map($first_of_each, $rows);
                    return $filtered;
                }
            }else{
                return array();
            }
        }else{
            return array();
        }
    }
}
?>