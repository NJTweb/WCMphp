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
                successFunc(data);
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

// get by name function must be implemented 
// (as opposed to an element property) to avoid
// circular references which are created when a DOM
// element is added to an objects properties
// an object with circular references cannot be
// parsed into JSON
function getByName(name) {
    return $("[name='" + name + "']");
}
function ISODate(dateStr) {
    return (new Date(Date.parse(dateStr))).toISOString();
}

function render(data) {
    //console.log(data);
    window.document.body.innerHTML = data;
}

var DEV_MODE = true;

console.log("DEV MODE IS " + (DEV_MODE ? "On" : "Off"));