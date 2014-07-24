function updateEmailBody() {
    if ($("[name='RepairedLocation']").val() == "Mound") {
        var bodyHTML = "<br>";
        bodyHTML += "Tool Name: " + $("[name='ToolName']").val() + "<br>";
        bodyHTML += "Tool Number: " + $("[name='ToolNumber']").val() + "<br>";
        bodyHTML += "Problems: " + $("[name='Problems']").val() + "<br>";
        bodyHTML += "Pick Up Date: " + $("[name='DateShipped']").val() + "<br>";
        bodyHTML += "Due Date: " + $("[name='DateReceived']").val() + "<br>";
        var bodyInput = $("[name='FormData[EmailBody]']");
        bodyInput.val(bodyHTML);
    }
    return true;
}

function updateEmailContacts() {
    var defaultContacts = "jmurphy@venturecorporation.net; pillars@ventureglobalengineering.com; thomason@mayco-mi.com; dharper@mayco-mi.com; hooks@njt-na.com; mbommarito@mayco-mi.com";
    var foremen = new Array();
    foremen["Zone_1"] = "reese@mayco-mi.com; clay@mayco-mi.com; vernon@VNA1.onmicrosoft.com";
    foremen["Zone_2"] = "selliott@mayco-mi.com; green@njt-na.com; claes@mayco-mi.com";
    foremen["Zone_3"] = "fxake@mayco-mi.com; eweathers@mayco-mi.com; shah@mayco-mi.com";
    foremen["Zone_4"] = "haggerty@mayco-mi.com; sherbutte@njt-na.com; greer@mayco-mi.com";
    var zone = $("[name='Zone']").val();
    var foremenEmails = (foremen[zone] == undefined ? "" : "; " + foremen[zone]);
    $("[name='ToolRepair']").attr("data-contacts", defaultContacts + foremenEmails);
}