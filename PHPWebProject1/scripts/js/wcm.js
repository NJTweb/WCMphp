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
                // calls function obj.successFunc(data)
                // this means that the successFunc (string) 
                // must always be implemented
                // as a function of obj
                obj[successFunc](data);
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