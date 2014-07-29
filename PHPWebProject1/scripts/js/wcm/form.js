/*
* FORM CLASS
* opens form into form element
* allows updating and submitting of form
* models an html form, adds functionality
* uses an array of Field objects to make changes
* initialized with form name. everything else
* comes from data-* properties set on the
* html form tag
*/
function Form(name) {
    this.rawObj = {};
    this.fields = [];
    this.DOMName = name;
    this.name = getByName(name).attr("data-name");
    this.table = getByName(name).attr("data-table");
    if (DEV_MODE) {
        this.table = "dev_" + this.table;
    }
    this.primaryKey = getByName(name).attr("data-primarykey");
    this.ID = getByName(name).attr("data-id");
    this.connection = getByName(name).attr("data-connection");
    this.emailBody = getByName(name).attr("data-email");
    this.contacts = getByName(name).attr("data-contacts");

    this.initializeFields();
}

Form.prototype.initializeFields = function () {
    var fieldNames = $("select, textarea, input").map(function () { return $(this).attr("name"); }).get();
    for (var i = 0, l = fieldNames.length; i < l; ++i) {
        this.fields.push(new Field(fieldNames[i], $("[name='" + fieldNames[i] + "']").val()));
        this.fields[i].formatValue();
    }
};

Form.prototype.open = function () {
    ajaxPostJSONjQuery("scripts/php/Open.php", this, this.setData.bind(this), true);
};

Form.prototype.update = function () {
    ajaxPostHTMLjQuery("scripts/php/Update.php", this, render, true);
};

Form.prototype.submit = function () {
    ajaxPostHTMLjQuery("scripts/php/Submit.php", this, render, true);
};

Form.prototype.getMaxID = function () {
    ajaxPostJSONjQuery("scripts/php/getMaxID.php", this, this.setMaxID.bind(this), true);
};

Form.prototype.setMaxID = function (data) {
    ++data[0];
    getByName(this.primaryKey).val(data[0]);
    getByName(this.DOMName).attr("data-id", data[0]);
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

Form.prototype.updateFields = function () {
    //sort the fields in the correct order before updating them
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
    try{
        this.name = name;
        this.value = value;
        this.format = getByName(name).attr("data-format") || getByName(name).attr("type") || "text";
        this.default = getByName(name).attr("data-default");
        this.order = parseFloat(getByName(name).attr("data-order")) || Number.MAX_VALUE;
        this.type = getByName(name).prop("tagName");
        this.query = getByName(name).attr("data-query");
        this.list = getByName(name).attr("data-list");
        this.connection = "";
        this.childNames = $("[data-order='" + (this.order + 0.1).toFixed(1) + "']").map(function () { return $(this).attr("name"); }).get();

        this.setChangeEvent();
        if (this.type == "SELECT") {
            this.getOptions();
        }
        //this.formatValue(); no longer formats value on initialization
    }catch(e){
        console.log(e.message);
        console.log("Error initializing " + name);
        console.log(getByName(name));
    }
}

Field.prototype.formatValue = function () {
    //console.log("Original Value: " + this.value);
    var valid = true;

    if (this.value != undefined && this.value != null && this.value != "") {
        try {
            this.value = String(conversions[this.format](this.value));
        } catch (e) {
            valid = false;
            console.log(e.message + ", format: " + this.format + ", value: " + this.value);
        }
    } else {
        valid = false;
    }
    if (!valid) {
        //console.log(this.value + " is not a valid value for the format " + this.format);
        //console.log(this.name + " default is " + getByName(this.name).attr("data-default"));
        if (this.default != undefined) {
            //console.log("Set to default: " + this.default);
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
        // check if the query has been parsed to a string yet
        if (typeof (this.query) == "string") {
            this.query = $.parseJSON(this.query);
        }
        for (var i = 0, l = this.query.Params.length; i < l; ++i) {
            //if the parameter is a reference (contains $)
            if (String(this.query.Params[i]).indexOf("$") != -1) {
                var thisParam = this.query.Params[i];
                //remove the $ from each side of the string
                var refName = thisParam.substring(1, thisParam.length - 1);
                //console.log(refName);
                var refVal = $("[name='" + refName + "']").val();
                this.query.Params[i] = refVal;
            }
        }
        //console.log(this.query.Params);
        // the object passed to these functions must have a connection
        // property defined, so we set it to the connection specified 
        // in the query object
        this.connection = this.query.connection;
        ajaxPostJSONjQuery("scripts/php/Query.php", this, this.appendOptions.bind(this), false);
    }
};

Field.prototype.appendOptions = function (list) {
    //console.log(list);
    getByName(this.name).html("<option value=''>" + this.name + "</option>");
    for (var i = 0, l = list.length; i < l; ++i) {
        getByName(this.name).append("<option value='" + list[i] + "'>" + list[i] + "</option>");
    }
};

// set the change event so that any dependent elements (+0.1 in the order)
// will update when this elements value changes
// i.e. if an element with data-order 1.0 is changed
// all elements with data-order 1.1 will update their values
Field.prototype.setChangeEvent = function () {
    if (this.childNames != undefined) {
        for (var i = 0, l = this.childNames.length; i < l; ++i) {
            getByName(this.name).change(
                { jqEl: getByName(this.childNames[i]) },
                function (event) {
                    var el = event.data.jqEl;
                    (new Field(el.attr("name"), el.attr("value"))).getOptions();
                }
            );
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

function userOpen(formName) {
    var ID = prompt("Enter a form number");
    var thisForm = new Form(formName);
    thisForm.ID = ID;
    getByName(thisForm.name).attr("data-id", thisForm.ID);
    thisForm.open();
}

function userUpdate(formName) {
    var thisForm = new Form(formName);
    thisForm.update();
}

function userSubmit(formName) {
    var thisForm = new Form(formName);
    thisForm.submit();
}

var conversions = {
    "date":             function (val) { return ISODate(val).split("T")[0]; },
    "time":             function (val) { return ISODate(val).split("T")[1].replace("Z", ""); },
    "datetime":         function (val) { return ISODate(val); },
    "datetime-local":   function (val) { return ISODate(val); },
    "number":           function (val) { return parseFloat(val); },
    "range":            function (val) { return parseFloat(val); },
    "int":              function (val) { return parseInt(val); },
    "float":            function (val) { return parseFloat(val); },
    "bool":             function (val) { return Boolean(parseInt(this.value)); },
    "text":             function (val) { return val; },
    "password":         function (val) { return val; },
    "hidden":           function (val) { return val; }
};