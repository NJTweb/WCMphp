function logError(xhr, status, error) {
    console.log(status + " : " + error);
    console.log(this);
}

function render(data) {
    //console.log(data);
    window.document.body.innerHTML = data;
}

function getByName(name) {
    return $("[name='" + name + "']");
}