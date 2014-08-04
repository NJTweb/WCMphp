$(document).ready(initializeFields);

function initializeFields() {
    var inputs = getOrderedInputs();
    inputs.each(
        function () {
            setChangeEvent($(this));
            getOptions($(this));
        });
    getByName("file[]").change(function () {

        if (checkTotalFileSize() > 7884800) {
            alert("Files are too large.");
            resetFormElement(this);
        }

    });
}

var totalSize = 0;
function checkTotalFileSize() {
    totalSize = 0;
    getByName("file[]").each(function () {
        if (this.files[0] != undefined) {
            totalSize += (this.files[0].size);
        }
    });
    //console.log(totalSize);
    return totalSize;
}

function resetFormElement(e) {
    $(e).wrap('<form>').closest('form').get(0).reset();
    $(e).unwrap();
    //console.log(e.files[0]);
}
function open() {
    var con = getFormData("Connection").val();
    var table = getFormData("Table").val();
    var pk = getFormData("PrimaryKey").val();
    var id = getFormData("ID").val();

    $.ajax({
        url: "scripts/php/Open.php",
        type: "POST",
        dataType: "json",
        data: {
            Connection: con,
            Table: table,
            PrimaryKey: pk,
            ID: id
        },
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                //console.log(data);
                setData(data);
            }
        },
        error: logError
    });
}

function getNextID() {
    var con = getFormData("Connection").val();
    var table = getFormData("Table").val();
    var pk = getFormData("PrimaryKey").val();

    $.ajax({
        url: "scripts/php/getMaxID.php",
        type: "POST",
        dataType: "json",
        data: {
            Connection: con,
            Table: table,
            PrimaryKey: pk
        },
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                ++data;
                setID(data);
            }
        },
        error: logError
    });
}

function setID(id) {
    var pk = getFormData("PrimaryKey").val();
    getByName(pk).val(id);
    getFormData("ID").val(id);
}

function setData(data) {
    //console.log(data);
    if (data.length == 0) {
        $("#submit").text("Submit");
        $("form").attr("action", "scripts/php/Submit.php");
        //TODO change form to be more specific
        setDefaults();
        getNextID();
    } else {
        dataToFields(data);
        $("#submit").text("Update");
        $("form").attr("action", "scripts/php/Update.php");
    }
}

function setDefaults() {
    //console.log("Called");
    var inputs = getOrderedInputs();
    inputs.each(
        function () {
            var def = $(this).attr("data-default");
            if (def != undefined) {
                $(this).val(def);
                //console.log(def);
            } else {
                $(this).val("");
            }
        });
}

function dataToFields(data) {
    var inputs = getOrderedInputs();
    inputs.each(
        function () {
            try {
                var thisKey = $(this).attr("name"); //inputs[i].name;
                var thisType = $(this).attr("type") || "text";
                var value = formatValue(data[thisKey]["type"], thisType, data[thisKey]["value"]);
                //console.log("Changing " + thisKey + " to " + value);
                $(this).val(value).change();
            } catch (e) {
                try{
                    //console.log(e.message + " (Name: " + thisKey + ", Value: " + value + ")");
                } catch (e) {
                    //console.log(e.message + " (Name: " + thisKey + ")");
                }
            }
        });
}

function getOrderedInputs() {
    var inputs = $("select, textarea, input").filter("[name]").not("[name*='FormData']");
    inputs.sort(function (a, b) { return +a.getAttribute("data-order") - +b.getAttribute("data-order"); });
    return inputs;
}

function formatValue(sqlType, htmlType, value) {
    switch (sqlType) {
        case "datetime":
            switch (htmlType) {
                case "date":
                    value = value[0];
                    break;
                case "time":
                    value = value[1];
                    break;
                default:
                    value = value[0] + " " + value[1];
                    break;
            }
            break;
        case "varchar":
        case "nvarchar":
        case "text":
        case "ntext":
            break;
        case "int":
            value = parseInt(value);
            break;
        case "float":
            value = parseFloat(value);
            break;
        case "bit":
            value = Boolean(parseInt(value));
            break;
    }
    return value;
}

function getOptions(jqEl) {
    //console.log(jqEl);
    if (jqEl.attr("data-list") != undefined) {
        var listObj = $.parseJSON(jqEl.attr("data-list"));
        var list = listObj["list"];
        appendOptions(jqEl, list);
    } else if (jqEl.attr("data-query") != undefined) {
        var query = $.parseJSON(jqEl.attr("data-query"));
        for (var i = 0, l = query.Params.length; i < l; ++i) {
            //if the parameter is a reference (contains $)
            if (String(query.Params[i]).indexOf("$") != -1) {
                var thisParam = query.Params[i];
                //remove the $ from each side of the string
                var refName = thisParam.substring(1, thisParam.length - 1);
                var refVal = $("[name='" + refName + "']").val();
                query.Params[i] = refVal;
            }
        }
        var qdata = {
            Query: query,
            fetchType: "NUM"
        };
        //console.log(qdata);
        $.ajax({
            url: "scripts/php/Query.php",
            type: "POST",
            async: false,
            dataType: "json",
            data: { "Object": JSON.stringify(qdata) },
            context: jqEl,
            success: function (data, status, xhr) {
                if (status == "success" && xhr.readyState == 4) {
                    appendOptions($(this), data);
                }
            },
            error: logError
        });
    }
}

function appendOptions(jqEl, list) {
    jqEl.html("<option value=''>" + jqEl.attr("name") + "</option>");
    for (var i = 0, l = list.length; i < l; ++i) {
        jqEl.append("<option value='" + list[i] + "'>" + list[i] + "</option>");
    }
}

// set the change event so that any dependent elements (+0.1 in the order)
// will update when this elements value changes
// i.e. if an element with data-order 1.0 is changed
// all elements with data-order 1.1 will update their values
function setChangeEvent(jqEl) {
    var order = jqEl.attr("data-order");
    if (order != undefined) {
        var dependents = $("[data-order='" + (parseFloat(order) + 0.1).toFixed(1) + "']");
        //console.log(dependents);
        dependents.each(
            function () {
                jqEl.change(
                    { jqEl: $(this) },
                    function (event) {
                        //console.log("Changing " + event.data.jqEl.attr("name"));
                        getOptions(event.data.jqEl);
                    });
            });
    }
}


// GLOBAL

function userOpen() {
    var ID = prompt("Enter a form number");
    getFormData("ID").val(ID);
    open();
}

function getFormData(attr) {
    return getByName("FormData[" + attr + "]");
}