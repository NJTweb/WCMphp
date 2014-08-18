$(document).ready(
function () {
    $("input, textarea, select").filter("[name]").each(
        function () {
            $(this).change(
                { jqEl: $(this) },
                function (event) {
                    var f = new Form();
                    f.updateField(event.data.jqEl);
                });
        });
    $("select").filter("[name]").each(
        function () {
            $(this).change(
                function () {
                    var f = new Form();
                    f.getNewOptions();
                });
        });
    $("input[type='file']").each(
        function () {
            $(this).change(
                { el: this },
                function (event) {
                    console.log("event called");
                    var reader = new FileReader();
                    reader.addEventListener("loadend", function () {
                        $(event.target).nextAll("input[class='fileURL']").first().val(reader.result).change();
                    });
                    reader.readAsDataURL(event.data.el.files[0]);

                });
        });
});

function Pipe() {
}

Pipe.prototype.render = function (data, ctx) {
    // set context to element before calling ajax
    // so that 'this' refers to the correct element
    $(ctx).html(data);
}

Pipe.prototype.executeCode = function (data) {
    //clean up data
    eval(data);
}
Pipe.prototype.ajaxPOSTPipe = function (data, success, ctx) {
    $.ajax({
        url: "scripts/php/Form.php", //find a way to set this programmatically
        type: "POST",
        context: ctx,
        dataType: "text",
        data: {PHP: data },
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                console.log(data);
                success(data, ctx);
            }
        },
        error: logError
    });
}

Pipe.prototype.sendPHP = function (php, type, ctx) {
    switch (type) {
        case "execute":
            var success = this.executeCode;
            break;
        case "render":
            var success = this.render;
            break;
        case "doNothing":
            var success = function (data, ctx) { };
            break;
    }
    this.ajaxPOSTPipe(php, success, ctx);
}

function logError(xhr, status, error) {
    console.log(status + " : " + error);
    console.log(this);
}

function Form() {
}

Form.prototype = Pipe.prototype;

Form.prototype.submit = function () {
    var PHP = 
"$f = Form::revive(); \
$f->submit();"
    this.sendPHP(PHP, "render", document.body);
};

Form.prototype.update = function () {
    var PHP =
"$f = Form::revive(); \
$f->update();"
    this.sendPHP(PHP, "render", document.body);
};

Form.prototype.open = function () {
    var id = prompt("Enter the id of the form you'd like to open");
    var noParamsPath = getNoParamsURL();
    var newURL = noParamsPath + "?id=" + id;
    location.href = newURL;
};

Form.prototype.updateField = function (jqEl) {
    var PHP =
"$f = Form::revive(); \
$f->update_fields(array('" + jqEl.attr("name") + "' => '" + jqEl.val() + "'));"
    this.sendPHP(PHP, "execute", this);
};

Form.prototype.getNewOptions = function () {
    var PHP = 
"$f = Form::revive(); \
$f->update_field_options(true);"
    this.sendPHP(PHP, "execute", this);
};

Form.prototype.updateOptions = function (jqEl, optionsHTML) {
    jqEl.html(optionsHTML);
};

Form.prototype.sendFile = function (file) {//formData){
    console.log(file);
    $.ajax({
        url: "scripts/php/Form.php", //find a way to set this programmatically
        type: "POST",
        dataType: "text",
        data: file,
        processData: false,
        //contentType: false,
        success: function (data, status, xhr) {
            if (status == "success" && xhr.readyState == 4) {
                console.log(data);
            }
        },
        error: logError
    });
};

function getNoParamsURL() {
    return location.protocol + '//' + location.host + location.pathname;
}

/*
This doesn't work correctly if the sketch area has a size set manually
i.e. in CSS or some other attribute
*/

function drawSketch() {
    var sketchArea = $("#sketchArea");
    var sketchCtx = sketchArea[0].getContext("2d");

    var off = sketchArea.offset();

    var relX = (mouse.x - off.left);
    var relY = (mouse.y - off.top);

    sketchCtx.fillStyle = $("#colorPicker").val();
    if (mouse.left) {
        sketchCtx.fillRect(relX, relY, $("#brushSize").val(), $("#brushSize").val());
    }
    //console.log($("#colorPicker").val());
    //console.log("X: " + relX + ", Y: " + relY);
}

function showSketch(sketchURL) {
    var sketchArea = $("#sketchArea");
    var sketchCtx = sketchArea[0].getContext("2d");

    var img = new Image();
    img.src = sketchURL;
    img.onload = function () {
        sketchCtx.drawImage(img, 0, 0);
    }
}

function clearSketch() {
    var sketchArea = $("#sketchArea");
    var sketchCtx = sketchArea[0].getContext("2d");
    sketchCtx.clearRect(0, 0, sketchArea.width(), sketchArea.height());
}

function saveSketch() {
    var dataURL = $("#sketchArea")[0].toDataURL();
    $("#sketchUrl").val(dataURL);
}

var mouse = { x: 0, y: 0, left: false };

document.addEventListener('mousemove', function (e) {
    mouse.x = e.pageX || e.clientX;
    mouse.y = e.pageY || e.clientY;
}, false);

document.addEventListener('mousedown', function (e) {
    mouse.left = (e.button === 0);
}, false);

document.addEventListener('mouseup', function (e) {
    mouse.left = false;
}, false);