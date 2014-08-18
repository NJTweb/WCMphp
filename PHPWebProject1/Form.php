<?php
require_once("/scripts/php/Form.php");
$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$f = new Form("Development Form","DevForm","ID",$id,"Safety","hooks@njt-na.com"," ");
$f->open();
$f->get_field_by_name("ID")->set_data("text","","",array(),null,INF,'style="width: auto; background-color: inherit;" readonly');
$f->get_field_by_name("ToolName")->set_data("text");
$f->get_field_by_name("Problems")->set_data("textarea");
$f->get_field_by_name("ToolNumber")->set_data("select","","",array(),new Query("Tools",array(),PDO::FETCH_NUM));
$f->get_field_by_name("Plant")->set_data("select","","",array(),new Query("Plants",array(),PDO::FETCH_NUM));
$f->get_field_by_name("Department")->set_data("select","","",array(),new Query("Departments",array("_Plant_"),PDO::FETCH_NUM));
$f->get_field_by_name("Zone")->set_data("select","","",array(),new Query("Zones",array("_Plant_", "_Department_"),PDO::FETCH_NUM));
$f->get_field_by_name("Machine")->set_data("select","","",array(),new Query("Machines",array("_Plant_", "_Department_","_Zone_"),PDO::FETCH_NUM));
$f->get_field_by_name("ReportedBy")->set_data("text","","Justin Thomason");
$f->get_field_by_name("DateShipped")->set_data("date");
$f->get_field_by_name("RepairedLocation")->set_data("select","","",array("Mayco","Mound"));
$f->get_field_by_name("Notes")->set_data("text");
$f->get_field_by_name("DateReceived")->set_data("date");
$f->get_field_by_name("IsService")->set_data("select","bool","false",array("true","false"));
$f->get_field_by_name("DateTimeOccurred")->set_data("hidden");
$f->get_field_by_name("DateTimeStarted")->set_data("hidden");
$f->get_field_by_name("DateTimeCompleted")->set_data("hidden");
$f->get_field_by_name("FileURL1")->set_data("hidden", "", "", array(), null, 'class="fileURL"');
$f->update_field_options();
?>

<!DOCTYPE html>
<html>
<head>
    <link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
    <script src="scripts/js/wcm/wcm.js"></script>
    <script src="scripts/js/wcm/form.js"></script>
    
    <script src="scripts/js/forms/EWO.js"></script> 
    <script src="scripts/js/forms/checklist.js"></script>

    <link rel="stylesheet" href="css/Normalize.css" />
    <link rel="stylesheet" href="css/Checklist.css" />
    <link rel="stylesheet" href="css/WCC.css" />

    <title>WCM Safety Checklist</title>
</head>
<body onload="splitDateTimes()">
    <form name="Test" enctype="multipart/form-data" method="post">
        
        <?php
        echo $f->get_field_by_name("DateTimeOccurred")->to_html();
        echo $f->get_field_by_name("DateTimeStarted")->to_html();
        echo $f->get_field_by_name("DateTimeCompleted")->to_html();
        ?>
        
        <table>           
            <tr><td colspan="3"><h1>Development Form No. <?php echo $f->get_field_by_name("ID")->to_html(); ?></h1></td></tr>
            <tr><td>
            <input type="date" id="dateocc" onchange="concatDateTimes()" /> <!-- date occurred -->
            <input type="time" id="timeocc" onchange="concatDateTimes()" />
            <input type="date" id="datestart" onchange="concatDateTimes()" /> <!-- date started -->
            <input type="time" id="timestart" onchange="concatDateTimes()" />
            <input type="date" id="datecomp" onchange="concatDateTimes()" /> <!-- date completed -->
            <input type="time" id="timecomp" onchange="concatDateTimes()" />
            <?php
            echo $f->get_field_by_name("ToolName")->to_html();
            echo $f->get_field_by_name("Problems")->to_html();
            echo $f->get_field_by_name("ToolNumber")->to_html();
            echo $f->get_field_by_name("Plant")->to_html();
            echo $f->get_field_by_name("Department")->to_html();
            echo $f->get_field_by_name("Zone")->to_html();
            echo $f->get_field_by_name("Machine")->to_html();
            echo $f->get_field_by_name("ReportedBy")->to_html();
            echo $f->get_field_by_name("DateShipped")->to_html();
            echo $f->get_field_by_name("RepairedLocation")->to_html();
            echo $f->get_field_by_name("Notes")->to_html();
            echo $f->get_field_by_name("DateReceived")->to_html();
            echo $f->get_field_by_name("IsService")->to_html();
            ?>
            <input type="file" />
            <?php echo $f->get_field_by_name("FileURL1")->to_html(); ?>
            </td></tr>
            <tr>
                <td>
                    <button type="button" onclick="(new Form())<?php echo $f->has_record ? ".update()" : ".submit()"; ?>"><?php echo $f->has_record ? "Update" : "Submit"; ?></button>
                    <button type="button" onclick="location.href = getNoParamsURL();">Clear</button>
                    <button type="button" onclick="(new Form()).open();">Open</button>
                </td>
            </tr>
        </table>
        </form>
    </body>
</html>