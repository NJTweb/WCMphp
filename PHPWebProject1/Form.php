<!DOCTYPE html>
<html>
<head>
    <link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
    <script src="scripts/js/wcm/wcm.js"></script>
    <script src="scripts/js/wcm/form.js"></script>
    
    <script src="scripts/js/forms/checklist.js"></script>
    <script src="scripts/js/forms/ToolRepair.js"></script>
    <script src="scripts/js/forms/EWO.js"></script>
    <!-- <script src="scripts/js/wcm/images.js"></script> -->

    <link rel="stylesheet" href="css/Normalize.css" />
    <link rel="stylesheet" href="css/Checklist.css" />
    <link rel="stylesheet" href="css/WCC.css" />

    <title>WCM Safety Checklist</title>
</head>
<body onload="open()">
    <form name="Test" enctype="multipart/form-data" method="post">
        <input type="hidden" name="FormData[Name]" value="TEST" />
        <input type="hidden" name="FormData[Table]" value="DevForm" />
        <input type="hidden" name="FormData[PrimaryKey]" value="ID" />
        <input type="hidden" name="FormData[ID]" value="-1" />
        <input type="hidden" name="FormData[Connection]" value="Safety" />
        <input type="hidden" name="FormData[Contacts]" value="hooks@njt-na.com; marshallja@jvisusallc.com; gwilloughby@mayco-mi.com" />
        <input type="hidden" name="FormData[EmailBody]" value="" />
        
        <input type="hidden" name="DateTimeOccurred" onchange="splitDateTimes()" />
        <input type="hidden" name="DateTimeStarted" onchange="splitDateTimes()" />
        <input type="hidden" name="DateTimeCompleted" onchange="splitDateTimes()" />
        
        <table>
            
<tr><td colspan="3"><h1>Development Form No. <input id="ID" type="text" name="ID" value="" style="width: auto; background-color: inherit;" readonly></h1></td></tr>
<tr><td>
<input type="date" id="dateocc" onchange="concatDateTimes()" /> <!-- date occurred -->
<input type="time" id="timeocc" onchange="concatDateTimes()" />
<input type="date" id="datestart" onchange="concatDateTimes()" /><!-- date started -->
<input type="time" id="timestart" onchange="concatDateTimes()" />
<input type="date" id="datecomp" onchange="concatDateTimes()" /><!-- date completed -->
<input type="time" id="timecomp" onchange="concatDateTimes()" />
<input type="text" name="ToolName" />
<textarea name="Problems"></textarea>
<select name="ToolNumber" data-query='{ "Query" : "Tools", "Params" : []}' data-con="Mattec" required></select>
<select name="Plant" data-query='{ "Query": "Plants", "Params" : []}' data-order="1.0" required></select>
<select name="Department" data-query='{ "Query" : "Departments", "Params" : ["Merrill"]}' data-order="1.0" required></select>
<select name="Zone" data-query='{ "Query" : "Zones", "Params" : ["Merrill", "$Department$"]}' data-order="1.1" required></select>
<select name="Machine" data-query='{ "Query" : "Machines", "Params" : ["Merrill", "$Department$", "$Zone$"]}' data-order="1.2"></select>
<input type="text" name="ReportedBy" value="Thomason, Justin" />
<input type="date" name="DateShipped" />
<select name="RepairedLocation" data-list='{ "list" : ["Mayco","Mound"] }'></select>
<input type="text" name="Notes" /> 
<input type="date" name="DateReceived" />
<select name="IsService" data-list='{ "list" : [true,false] }' data-default="false" data-format="bool"></select>
</td></tr>
            
<?php
                $xml = simplexml_load_file("xml/wcclabels.xml");
                $i = 0;
                foreach($xml->group as $groupValues):
                    foreach($groupValues as $type => $text):
                        if($type == "header"):
            ?>
            <tr class="header"><td colspan="3"><h3><?php echo $text; ?></h3></td></tr>
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
                    <input type="file" name="file[]" id="file<?php echo $i; ?>" onchange="changeImage($(this), $('#uploadimg<?php echo $i; ?>')); ">
                    <!-- <input type="hidden" id="FileURL<?php //echo $i; ?>" value="" /> -->
                </td>
                <td>
                    <select name="Compliant<?php echo $i; ?>" data-list='{"list" : ["Satisfactory","Unsafe Condition","Unsafe Act","Both"]}' data-default="Satisfactory" required></select>
                </td>
            </tr>
            <?php
                        endif;
                    endforeach;
                endforeach;
            ?>
            <td>
                <button type="submit" id="submit">Submit</button>
                <button type="button" onclick="location.reload();">Clear</button>
                <button type="button" onclick="userOpen();">Open</button>
            </td>
        </table>
    </body>
</html>