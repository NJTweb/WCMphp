function Query(name, params, fetchType, connection) {
    this.results = {};
    this.name = name;
    this.params = params;
    this.connection = connection;
    this.fetchType = fetchType;
    //fetchType can be "ASSOC" or "NUM"
}

Query.prototype.execute = function () {
    ajaxPostJSONjQuery("scripts/php/Query.php", this, "storeResult", true);
};

Query.prototype.toString = function (func) {
    // func should take a single object (result)
    // return a string
    var retStr = "";
    for (var i = 0, l = this.results.length; i < l; ++i) {
        retStr += func(this.results[i]);
    }
    return retStr;
};

Query.prototype.storeResult = function(data){
    this.results = data;
};