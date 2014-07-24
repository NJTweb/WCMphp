/*
* FORM CLASS
* opens form into form element
* allows updating and submitting of form
* models an html form, adds functionality
* uses an array of Field objects to make changes
* initialized with form name (used for emails), table,
* primary key field name, and the id of the current record
*/
function Form(name) {
    this.rawObj = {};
    this.fields = [];
    this.name = name;
    this.table = getByName(name).attr("data-table");
    this.primaryKey = getByName(name).attr("data-primaryKey");
    this.ID = getByName(name).attr("data-id");
    this.connection = getByName(name).attr("data-connection");
    this.initializeFields();
    this.emailBody = getByName(name).attr("data-email");
    this.contacts = getByName(name).attr("data-contacts");
}

Form.prototype.initializeFields = function () {
    var fieldNames = $("select, textarea, input").map(function () { return $(this).attr("name"); }).get();
    for (var i = 0, l = fieldNames.length; i < l; ++i) {
        this.fields.push(new Field(fieldNames[i], $("[name='" + fieldNames[i] + "']").val()));
    }
};

Form.prototype.open = function () {
    ajaxPostJSONjQuery("scripts/php/Open.php", this, "setData", true);
};

Form.prototype.setData = function (data) {
    this.rawObj = data;
    this.rawObjToFields();
    this.updateFields();
    if (this.rawObj.length == 0) {
        $("#update").hide();
        $("#submit").show();
        this.getMaxID();
    } else {
        $("#submit").hide();
        $("#update").show();
    }
};

Form.prototype.update = function () {
    ajaxPostHTMLjQuery("scripts/php/Update.php", this, "render", true);
};

Form.prototype.submit = function () {
    ajaxPostHTMLjQuery("scripts/php/Submit.php", this, "render", true);
};

Form.prototype.getMaxID = function () {
    ajaxPostJSONjQuery("scripts/php/getMaxID.php", this, "setMaxID", true);
};

Form.prototype.setMaxID = function (data) {
    getByName(this.primaryKey).val(++data[0]);
};

Form.prototype.rawObjToFields = function () {
    for (col in this.rawObj[0]) {
        for (var i = 0, l = this.fields.length; i < l; ++i) {
            if (this.fields[i].name == col) {
                this.fields[i].value = this.rawObj[0][col];
                this.fields[i].formatValue();
                break;
            }
        }
    }
};

Form.prototype.render = function (data) {
    //console.log(data);
    window.document.body.innerHTML = data;
};

Form.prototype.updateFields = function () {
    this.fields.sort(function (a, b) { return a.order - b.order; });
    //console.log(this.fields);
    for (var i = 0, l = this.fields.length; i < l; ++i) {
        this.fields[i].update();
    }
};

//========================================================

/*
* FIELD CLASS
* Models a single input (select, input, or textarea)
* formats and updates the input value when its value is set
*/
function Field(name, value) {
    this.name = name;
    this.format = getByName(name).attr("data-format") || getByName(name).attr("type") || "text";
    this.default = getByName(name).attr("data-default");
    this.order = parseFloat(getByName(name).attr("data-order")) || Number.MAX_VALUE;
    this.type = getByName(name).prop("tagName");
    this.value = value;
    this.connection = "";
    this.query = getByName(name).attr("data-query");
    this.list = getByName(name).attr("data-list");
    this.childNames = $("[data-order='" + (this.order + 0.1).toFixed(1) + "']").map(function () { return $(this).attr("name"); }).get();
    this.setChangeEvent();
    if (this.type == "SELECT") {
        this.getOptions();
    }
    //this.formatValue();
}

Field.prototype.formatValue = function () {
    //console.log("Original Value: " + this.value);
    var valid = true;
    if (this.value != undefined && this.value != null) {
        try {
            switch (this.format) {
                case "date":
                    this.value = (new Date(Date.parse(this.value + ""))).toISOString().split("T")[0];
                    break;
                case "time":
                    this.value = (new Date(Date.parse(this.value + ""))).toISOString().split("T")[1].replace("Z", "");
                    break;
                case "datetime":
                case "datetime-local":
                    this.value = (new Date(Date.parse(this.value + ""))).toISOString();
                    break;
                case "number":
                case "range":
                    this.value = parseFloat(this.value);
                    break;
                case "int":
                    this.value = parseInt(this.value);
                    break;
                case "float":
                    this.value = parseFloat(this.value);
                    break;
                case "bool":
                    this.value = Boolean(parseInt(this.value));
                    break;
                case "text":
                case "password":
                default:
                    break;
            }
        } catch (e) {
            valid = false;
            console.log(e.message + ", format: " + this.format + ", value: " + this.value);
        }
    } else {
        valid = false;
    }
    if (!valid) {
        console.log(this.value + " is not a valid value for the format " + this.format);
        if (this.default != undefined) {
            this.value = this.default;
        } else {
            this.value = "";
        }
    }
    //console.log("New value :" + this.value);
};

