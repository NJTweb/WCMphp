function Issue(json) {
    var rawObj = $.parseJSON(json);
    for (var key in rawObj) {
        this[key] = rawObj[key];
    }
    this.connection = "Safety";
}

Issue.prototype.open = function () {
    this.query = {
        Query: "OpenIssue",
        Params: [this["Name"], this["ID"], this["LineNum"]]
    };
    ajaxPostHTMLjQuery("scripts/php/Query.php", this, "getissues", true);
};

Issue.prototype.close = function (action) {
    this.query = {
        Query: "CloseIssue",
        Params: [this["Name"], this["ID"], this["LineNum"], action]
    };
    ajaxPostHTMLjQuery("scripts/php/Query.php", this, "getissues", true);
};

Issue.prototype.toHTML = function (jqEl) {
    var thisHTML = "<div class='issue' data-json='" + JSON.stringify(this) + "' onclick='closeIssue($(this))'>";
    thisHTML += this["Name"] + " number " + this["ID"] + " on line " + this["LineNum"] + " of type " + this["Compliancy"] + " with severity level " + this["Severity"];
    thisHTML += "</div>";
    jqEl.append(thisHTML);
};

Issue.prototype.render = function (data) {
    console.log(data);
    //window.document.body.innerHTML = data;
};

Issue.prototype.getissues = function () {
    getAllIssues();
};

function getAllIssues() {
    console.log("Getting issues");

    var openIssuesQuery = {
        connection: "Safety",
        query: {
            Query: "GetOpenIssues",
            Params: []
        },
        fetchType: "ASSOC",
        dtoi: function (data) {
            dataToOpenIssues(data);
        }
    }
    ajaxPostJSONjQuery("scripts/php/Query.php", openIssuesQuery, "dtoi", true);

    var closedIssuesQuery = {
        connection: "Safety",
        query: {
            Query: "GetClosedIssues",
            Params: []
        },
        fetchType: "ASSOC",
        dtoi: function (data) {
            dataToClosedIssues(data);
        }
    }
    ajaxPostJSONjQuery("scripts/php/Query.php", closedIssuesQuery, "dtoi", true);
}

function dataToOpenIssues(data) {
    var jqEl = $("#open_issues_div");
    jqEl.html("");
    dataToIssues(data, jqEl);
}

function dataToClosedIssues(data) {
    var jqEl = $("#closed_issues_div");
    jqEl.html("");
    dataToIssues(data, jqEl);
}

function dataToIssues(data, jqEl) {
    data = filterIssues(data);
    sortIssues(data);
    for (var i = 0, l = data.length; i < l; ++i) {
        (new Issue(JSON.stringify(data[i]))).toHTML(jqEl);
    }
    console.log(data);
}

function filterIssues(data) {
    result = [];
    filter = getFilter();
    for (var i = 0, l = data.length; i < l; ++i) {
        var goodData = true;
        for (var key in filter) {
            goodData = goodData && (filter[key] == data[i][key] || filter[key] == undefined || filter[key] == "" || data[i][key] == undefined);
        }
        if (goodData) {
            result.push(data[i]);
        }
    }
    return result;
}

function sortIssues(data) {
    data.sort(
        function (a, b) {
            var nameSort = (a.Name < b.Name) ? -4 : (a.Name > b.Name) ? 4 : 0;
            var idSort = (a.ID - b.ID) < 0 ? -2 : (a.ID - b.ID) > 0 ? 2 : 0;
            var lineSort = (a.LineNum - b.LineNum) < 0 ? -1 : (a.LineNum - b.LineNum) > 0 ? 1 : 0;
            return nameSort + idSort + lineSort;
        }
    );
    return data;
}

function getFilter() {
    var filter = {
        Name: getByName("formname").val(),
        Department: getByName("departments").val(),
        Zone: getByName("zones").val(),
        Machine: getByName("machines").val(),
        Supervisor: getByName("supervisors").val(),
        Shift: getByName("shifts").val()
    }
    return filter;
}

function closeIssue(jqEl) {
    var issue = new Issue(jqEl.attr("data-json"));
    var action = prompt("Enter the action taken to close this issue");
    if (action != "" && action != undefined) {
        issue.close(action);
    }
}

function openIssue(jqEl) {
    var issue = new Issue(jqEl.attr("data-json"));
    issue.open();
}

function filterForm(jqEl, form) {
    getByName('formname').val(form);
    $("#left_nav ul li").not(jqEl).each(
        function () {
            $(this).attr("class", "");
            console.log($(this));
        }
    );
    jqEl.attr("class", "activated");
    getAllIssues();
}