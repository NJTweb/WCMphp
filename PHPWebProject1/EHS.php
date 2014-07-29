
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
    <script src="scripts/js/wcm/wcm.js"></script>
    <script src="scripts/js/wcm/form.js"></script>
    
    <script src="scripts/js/forms/checklist.js"></script>
    <script src="scripts/js/wcm/images.js"></script>
    
    <link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/Normalize.css" />
    <link rel="stylesheet" href="css/Checklist.css" />
    <link rel="stylesheet" href="css/EHS.css" />

    <title>EHS Checklist</title>
</head>

<body onload="(new Form('EHS')).open();">
    <form name="EHS" id="EHS" data-name="EHS" data-table="EHS" data-primarykey="ID" data-id="-1" data-connection="Safety" data-email="" 
          data-contacts="hooks@njt-na.com; phelps@njt-na.com; pittam@mayco-mi.com; paul@mayco-mi.com; marshallja@jvisusallc.com; gwilloughby@mayco-mi.com">
        <table>
            <tr>
                <td colspan="4"><h1>EHS No. <input id="ID" type="text" name="ID" value="" style="width: auto; background-color: inherit;" readonly></h1></td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="inputPlusLabel">
                        <strong>Date</strong>
                        <input type="date" name="EHSDate" placeholder="Date created" required>

                    </div>
                    <div class="inputPlusLabel">
                        <strong>Shift</strong>
                        <select name="Shift" data-list='{ "list" : [1,2,3] }' required></select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Auditor's Name</strong>
                        <select name="AuditorName" id="AuditorName" data-query='{"connection" : "Safety", "Query" : "Auditors", "Params" : []}' required>
                        </select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Plant</strong>
                        <select name="Plant" data-query='{"connection" : "Safety", "Query": "Plants", "Params" : []}' data-order="1.0" required>
                        </select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Department</strong>
                        <select name="Department" id="dept" data-query='{"connection" : "Safety", "Query" : "Departments", "Params" : ["$Plant$"]}' data-order="1.1" required></select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Zone</strong>
                        <select name="Zone" id="zone" data-query='{"connection" : "Safety", "Query" : "Zones", "Params" : ["$Plant$", "$Department$"]}' data-order="1.2" required></select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Machine</strong>
                        <select name="MachID" id="mach" data-query='{"connection" : "Safety", "Query" : "Machines", "Params" : ["$Plant$", "$Department$", "$Zone$"]}' data-order="1.3" ></select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Work Cell</strong>
                        <select name="WorkCell" id="workcell" data-query='{"connection" : "Safety", "Query" : "WorkCells", "Params" : ["$Plant$", "$Department$"]}' data-order="1.2" required>
                            <!-- code to update at end of file -->
                        </select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Tool No.</strong>
                        <select name="PartNum" id="tool" data-query='{"connection" : "Mattec", "Query" : "Tools", "Params" : []}' data-con="Mattec" data-format="text">
                        </select>
                    </div>
                    <div class="inputPlusLabel">
                        <strong>Supervisor</strong>
                        <select name="Supervisor" id="supervisor" data-query='{"connection" : "Safety", "Query" : "Supervisors", "Params" : []}' required>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><h2>Requirement</h2></td>
                <td><h2>Select</h2></td>
                <td><h2>Severity</h2></td>
            </tr>
            <?php
                $xml = simplexml_load_file("xml/ehslabels.xml");
                $i = 0;
                foreach($xml->group as $groupValues):
                    foreach($groupValues as $type => $text):
                        if($type == "header"):
            ?>
            <tr class="header"><td colspan="4"><h3><?php echo $text; ?></h3></td></tr>
            <?php
                        else:
                            $i+=1;
            ?>
            <tr>
                <td>
                    <span><?php echo $i.". ".$text; ?></span>
                </td>
                <td>
                    <label for="file<?php echo $i; ?>">
                        <img id="uploadimg<?php echo $i; ?>" src="res/upload.png" alt="Upload">
                    </label>
                    <input type="file" id="file<?php echo $i; ?>" onchange="changeImage($(this), $('#uploadimg<?php echo $i; ?>')); readImage(this, <?php echo "'FileURL".$i."'"; ?>);">
                    <input type="hidden" name="FileURL<?php echo $i; ?>" value="" />
                </td>
                <td>
                    <select name="Compliant<?php echo $i; ?>" data-list='{"list" : ["Satisfactory","Unsafe Condition","Unsafe Act","Both"]}' data-default="Satisfactory" required></select>
                </td>
                <td>
                    <select name="Severity<?php echo $i; ?>" data-list='{"list" : ["Low","Medium","High"]}' data-default="Low" ></select>
                </td>
            </tr>
            <?php
                        endif;
                    endforeach;
                endforeach;
            ?>
            <tr>
                <td colspan="4"><h3>Comments</h3></td>
            </tr>
            <tr></tr> <!-- makes sure the next (last) row is blue, becaue of the nth-of-type rule-->
            <tr>
                <td colspan="3">
                    <textarea maxlength="1000" name="Comments" rows="6" cols="50" form="EHS" placeholder="Comments."></textarea><br />
                </td>
                <td>
                    <button type="button" id="update" onclick="userUpdate('EHS');">Update</button>
                    <button type="button" id="submit" onclick="userSubmit('EHS');">Submit</button>
                    <button type="button" onclick="location.reload();">Clear</button>
                    <button type="button" onclick="userOpen('EHS');">Open</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>