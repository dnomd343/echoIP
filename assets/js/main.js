var rangeSize = 300;
var shareX, shareY;
mapboxgl.accessToken = 'pk.eyJ1Ijoic2hldm9ua3VhbiIsImEiOiJja20yMjlnNDYybGg2Mm5zNW40eTNnNnUwIn0.6xj6sgjWvdQgT_7OQUy_Jg';

$(document).ready(function() {
    if (getQuery("ip") === null) {
        getInfo();
    } else {
        $("input").val(getQuery("ip"));
        getInfo();
    }
    $("table").hide();
    $("#share").hide();
    $("button").click(function() {
        $(this).css({
            'border-color': '',
            'background-color': ''
        });
        $(this).text("Searching...");
        $("table").hide(1000);
        $("input").val(trim($("input").val()));
        if ($("input").val() == '' || checkIP($("input").val()) == "ok") {
            getInfo();
        } else {
            errorIP();
        }
    });
    $("button").dblclick(function(event) {
        event.stopPropagation();
        getVersion();
    });
    $("input").dblclick(function(event) {
        event.stopPropagation();
    });
    $("table").dblclick(function(event) {
        event.stopPropagation();
    });
    $('#output').dblclick(function() {
        showQRCode();
    });
    $('#output').click(function() {
        hideQRCode();
    });
    $('#map').click(function() {
        hideQRCode();
    });
    $('body').mousemove(function() {
        checkRange();
    });
    $('body').mouseleave(function() {
        hideQRCode();
    });
});

$(document).keydown(function(event) {
    if (event.keyCode == 13) {
        $("button").focus();
    }
});

function checkRange() {
    var distanceX = Math.abs(event.pageX - shareX);
    var distanceY = Math.abs(event.pageY - shareY);
    if ((distanceX >= rangeSize) || (distanceY >= rangeSize)) {
        hideQRCode();
    }
}

function hideQRCode() {
    if (!$("#qrcode").is(':hidden')) {
        $("#share").hide(200);
    }
}

function showQRCode() {
    var ip = $("#ip").text();
    if (checkIP(ip) == 'ok') {
        var pageUri = "https://" + window.location.host + "/?ip=" + ip;
        $("#qrcode").attr("href", pageUri);
        $("#qrcode").empty();
        $('#qrcode').qrcode({
            width: 100,
            height: 100,
            text: pageUri
        });
        var shareLeft = event.pageX - 50;
        var shareTop = event.pageY + 15;
        if (shareLeft + 122 > $(document).width()) {
            shareLeft = $(document).width() - 122;
        }
        if (shareTop + 122 > $(document).height()) {
            shareTop = $(document).height() - 122;
        }
        shareX = shareLeft + 61;
        shareY = shareTop + 61;
        if ($("#qrcode").is(':hidden')) {
            $("#share").css('left', shareLeft);
            $("#share").css('top', shareTop);
            $("#share").show(200);
        } else {
            $("#share").hide(100, function() {
                $("#share").css('left', shareLeft);
                $("#share").css('top', shareTop);
                $("#share").show(150);
            });
        }
    }
}

function getInfo() {
    $.get("/info/" + $("input").val(), function(data) {
        console.log(data);
        if (data.status == "F") {
            errorIP();
            return;
        }
        if (!$("input").val()) {
            $("input").val(data.ip);
        }
        $("button").text("Search");
        $("table").show(1000);
        $("#ip").text(data.ip);
        data.city = (data.city == null) ? "Unknow" : data.city;
        data.region = (data.region == null) ? "Unknow" : data.region;
        data.country = (data.country == null) ? "Unknow" : data.country;
        data.timezone = (data.timezone == null) ? "Unknow" : data.timezone;
        data.isp = (data.isp == null) ? "Unknow" : data.isp;
        data.scope = (data.scope == null) ? "Unknow" : data.scope;
        data.detail = (data.detail == null || data.detail == ' ') ? "Unknow" : data.detail;
        $("#city").text(data.city);
        $("#region").text(data.region);
        $("#country").text(data.country);
        $("#timezone").text(data.timezone);
        $("#isp").text(data.isp);
        $("#scope").text(data.scope);
        $("#detail").text(data.detail);
        if (data.as == null) {
            $("#as").text("Unknow");
        } else {
            $("#as").text(data.as);
            var asUri = "https://bgpview.io/asn/" + data.as.substr(2);
            $("#as").html('<a href="' + asUri + '" target="_blank" title="AS information">' + data.as + '</a>');
        }
        if (data.loc == null) {
            $("#loc").text("Unknow");
            clear();
        } else {
            var earthUri = "https://earth.google.com/web/@" + data.loc + ",0a,398836d,1y,0h,0t,0r";
            $("#loc").html('<a href="' + earthUri + '" target="_blank" title="View on Google Earth">' + data.loc + '</a>');
            draw(parseFloat(data.loc.split(',')[0]), parseFloat(data.loc.split(',')[1]));
        }
    });
}

