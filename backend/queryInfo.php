<?php

include("getInfo.php");
include("getIP.php");

function sendHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

$ip = $_GET['ip'];
if ($ip) {
    if (!filter_var($ip, \FILTER_VALIDATE_IP)) {
        echo "Illegal IP format".PHP_EOL;
        exit;
    }
} else {
    $ip = getClientIp();
}

if ($_GET['cli'] == "true") {
    echo getIPInfo($ip, true);
} else {
    sendHeaders();
    echo getIPInfo($ip, false);
}

?>
