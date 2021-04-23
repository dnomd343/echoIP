$(document).ready(function() {
    $.get("/ip", function(data) {
        $("#ip_default").val(data);
        getInfo();
    });
    $("table").hide();
    $("button").click(function() {
        $("button").css({
            'border-color': '',
            'background-color': ''
        });
        $("button").text("Searching...");
        $("table").hide(1000);
        if (checkIP($("input").val()) == "ok") {
            getInfo();
        } else {
            errorIP();
        }
    });
});

$(document).keydown(function(event) {
    if (event.keyCode == 13) {
        $("button").click();
    }
});

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
        $("#as").text(data.as);
        $("#city").text(data.city);
        $("#region").text(data.region);
        $("#country").text(data.country);
        $("#timezone").text(data.timezone);
        var earthUri = "https://earth.google.com/web/@" + data.loc + ",0a,398836d,1y,0h,0t,0r";
        $("#loc").html('<a href="' + earthUri + '" target="_blank" title="On Google Earth">' + data.loc + '</a>');
        $("#isp").text(data.isp);
        $("#scope").text(data.scope);
        $("#detail").text(data.detail);
        draw(parseFloat(data.loc.split(',')[0]), parseFloat(data.loc.split(',')[1]));
    });
}

function errorIP() {
    $("button").text("Illegal IP");
    $("button").css({
        'border-color': '#ff406f',
        'background-color': '#ff406f'
    });
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

mapboxgl.accessToken = 'pk.eyJ1Ijoic2hldm9ua3VhbiIsImEiOiJja20yMjlnNDYybGg2Mm5zNW40eTNnNnUwIn0.6xj6sgjWvdQgT_7OQUy_Jg';

function draw(x, y) {
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [y, x],
        zoom: 3
    });

    var size = 100;

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

            // draw outer circle
            context.clearRect(0, 0, this.width, this.height);
            context.beginPath();
            context.arc(this.width / 2, this.height / 2, outerRadius, 0, Math.PI * 2);
            context.fillStyle = 'rgba(255, 200, 200,' + (1 - t) + ')';
            context.fill();

            // draw inner circle
            context.beginPath();
            context.arc(this.width / 2, this.height / 2, radius, 0, Math.PI * 2);
            context.fillStyle = 'rgba(255, 100, 100, 1)';
            context.strokeStyle = 'white';
            context.lineWidth = 2 + 4 * (1 - t);
            context.fill();
            context.stroke();

            // update this image's data with data from the canvas
            this.data = context.getImageData(0, 0, this.width, this.height).data;

            // keep the map repainting
            map.triggerRepaint();

            // return `true` to let the map know that the image was updated
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
