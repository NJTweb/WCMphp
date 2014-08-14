function getParams(query) {
    $.ajax({
        url: "scripts/php/getParams.php",
        type: "POST",
        dataType: "json",
        data: { Query: query },
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                renderParamInputs(data);
            }
        },
        error: logError
    });
}

function renderParamInputs(data) {
    var pDiv = $("#ParamDiv");
    pDiv.html("");
    if (data.parameter != undefined) {
        for (var i = 0; i < data.parameter.length; ++i) {
            var name = data.parameter[i]["@attributes"].name;
            var type = data.parameter[i].type;
            pDiv.append("<label>" + name + "</label><input type='" + type + "' name='Params[]' placeholder='" + name + "' />");
        }
    }
}

function chartData() {
    var params = [];
    $("[name='Params[]']").each(function () {
        params.push($(this).val());
    });
    var query = $("[name='Query']").val();

    // var qry = new Query(query, params, "ASSOC", "Safety");
    // build your own query. query obj removed

    //ajaxPostJSONjQuery("scripts/php/Query.php", qry, renderChartData, true);

    var qry = {
        Query : {
            Query: query,
            Params: params
        },
        fetchType: "ASSOC"
    }

    console.log(qry);

    $.ajax({
        url: "scripts/php/Query.php",
        type: "POST",
        dataType: "json",
        data: {Object : JSON.stringify(qry)},
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                renderChartData(data);
            }
        },
        error: logError
    });
}

function randomRGB(){
    var rint = Math.round(0xffffff * Math.random());
    return 'rgb(' + (rint >> 16) + ',' + (rint >> 8 & 255) + ',' + (rint & 255) + ')';
}

function renderChartData(data) {

    getByName("Data").val("<pre>" + JSON.stringify(data, null, "\t") + "</pre>");

    switch (Object.keys(data[0]).length) {
        case 2:
            renderBarChart(data);
            break;
        case 3:
            renderStackedChart(data);
            break;
    }
}

function renderBarChart(data) {
    data.sort(sortResults);
    var objKeys = Object.keys(data[0]);

    var fieldInfo = {};
    var dataTable = new google.visualization.DataTable();

    var mainKey = objKeys[0];   // key containing values for the first column
    var valueKey = objKeys[1];  // key containing values for the datatable
    //console.log(fieldInfo);

    dataTable.addColumn("string", mainKey);
    dataTable.addColumn("number", valueKey);

    for (var i = 0, j = data.length; i < j; ++i) {
        var thisVal = data[i][valueKey];
        thisVal = Number(thisVal);
        dataTable.addRow([data[i][mainKey], thisVal]);
    }

    //console.log(dataTable);

    var options = {
        isStacked: false
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chartDiv"));
    chart.draw(dataTable, options);
}

function renderStackedChart(data) {
    /*
    [
    ["PRESS", "ColorChg", "DIE SET", ...],
    ["PR01", 50, 70, ...]
    ]

    find out which field actually has your data (numeric field)
        this is automatically the only numeric field
    find out which field has the main labels (string field with the most unique values)
        get the unique values of the object array key values
        count them
        whichever string column has the most is our main

    */
    //console.log(data);
    //data.sort(sortResults);     // find a way to order by totals for each press

    var objKeys = Object.keys(data[0]);

    var mainKey = objKeys[0];   // key containing values for the first column
    var secondKey = objKeys[1]; // key containing values for the other columns
    var valueKey = objKeys[2];  // key containing values for the datatable

    var dataTable = new google.visualization.DataTable();

    dataTable.addColumn("string", mainKey);

    // returns only the unique values identified in the list of objects by the second key
    // i.e. the column names other than the first column name (mainKey)
    var cols = objectArrayKeyValues(secondKey, data).filter(onlyUnique);
    for (var i = 0, j = cols.length; i < j; ++i) {
        dataTable.addColumn("number", cols[i]);
    }

    //console.log(cols);

    // returns only the unique values idenitified in the list of objects by the first key
    // i.e. the values for the first column
    var firstColVals = objectArrayKeyValues(mainKey, data).filter(onlyUnique);

    var totals = [];
    for (var i = 0, j = firstColVals.length; i < j; ++i) {
        var total = 0
        for (var k = 0, l = data.length; k < l; ++k) {
            if (data[k][mainKey] == firstColVals[i]) {
                var thisVal = Number(data[k][valueKey]);
                total += thisVal;
            }
        }
        var totalObj = {
            col: firstColVals[i],
            total : total
        };
        totals.push(totalObj);
    }

    totals.sort(
        function (a, b) {
           return a.total - b.total;
        }
    );

    firstColVals = objectArrayKeyValues("col", totals);

    //console.log(firstColVals);

    for (var i = 0, j = firstColVals.length; i < j; ++i) {
        // set value for the first column
        var thisData = [firstColVals[i]];
        for (var m = 0, n = cols.length; m < n; ++m) {
            var foundVal = false;
            for (var k = 0, l = data.length; k < l; ++k) {
                if (data[k][mainKey] == firstColVals[i] && data[k][secondKey] == cols[m]) {
                    //console.log(data[k][mainKey] + ", " + firstColVals[i] + ", " + data[k][secondKey] + ", " + cols[m] + ", value is: " + data[i][valueKey]);
                    var thisVal = data[k][valueKey];
                    thisData.push(Number(thisVal));
                    foundVal = true;
                }
            }
            if (!foundVal) {
                //console.log("No data found");
                thisData.push(0);
            }
        }
        dataTable.addRow(thisData);
    }

    //console.log(dataTable);

    var chartData = dataTable;

    var options = {
        isStacked: true
    };

    var chart = new google.visualization.ColumnChart(document.getElementById("chartDiv"));
    chart.draw(chartData, options);
}

function sortResults(a, b) {
    var firstKey = Object.keys(a).pop();
    return (a[firstKey] - b[firstKey]);
}

function objectArrayKeyValues(key, objs) {
    var results = [];
    for (var i = 0, l = objs.length; i < l; ++i) {
        results.push(objs[i][key]);
    }
    return results;
}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

function getUniqueKeyVals(data) {
    var uniqueObj = {};
    var objKeys = Object.keys(data[0]);
    for (var k = 0, l = objKeys.length; k < l; ++k) {
        var keyVals = objectArrayKeyValues(objKeys[k], data);
        var uniqueVals = keyVals.filter(onlyUnique);
        var notNumeric = arrIsNaN(uniqueVals);
        uniqueObj[objKeys[k]] = {};
        uniqueObj[objKeys[k]]["values"] = uniqueVals;
        uniqueObj[objKeys[k]]["type"] = notNumeric ? "string" : "number";
    }
    return uniqueObj;
}

function arrIsAN(arr){
    var tempArr = arr;
    arr = arr.filter(
        function (el) {
            return !isNaN(el);
        });
    var numeric = (arr.length == tempArr.length);
    return numeric;
}

function logError(xhr, status, error) {
    console.log(status + " : " + error);
    console.log(this);
}