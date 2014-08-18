<?php
session_start();

require_once("Utilities.php");
require_once("Pipe.php");

class Form{
    private $fields;
    
    private $name;
    private $table;
    private $primary_key;
    private $id;
    
    private $connection;
    
    private $contacts;
    private $email_body;
    
    public $has_record = false;
    
    public function __construct($name, $table, $primary_key, $id, $connection, $contacts, $email_body){
        $this->name = $name;
        $this->table = $table;
        $this->primary_key = $primary_key;
        $this->id = $id;
        $this->connection = $connection;
        $this->contacts = $contacts;
        $this->email_body = $email_body;
    }
    
    public function __destruct(){
        $_SESSION["Object"] = serialize($this);
    }
    
    public function set_email_body($email_body){
        $this->email_body = $email_body;
    }
    
    public static function revive(){
        return unserialize($_SESSION["Object"]);
    }   
    
    public function open(){
        $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key}=?";
        $query = ms_escape_string($query);
        $conn = get_connection($this->connection);
        if($conn){
            $stmt = $conn->prepare($query);
        }else{
            return;
        }
        
        if($stmt->execute(array($this->id))){
            if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $this->data_to_fields($row);
                $this->has_record = true;
            }else{
                $this->has_record = false;
                $max_id = $this->get_max_id();
                ++$max_id;
                $this->id = $max_id;
                $this->get_field_by_name($this->primary_key)->set_value($max_id);
                return;
            }
        }else{
            return;
        }
    }
    
    public function add_file($file){
        echo "test";
    }
    
    private function data_to_fields($data){
        foreach($data as $name => $value){
            $this->get_field_by_name($name)->set_value($value);
        }
    }
    
    public function update(){
        $this->execute_query_upload_files_and_notify("prepare_update", $this->name." number ".$this->id." updated.", $this->name." number ".$this->id." not updated.");
    }
    
    public function submit(){
        $this->execute_query_upload_files_and_notify("prepare_insert", $this->name." number ".$this->id." submitted.", $this->name." number ".$this->id." not submitted.");
    }
    
    private function execute_query_upload_files_and_notify($query_func, $success, $failure){

        $conn = get_connection($this->connection);

        $query = $this->$query_func();
        $query = ms_escape_string($query);
        $stmt = $conn->prepare($query);

        $params = $this->fields_to_assoc_arr();
        
        if($stmt->execute($params)){
            echo $success."<br>";
            $this->notify($success);
            if(isset($_FILES["file"])){
                $this->upload_files();
            }
        }else{
            echo $failure."<br>";
            echo $query."<br>";
            print_r($stmt->errorInfo());
        }
    }

    private function prepare_insert(){
        $query = "INSERT INTO {$this->table} ";
        $cols = "";
        $vals = "";
        foreach($this->fields as $field){
            if($field->get_name() == $this->primary_key){
                continue;
            }
            $cols .= "[".$field->get_name()."],";
            $vals .= ":".$field->get_name().",";
        }
        $cols = substr($cols, 0, strlen($cols) - 1);
        $vals = substr($vals, 0, strlen($vals) - 1);
        $query .= "(".$cols.") VALUES (".$vals.")";
        return $query;
    }

    private function prepare_update(){
        $query = "UPDATE {$this->table} SET ";
        foreach($this->fields as $field){
            if($field->get_name() == $this->primary_key){
                continue;
            }
            $query .= "[".$field->get_name()."]=:".$field->get_name().",";
        }
        $query = substr($query, 0, strlen($query) - 1);
        $query .= " WHERE {$this->primary_key}={$this->id}";
        return $query;
    }
    
    private function fields_to_assoc_arr(){
        $assoc_arr = array();
        foreach($this->fields as $field){
            if($field->get_name() == $this->primary_key){
                continue;
            }
            $assoc_arr[$field->get_name()] = $field->get_value();
        }
        return $assoc_arr;
    }
    
    private function get_max_id(){
        $conn = get_connection($this->connection);

        $query = "SELECT MAX({$this->primary_key}) FROM {$this->table}";
        $query = ms_escape_string($query);

        if($conn){
            $stmt = $conn->prepare($query);
        }else{
            return -1;
            exit();
        }

        if($stmt->execute()){
            if($row = $stmt->fetch(PDO::FETCH_NUM)){
                return $row[0];
                exit();
            }else{
                return -1;
                exit();
            }
        }else{
            return -1;
            exit();
        }

        return 1;
    }
    
    public function get_field_by_name($name){
        if(count($this->fields) >= 1){
            foreach($this->fields as $field){
                if($field->get_name() == $name){
                    return $field;
                }
            }
        }
        $this->fields[] = (new Field($name, "", $this));
        return end($this->fields);
    }
    
    public function update_fields($assoc_array){
        foreach($this->fields as $field){
            if(isset($assoc_array[$field->get_name()])){
                $field->set_value($assoc_array[$field->get_name()]);
            }
        }
    }
    
    public function update_field_options($render_js = false){
        /*$sort_by_order = function($a, $b){
            return ((float)$a->get_order() - (float)$b->get_order());
        };
        usort($this->fields, $sort_by_order);*/
        foreach($this->fields as $field){
            if($field->query != null){
                for($i = 0, $l = count($field->query->placeholders); $i < $l; ++$i){
                    if($field->query->placeholders[$i][0] == "_"){
                        $pl = $field->query->placeholders[$i];
                        $ref_name = substr($pl, 1, -1);
                        $val = $this->get_field_by_name($ref_name)->get_value();
                        $field->query->params[$i] = $val;
                    }
                }
                $field->get_options();
                if($render_js){
                    $options_html = $field->options_to_html();
                    $js = "var f = new Form();";
                    $js .= "f.updateOptions(getByName('".$field->get_name()."'), '".$options_html."');";
                    echo $js;
                }
            }
        }
    }
    
    /*
    $_FILES["file"]["name"] - the name of the uploaded file
    $_FILES["file"]["type"] - the type of the uploaded file
    $_FILES["file"]["size"] - the size in bytes of the uploaded file
    $_FILES["file"]["tmp_name"] - the name of the temporary copy of the file stored on the server
    $_FILES["file"]["error"] - the error code resulting from the file upload
     */

    private function upload_files(){
        for($i = 0; $i < count($_FILES["file"]["name"]); ++$i) {
            $base_file = basename($_FILES["file"]["name"][$i]);
            if($base_file != ""){
                $fname = "../../uploads/{$this->name}_{$this->id}_{$i}_{$base_file}";
                if(move_uploaded_file($_FILES["file"]["tmp_name"][$i],$fname)){
                    echo "Uploaded file ".$base_file."<br>";
                }else{
                    echo "Error: ".$_FILES["file"]["error"][$i].", Could not upload ".$_FILES["file"]["name"][$i]." to ".$fname."<br>";
                }
            }
        }
    }
    
    function notify($subject){
        $conn = get_connection("Safety");
        $stmt = $conn->prepare("INSERT INTO Emails (Contacts, Subj, Body) VALUES(?, ?, ?)");
        if($stmt->execute(array($this->contacts, $subject, $this->email_body))){
            echo "Email query executed (Email will send in 0-5 minutes)<br>";
        }else{
            echo "Email query not executed<br>";
        }
    }
}

