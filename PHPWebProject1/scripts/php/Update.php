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

$query = buildQuery($values, $obj->table, $obj->ID, $obj->primaryKey);

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
		<p>Successfully updated.</p>
<?php
    notify($obj->contacts, "{$obj->name} number {$obj->ID} updated.", "A(n) {$obj->name} was updated.".$obj->emailBody, $conn);
else:
?>
    <body>
		<p>Submission unsuccessful.</p>
<?php
    print_r($stmt->errorInfo());
    echo "<br><br>".$query;
    echo "<br><br>";
    print_r($values);
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
function buildQuery($fields, $table, $id, $pk){
    
    $columns = "";
    $values = "";
    $query = "UPDATE ".$table." SET ";
    foreach($fields as $name => $value):
        $query .= "[".$name."]=:".$name.", ";//$value.", ";
    endforeach;
    $query = substr($query, 0, -2);
    $query .= " WHERE {$pk}={$id}";
    
    return $query;
}

?>