var http = require('http');
var url = require('url');
const format = require('./');

http.createServer(function(req, res){
    var params = url.parse(req.url, true).query;
    let info = format(params.dataA, params.dataB);
    res.write("{");
    res.write("\"dataA\": \"" + info['country'] + "\",");
    res.write("\"dataB\": \"" + info['area'] + "\",");
    res.write("\"country\": \"" + info['country_name'] + "\",");
    res.write("\"region\": \"" + info['region_name'] + "\",");
    res.write("\"city\": \"" + info['city_name'] + "\",");
    res.write("\"domain\": \"" + info['owner_domain'] + "\",");
    res.write("\"isp\": \"" + info['isp_domain'] + "\"");
    res.write("}");
    res.end();
}).listen(1602);
