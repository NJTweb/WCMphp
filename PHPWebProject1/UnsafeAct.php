<?php
require_once("scripts/php/Form.php");

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$f = new Form("Unsafe Act", "UnsafeActs", "ID", $id, "Safety", "hooks@njt-na.com; phelps@njt-na.com; pittam@mayco-mi.com; paul@mayco-mi.com; marshallja@jvisusallc.com; gwilloughby@mayco-mi.com", "");
$f->open();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
    <script src="scripts/js/wcm/wcm.js"></script>
    <script src="scripts/js/wcm/form.js"></script>

    <style>
        input, select, textarea, label {
            width: 100%;
            height: 50px;
        }
        textarea {
            height: 200px;
        }
        h2, h1 {
            width: 100%;
            font-family: Calibri;
            text-align: center;
        }
        input[name='ID'] {
            width: 10%;
            margin-left: 45%;
            margin-right: 454%;
        }
    </style>

    <title>Unsafe Act</title>
</head>

<body>
    <form name="UA" enctype="multipart/form-data" method="post"  <?php echo 'action="scripts/php/'.($f->has_record ? "Update": "Submit").'.php"';  ?>>
        <h1>Unsafe Act Form</h1>
        <h2>UA Number <?php $f->get_field_html("ID", "text","","","","",INF,'style="font: inherit; width: auto; background-color: inherit; border: none;" readonly'); ?></h2>
        <label>Plant</label>
        <?php $f->get_field_html("Plant", "select","","","",array("Query" => "Plants", "Params" => array()), 1.0); ?>
        <label>Department</label>
        <?php $f->get_field_html("Department", "select","","","",array("Query" => "Departments", "Params" => array("_Plant_")), 1.1); ?>
        <label>Zone</label>
        <?php $f->get_field_html("Zone", "select","","","",array("Query" => "Zones", "Params" => array("_Plant_", "_Department_")), 1.2); ?>
        <label>Shift</label>
        <?php $f->get_field_html("Shift", "select","","",array(1,2,3)); ?>
        <label>Type</label>
        <?php $f->get_field_html("UAType", "select","","","",array("Query" => "UATypes", "Params" => array()), 2.0); ?>
        <label>Micro Type</label>
        <?php $f->get_field_html("UATypeMicro", "select","","","",array("Query" => "UAMicroTypes", "Params" => array("_UAType_")), 2.1); ?>
        <label>Reviewed</label>
        <?php $f->get_field_html("Reviewed", "select","false","bool",array("true","false")); ?>
        <label>Comments</label>
        <?php $f->get_field_html("Comments", "textarea"); ?>
        <button type="submit" id="submit"><?php echo ($f->has_record ? "Update": "Submit"); ?></button>
        <button type="button" onclick="location.href = location.href.split('?')[0]">Clear</button>
        <button type="button" onclick="location.href = location.href.split('?')[0] + '?id=' + prompt('Enter the ID of the form to open'); ">Open</button>
    </form>
</body>