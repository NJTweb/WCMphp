function concatDateTimes() {
    $("[name='DateTimeOccurred']").val($("#dateocc").val() + " " + $("#timeocc").val()).change();
    $("[name='DateTimeStarted']").val($("#datestart").val() + " " + $("#timestart").val()).change();
    $("[name='DateTimeCompleted']").val($("#datecomp").val() + " " + $("#timecomp").val()).change();
    //console.log($("[name='DateTimeOccurred']").val());
    //console.log($("[name='DateTimeStarted']").val());
    //console.log($("[name='DateTimeCompleted']").val());
}

function splitDateTimes() {
    var dtocc = $("[name='DateTimeOccurred']").val().split(" ");
    $("#dateocc").val(dtocc[0]);
    $("#timeocc").val(dtocc[1]);
    //console.log(dtocc[0] + " " + dtocc[1]);
    var dtstart = $("[name='DateTimeStarted']").val().split(" ");
    $("#datestart").val(dtstart[0]);
    $("#timestart").val(dtstart[1]);
    //console.log(dtstart[0] + " " + dtstart[1]);
    var dtcomp = $("[name='DateTimeCompleted']").val().split(" ");
    $("#datecomp").val(dtcomp[0]);
    $("#timecomp").val(dtcomp[1]);
    //console.log(dtcomp[0] + " " + dtcomp[1]);
}