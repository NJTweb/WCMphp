<?php 
require_once("scripts/php/Form.php");

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$f = new Form("Blue Tag", "BlueTags", "ID", $id, "Safety", "hooks@njt-na.com; phelps@njt-na.com; pittam@mayco-mi.com; paul@mayco-mi.com; marshallja@jvisusallc.com; gwilloughby@mayco-mi.com", "");
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
    

    <link rel="stylesheet" href="css/Normalize.css" />
    <link rel="stylesheet" href="css/FormClasses.css" />
    <link rel="stylesheet" href="css/h3normalize.css" />

    <title>Blue Tag</title>
</head>

<body>
    <form name="BlueTag" enctype="multipart/form-data" method="post" <?php echo 'action="scripts/php/'.($f->has_record ? "Update": "Submit").'.php"';  ?>>
        <table>
            <tr>
                <td colspan="5" class="dkblue whitetext">
                    <h1>Blue Tag Number </h1>
                    <?php $f->get_field_html("ID", "text","","","","",INF,'style="width: auto; background-color: inherit;" readonly'); ?>
                </td>
            </tr>
            <tr>
                <td class="ltgrey">Tag#</td>
                <td class="ltgrey">WO#</td>
                <td class="ltgrey">Tag Type</td>
                <td class="ltgrey">Name</td>
            </tr>
            <tr>
                <td><input type="number" name="TagNo" placeholder="Tag Number" value="" required="required" /></td>
                <td><input type="number" name="WONo" value="" /></td>
                <td>
                    <?php $f->get_field_html("TagType","select","","",array("AM","WO")); ?>
                </td>
                <td><?php $f->get_field_html("Name","text"); ?></td>
            </tr>
            <tr>
                <td class="ltgrey">Date Opened</td>
                <td class="ltgrey">Shift</td>
                <td class="ltgrey">Department</td>
                <td class="ltgrey">Zone</td>
            </tr>
            <tr>
                <td><?php $f->get_field_html("OpenDate", "date"); ?></td>
                <td>
                    <?php $f->get_field_html("Shift", "select","","int",array(1,2,3)); ?>
                </td>
                <td>
                    <?php $f->get_field_html("Department", "select","","","",array("Query" => "Departments", "Params" => array("Merrill")), 1.0); ?>
                </td>
                <td>
                    <?php $f->get_field_html("Zone", "select","","","",array("Query" => "Zones", "Params" => array("Merrill", "_Department_")), 1.1); ?>
                </td>
            </tr>
            <tr>
                <td class="ltgrey">Machine</td>
                <td class="ltgrey">Problem Type</td>
                <td class="ltgrey">Date Completed</td>
                <td class="ltgrey">Completed By</td>
            </tr>
            <tr>
                <td>
                    <?php $f->get_field_html("Machine", "select","","","",array("Query" => "Machines", "Params" => array("Merrill", "_Department_","_Zone_")), 1.2); ?>
                </td>
                <td>
                    <?php $f->get_field_html("ProblemType", "select","","",array("Oil leak","Water leak","Grease/lube leak","Air leak","Contamination","Out of operating range","Vibration","Wrong temperature","Wrong pressure","Difficult to lube","Difficult to clean","Irregular function","Loose/missing fastener","Other")); ?>
                </td>
                <td><?php $f->get_field_html("CompletedDate", "date"); ?></td>
                <td><?php $f->get_field_html("CompletedBy","text"); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="ltgrey">Details</td>
            </tr>
            <tr>
                <td colspan="4" height="100"><input type="text" name="Details" placeholder="Details" value="" /></td>
            </tr>
            <tr>
                <td colspan="4">
                    <button type="submit" id="submit"><?php echo ($f->has_record ? "Update": "Submit"); ?></button>
                    <button type="button" onclick="location.href = location.href.split('?')[0]">Clear</button>
                    <button type="button" onclick="location.href = location.href.split('?')[0] + '?id=' + prompt('Enter the ID of the form to open'); ">Open</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>