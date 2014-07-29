<?php

require_once("ConInfo.php");
require_once("Utilities.php");

$obj = getObject($_POST["Object"]);
$conn = getPDO($obj->connection);

$values = array();
foreach($obj->fields as $field){
    $values[$field->name] = $field->value;
}
unset($values[$obj->primaryKey]);

$query = buildQuery($values, $obj->table);

$stmt = $conn->prepare($query);

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Submission page</title>
	</head>
<?php
if($stmt->execute($values)):
?>
	<body>
		<p>Successfully submitted.</p>
<?php
    notify($obj->contacts, "{$obj->name} number {$obj->ID} submitted.", "A(n) {$obj->name} was submitted.".$obj->emailBody, $conn);
else:
?>
    <body>
		<p>Submission unsuccessful.</p>
<?php
    print_r($stmt->errorInfo());
endif;
?>
	</body>
</html>

<?php
/**
 * Builds a SELECT or INSERT query using the POST-ed values
 * @param array $postVars The POST-ed values, without the FormData array
 * @param string $form The name of table in the database
 * @param int $id The ID of the record being searched for
 * @param string $pk The name of the primary key column in the table
 * @param bool $recExists Whether or not this record is already in the table
 * @return string The parameterized query
 */
function buildQuery($fields, $table){
    
    $columns = "";
    $values = "";
    $query = "INSERT INTO ".$table;
    foreach($fields as $name => $value):
        $columns .= "[".$name."], ";
        $values .= ":".$name.", ";
    endforeach;
    $columns = substr($columns, 0, -2);
    $values = substr($values, 0, -2);
    $query .= "({$columns}) VALUES({$values})";
    
    return $query;
}

?>