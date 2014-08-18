<!doctype html>
<html>
	<head>
		<title>Bar Chart</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
        <script src="/scripts/js/wcm/wcm.js"></script>
        <script src="/scripts/js/chart/chartFuncs.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", { packages: ["corechart"] });
        </script>
	</head>
	<body>
        <select name="Query" onchange="getParams($(this).val())">
            <option value=""></option>
            <?php if(isset($_GET["Pillar"])){ ?>
                <?php if($_GET["Pillar"] == "PM"){ ?>
            <option value="MachMTBF">MTBF by Machine</option>
            <option value="MachMTTR">MTTR by Machine</option>
            <option value="MachDowntime">Downtime Reasons by Machine</option>
            <option value="MachScrap">Machine Scrap</option>
            <option value="MachOEE">Machine OEE</option>
                <?php }?>
                <?php if($_GET["Pillar"] == "TR"){ ?>
            <option value="MoldOEE">Tool OEE</option>
            <option value="PMDue">PM Due per Tool</option>
                <?php } ?>
                <?php if($_GET["Pillar"] == "SA"){ ?>
            <option value="AuditsPerAuditor">Number of Audits by Auditor</option>
            <option value="AuditsPerWorkCell">Number of Audits by Work Cell</option>
            <option value="AuditsPerZone">Number of Audits by Zone</option>
            <option value="AgingReport">Days Open per Issue</option>
            <option value="SeverityReport">Number of Issues By Severity</option>
                <?php } ?>
            <?php } ?>
        </select>
        <div id="ParamDiv"></div>
        <button onclick="chartData()">Chart</button>
        <input type="hidden" name="Data" value="" />
        <button onclick="download(getByName('Query').val() + '.csv',getByName('Data').val())">Export Data</button>
        <button id="print">Printable Chart</button>
		<div id="chartDiv" style="width: 100%; height: 500px;">
		</div>
	</body>
</html>