class Field{
    private $name;
    private $type;
    private $value;
    private $format;
    private $options;
    public $query; // Query object
    //private $order;
    private $html;
    //private $parent;
    private $default;
    
    public function __construct($name, $value/*, $parent*/){
        $this->name = $name;
        $this->value = $value;
        //$this->parent = $parent;
    }
    
    public function set_data($type = "text", $format = "", $default = "", $options = array(), $query = null, /*$order = INF,*/ $html = ""){
        $this->type = $type;
        $this->format = $format ?: $type;
        $this->default = $default;
        $this->options = $options;
        $this->query = $query;
        //$this->order = $order;
        $this->html = $html;
    }
    
    public function to_html(){
        // $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
        // use this to insert text where needed
        $input = "";
        $this->value = ($this->value == "") ? $this->default : $this->value;
        $this->format_value();
        if($this->type == "select"){
            $input .= '<'.$this->type.' ';
            $input .= ' name="'.$this->name.'" ';
            if($this->html != ""){
                $input .= $html;
            }
            /*if($this->order != INF){
                $input .= 'data-order="'.$this->order.'" ';
            }*/
            $input .= '>';
            if($this->options != array()){
                $input .= $this->options_to_html();
            }
            $input .= '</'.$this->type.'>';
        }else if($this->type == "textarea"){
            $input .= '<'.$this->type.' ';
            $input .= ' name="'.$this->name.'" ';
            if($this->html != ""){
                $input .= $html;
            }
            $input .= '>';
            $input .= $this->value;
            $input .= '</'.$this->type.'>';
        }else{
            $input = '<input name="'.$this->name.'" type="'.$this->type.'" value="'.$this->value.'" '.$this->html.' />';
        }
        return $input;
    }
    
    public function get_name(){
        return $this->name;
    }
    
    public function set_value($val){
        $this->value = $val;
    }
    
    public function get_value(){
        return $this->value;
    }
    
    public function get_order(){
        return $this->order;
    }
    
    private function format_value(){
        switch($this->format){
            case "date":
                $dt = DateTime::createFromFormat("Y-m-d H:i:s.u", $this->value);
                if($dt){
                    $this->value = $dt->format("Y-m-d");
                }
                break;
            case "time":
                $dt = DateTime::createFromFormat("Y-m-d H:i:s.u", $this->value);
                if($dt){
                    $this->value = $dt->format("H:i:s");
                }
                break;
            case "int":
                $this->value = (int)$this->value;
                break;
            case "float":
                $this->value = (float)$this->value;
                break;
            case "bool":
                $this->value = (bool)(int)$this->value ? "true" : "false";
            default:
                break;
        }
    }
    
    public function get_options(){
        $this->options = $this->query->execute();
    }
    
    public function options_to_html(){
        $options_string = "";
        foreach($this->options as $option){
            $selected_text = ($option == $this->value) ? 'selected="selected"' : '';
            $options_string .= '<option value="'.$option.'" '.$selected_text.'>'.$option.'</option>';
        }
        return $options_string;
    }
}

require_once("Query.php");

?>