Field.prototype.update = function () {
    $("[name='" + this.name + "']").val(this.value).change();
    //console.log("set " + this.name + " to " + this.value);
};

Field.prototype.getOptions = function () {
    if (this.list != undefined) {
        var listObj = $.parseJSON(this.list);
        var list = listObj["list"];
        this.appendOptions(list);
    } else if (this.query != undefined) {
        //console.log(this.query);
        if (typeof (this.query) == "string") {
            this.query = $.parseJSON(this.query);
        }
        for (var i = 0, l = this.query.Params.length; i < l; ++i) {
            if (String(this.query.Params[i]).indexOf("$") != -1) {
                var thisParam = this.query.Params[i];
                var refName = thisParam.substring(1, thisParam.length - 1);
                //console.log(refName);
                var refVal = $("[name='" + refName + "']").val();
                this.query.Params[i] = refVal;
            }
        }
        //console.log(this.query.Params);
        this.connection = this.query.connection;
        ajaxPostJSONjQuery("scripts/php/Query.php", this, "appendOptions", false);
    }
};

Field.prototype.appendOptions = function (list) {
    //console.log(list);
    getByName(this.name).html("<option value=''>" + this.name + "</option>");
    for (var i = 0, l = list.length; i < l; ++i) {
        getByName(this.name).append("<option value='" + list[i] + "'>" + list[i] + "</option>");
    }
}

Field.prototype.setChangeEvent = function () {
    if (this.childNames != undefined) {
        for(var i = 0, l = this.childNames.length; i < l; ++i){
            getByName(this.name).change({ jqEl:  getByName(this.childNames[i]) },
                function (event) {
                    var el = event.data.jqEl;
                    (new Field(el.attr("name"), el.attr("value"))).getOptions();
                });
        }
    }
};


// GLOBAL FUNCTIONS

// get by name function must be implemented 
// (as opposed to an element property) to avoid
// circular references which are created when a DOM
// element is added to an objects properties
// an object with circular references cannot be
// parsed into JSON
function getByName (name) {
    return $("[name='" + name + "']");
}

function userOpen(formName) {
    var ID = prompt("Enter a form number");
    var thisForm = new Form(formName);
    thisForm.ID = ID;
    getByName(thisForm.name).attr("data-id", thisForm.ID);
    thisForm.open();
}

function userUpdate(formName){
    var thisForm = new Form(formName);
    thisForm.update();
}

function userSubmit(formName){
    var thisForm = new Form(formName);
    thisForm.submit();
}

/*
You'll notice that quite a few of my
comments are below the code they describe.
It's my personal preference to do something
and then explain why
*/

function ajaxPostjQuery(url, obj, successFunc, async, returnType) {
    $.ajax({
        url: url,
        async: async,
        type: "POST",
        dataType: returnType,
        //contentType: "application/json; charset=utf-8",
        data: { Object: JSON.stringify(obj) },
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                obj[successFunc](data);
                //calls function obj.successFunc(data)
                // this means that the success function must always be implemented
                // as a function of tthe passed boject
            }
        },
        error: function (xhr, status, error) {
            console.log(status + " : " + error);
        }
        //timeout: 2000
    });
}

function ajaxPostHTMLjQuery(url, obj, successFunc, async) {
    ajaxPostjQuery(url, obj, successFunc, async, "html");
}

function ajaxPostJSONjQuery(url, obj, successFunc, async) {
    ajaxPostjQuery(url, obj, successFunc, async, "json");
}


/* CONFIGURE */
/*
$(function () {
    if (!Modernizr.inputtypes.date) {
        $.datepicker.setDefaults({
            dateFormat: "yy-mm-dd"
        });
        $("input[type='date']").each(function () {
            $(this).datepicker();
        });
    }
    if (!Modernizr.inputtypes.color) {
        $("input[type='color']").each(function () {
            $(this).jPicker(
            {
                window:
                {
                    expandable: true
                }
            });
        });
    }
});*/

/* JUST USE OPERA */