function getVersion() {
    $.get("/version", function(data) {
        console.log(data);
        data.qqwry = data.qqwry.slice(0, 4) + "-" + data.qqwry.slice(4, 6) + "-" + data.qqwry.slice(6, 8);
        data.ipip = data.ipip.slice(0, 4) + "-" + data.ipip.slice(4, 6) + "-" + data.ipip.slice(6, 8);
        var data_ver = "";
        data_ver += "echoIP: " + data.echoip + "\n";
        data_ver += "纯真数据库: " + data.qqwry + "\n";
        data_ver += "IPIP.net数据库: " + data.ipip;
        alert(data_ver);
    });
}

function trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

function errorIP() {
    $("button").text("Illegal IP");
    $("button").css({
        'border-color': '#ff406f',
        'background-color': '#ff406f'
    });
    $("input").focus();
}

function checkIP(ipStr) {
    if (ipStr === null) {
        return "error";
    }
    var regIPv4=/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/;
    var regIPv6=/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:)|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}(:[0-9A-Fa-f]{1,4}){1,2})|(([0-9A-Fa-f]{1,4}:){4}(:[0-9A-Fa-f]{1,4}){1,3})|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){1,4})|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){1,5})|([0-9A-Fa-f]{1,4}:(:[0-9A-Fa-f]{1,4}){1,6})|(:(:[0-9A-Fa-f]{1,4}){1,7})|(([0-9A-Fa-f]{1,4}:){6}(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|(([0-9A-Fa-f]{1,4}:){5}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|(([0-9A-Fa-f]{1,4}:){4}(:[0-9A-Fa-f]{1,4}){0,1}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|([0-9A-Fa-f]{1,4}:(:[0-9A-Fa-f]{1,4}){0,4}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3})|(:(:[0-9A-Fa-f]{1,4}){0,5}:(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])(\\.(\\d|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])){3}))$/;
    var V4 = ipStr.match(regIPv4);
    var V6 = ipStr.match(regIPv6);
    if (V4 === null && V6 === null) {
        return "error";
    } else {
        return "ok";
    }
}

function getQuery(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var result = window.location.search.substr(1).match(reg);
    if (result != null) {
        return unescape(result[2]);
    } else {
        return null;
    }
}

function clear() {
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [0, 0],
        zoom: 1
    });
    map.on('load', function() {
        console.log("reset map");
    });
};

function draw(x, y) {
    var size = 100;
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [y, x],
        zoom: 3
    });
    var pulsingDot = {
        width: size,
        height: size,
        data: new Uint8Array(size * size * 4),

        onAdd: function() {
            var canvas = document.createElement('canvas');
            canvas.width = this.width;
            canvas.height = this.height;
            this.context = canvas.getContext('2d');
        },

        render: function() {
            var duration = 1000;
            var t = (performance.now() % duration) / duration;
            var radius = size / 2 * 0.3;
            var outerRadius = size / 2 * 0.7 * t + radius;
            var context = this.context;

            context.clearRect(0, 0, this.width, this.height);
            context.beginPath();
            context.arc(this.width / 2, this.height / 2, outerRadius, 0, Math.PI * 2);
            context.fillStyle = 'rgba(255, 200, 200,' + (1 - t) + ')';
            context.fill();

            context.beginPath();
            context.arc(this.width / 2, this.height / 2, radius, 0, Math.PI * 2);
            context.fillStyle = 'rgba(255, 100, 100, 1)';
            context.strokeStyle = 'white';
            context.lineWidth = 2 + 4 * (1 - t);
            context.fill();
            context.stroke();

            this.data = context.getImageData(0, 0, this.width, this.height).data;
            map.triggerRepaint();
            return true;
        }
    };

    map.on('load', function() {
        map.addImage('pulsing-dot', pulsingDot, { pixelRatio: 2 });
        map.addLayer({
            "id": "points",
            "type": "symbol",
            "source": {
                "type": "geojson",
                "data": {
                    "type": "FeatureCollection",
                    "features": [{
                        "type": "Feature",
                        "geometry": {
                            "type": "Point",
                            "coordinates": [y, x]
                        }
                    }]
                }
            },
            "layout": {
                "icon-image": "pulsing-dot"
            }
        });
    });
};
