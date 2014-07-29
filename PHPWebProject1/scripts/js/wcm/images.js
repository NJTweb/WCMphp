function readImage(input, inputName) {
    if (input.files && input.files[0]) {
        var FR = new FileReader();
        FR.onload = function (e) {
            $("[name='" + inputName + "']").val(e.target.result);
        };
        FR.readAsDataURL(input.files[0]);
    }
}