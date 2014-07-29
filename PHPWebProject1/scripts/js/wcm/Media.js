function mobileView() {
    if (window.innerWidth <= 799) {
        var tds = document.getElementsByTagName("td");
        for (var i = 0; i < tds.length; ++i) {
            var isHeader = true;
            var headerKeywords = ["required", "h1", "h2", "submit"];
            for (var j = 0; j < headerKeywords.length; ++j) {
                isHeader = isHeader && tds[i].innerHTML.indexOf(headerKeywords[j]) == -1;
            }
            if (isHeader) {
                tds[i].style.display = "none";
            }
        }
    }
}