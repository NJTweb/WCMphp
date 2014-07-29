function changeImage(input, imgEl){
	var isBlank = (input.val() == "");
	imgEl.attr("src", (isBlank ? "res/upload.png" : "res/uploadgreen.png"));
	console.log("src att changed to " + (isBlank ? "res/upload.png" : "res/uploadgreen.png"));
}
