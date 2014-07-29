function Query(name, params, fetchType, connection) {
    this.results = {};
    this.query = {
        Query: name,
        Params: params
    };
    this.connection = connection;
    this.fetchType = fetchType;
    //fetchType can be "ASSOC" or "NUM"
}

Query.prototype.execute = function () {
    ajaxPostJSONjQuery("scripts/php/Query.php", this, this.storeResult.bind(this), true);
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